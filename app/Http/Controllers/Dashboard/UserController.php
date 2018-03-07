<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use AltThree\Validator\ValidationException;
use Hifone\Hashing\PasswordHasher;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Role;
use Hifone\Models\Tag;
use Hifone\Models\TagType;
use Hifone\Models\User;
use Redirect;
use View;
use Input;

class UserController extends Controller
{
    const THREAD = 0;
    const USER = 1;
    protected $hasher;

    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;

        View::share([
            'current_menu'  => 'users',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('user'));
        $tagCount = Input::get('tag');
        if (array_key_exists('tags', $search)){
            $search['tags'] = explode(',', $search['tags']);
        }

        if ($tagCount['tagCount'] != "" ) {
            $users = User::has('tags', '=', $tagCount['tagCount'])->search($search)->with('roles', 'lastOpUser')->paginate(20);
        } else {
            $users = User::search($search)->with('roles', 'lastOpUser')->paginate(20);
        }
        $roles = Role::all();
        $orderTypes = User::$orderTypes;
        //传入所有用户标签类
        $userTagTypes = TagType::ofType(UserController::USER)->with('tags')->get();
        $tagCounts = range(1, count(Tag::whereIn('type', TagType::ofType(TagType::USER)->pluck('id')->toArray())->get()));

        return View::make('dashboard.users.index')
            ->withPageTitle(trans('dashboard.users.users').' - '.trans('dashboard.dashboard'))
            ->withUsers($users)
            ->with('orderTypes',$orderTypes)
            ->withRoles($roles)
            ->with('userTagTypes', $userTagTypes)
            ->withSearch($search)
            ->with('tagCounts', $tagCounts)
            ->withAllUsers([]);
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::adminGroup()->get();
        //传入所有用户标签类
        $userTagTypes = TagType::ofType(UserController::USER)->with('tags')->get();
        return View::make('dashboard.users.create_edit')
            ->withRoles($roles)
            ->with('userTagTypes', $userTagTypes)
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'));
    }

    /**
     * Stores a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $userData = Input::get('user');
        $roleId = Input::get('roleId');
        $tagData = explode(',',Input::get('userTags'));
        if (User::where('username', $userData['username'])->count() > 0) {
            return back()->withErrors('昵称已存在');
        }
        try {
            \DB::transaction(function () use ($userData, $roleId, $tagData) {
                //创建用户密码
                if (array_get($userData, 'password')) {
                    $userData['salt'] = str_random(8);
                    $userData['password'] = $this->hashPassword($userData['password'], $userData['salt']);
                }

                $user = User::create($userData);
                $user->tags()->sync($tagData);
                $this->updateOpLog($user, '创建用户');
                $user->addToIndex();
            });
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput($userData)
                ->withTitle('用户添加失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.user.index')
            ->withSuccess('用户添加成功');
    }

    public function edit(User $user)
    {
        $roles = Role::adminGroup()->get();
        //查询出用户的标签
        $userTags = $user->tags->pluck('id');
        //传入所有用户标签类
        $userTagTypes = TagType::ofType(UserController::USER)->with('tags')->get();

        return View::make('dashboard.users.create_edit')
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'))
            ->with('userTags', json_encode($userTags->toArray()))
            ->with('userTagTypes', $userTagTypes)
            ->withUser($user)
            ->withRoles($roles);
    }

    public function update(User $user)
    {
        $userData = Input::get('user');
        $tagData = explode(',', Input::get('userTags'));
        $roleId = Input::get('roleId');

        try {
            \DB::transaction(function () use ($user, $userData, $roleId, $tagData) {
                //修改用户密码，如果未设置则跳过
                if (array_get($userData, 'password')) {
                    $userData['salt'] = $user->salt;
                    $userData['password'] = $this->hashPassword($userData['password'], $userData['salt']);
                } else {
                    unset($userData['password']);
                }
                $user->update($userData);
                $user->tags()->sync($tagData);
                $user->role_id = $roleId;
                $this->updateOpLog($user, '修改用户信息');
                $user->updateIndex();
            });
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput(Input::except('password'))
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), '用户修改失败'))
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.user.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.edit.success')));
    }

    //恢复默认头像
    public function avatar(User $user)
    {
        $user->avatar_url = '';
        $this->updateOpLog($user, '恢复默认头像');
        $user->updateIndex();

        return Redirect::back()->withSuccess('头像删除成功');
    }

    //禁言或取消禁言
    public function comment(User $user)
    {
        $user->role_id = ($user->role_id == Role::NO_COMMENT) ? Role::REGISTER_USER : Role::NO_COMMENT;
        $this->updateOpLog($user, $user->role_id ? '取消禁言' : '禁言');
        $user->updateIndex();
        return Redirect::back()->withSuccess('修改成功');
    }

    //禁止登录或者取消登录
    public function login(User $user)
    {
        $user->role_id = ($user->role_id == Role::NO_LOGIN) ? Role::REGISTER_USER : Role::NO_LOGIN;
        $this->updateOpLog($user, $user->role_id ? '取消禁止登录' : '禁止登录');
        $user->updateIndex();
        return Redirect::back()->withSuccess('修改成功');
    }

    /**
     * hash user's raw password.
     *
     * @param string $password plain text form of user's password
     * @param string $salt     salt
     *
     * @return string hashed password
     */
    private function hashPassword($password, $salt)
    {
        return $this->hasher->make($password, ['salt' => $salt]);
    }

}
