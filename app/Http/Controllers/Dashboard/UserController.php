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
use Hifone\Models\Moderator;
use Hifone\Models\PraModerator;
use Hifone\Models\Role;
use Hifone\Models\Tag;
use Hifone\Models\TagType;
use Hifone\Models\User;
use Redirect;
use View;
use Input;

class UserController extends Controller
{
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
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('user'));
        //标签个数，用于筛选
        $tagCount = Input::get('tag');
        if (array_key_exists('tags', $search)){
            $search['tags'] = explode(',', $search['tags']);
        }

        if (array_get($tagCount, 'tagCount')) {
            if( count(array_get($search, 'tags')) > $tagCount['tagCount'] ) {
                return Redirect::route('dashboard.user.index')->withErrors('具体的标签不能大于选择的标签个数');
            } else {
                $users = User::has('tags', '=', $tagCount['tagCount'])->search($search)->with(['roles', 'lastOpUser', 'tags'])->paginate(20);
            }
        } else {
            $users = User::search($search)->with(['roles', 'lastOpUser', 'tags'])->paginate(20);
        }
        $orderTypes = User::$orderTypes;
        //传入所有用户标签类
        $userTagTypes = TagType::ofType([TagType::USER, TagType::AUTO])->with('tags')->get();
        //传入所有用户标签类，排除自动标签
        $userTagTypesExceptAuto = TagType::ofType([TagType::USER])->with('tags')->get();
        //传入标签个数的数组

        $tagCounts = range(1, Tag::whereIn('tag_type_id', $userTagTypes->pluck('id')->toArray())->count());

        return View::make('dashboard.users.index')
            ->withPageTitle(trans('dashboard.users.users').' - '.trans('dashboard.dashboard'))
            ->withUsers($users)
            ->with('orderTypes',$orderTypes)
            ->with('userTagTypes', $userTagTypes)
            ->with('userTagTypesExceptAuto', $userTagTypesExceptAuto)
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
        return View::make('dashboard.users.create_edit')
            ->withRoles($roles)
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
        if (User::where('username', $userData['username'])->count() > 0) {
            return back()->withErrors('昵称已存在');
        }
        try {
            \DB::transaction(function () use ($userData, $roleId) {
                //创建用户密码
                if (array_get($userData, 'password')) {
                    $userData['salt'] = str_random(8);
                    $userData['password'] = $this->hashPassword($userData['password'], $userData['salt']);
                }

                $user = User::create($userData);
                $this->updateOpLog($user, '创建用户');
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

        return View::make('dashboard.users.create_edit')
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'))
            ->withUser($user)
            ->withRoles($roles);
    }

    public function update(User $user)
    {
        $userData = Input::get('user');
        $roleId = Input::get('roleId');
        try {
            \DB::transaction(function () use ($user, $userData, $roleId) {
                //修改用户密码，如果未设置则跳过
                if (array_get($userData, 'password')) {
                    $userData['salt'] = $user->salt;
                    $userData['password'] = $this->hashPassword($userData['password'], $userData['salt']);
                } else {
                    unset($userData['password']);
                }
                $user->update($userData);

                $oldRole = $user->roles()->first();
                $newRole = Role::find($roleId);
                if ($oldRole && ($oldRole->name == 'NodePraMaster' || $oldRole->name == 'NodeMaster')) {
                    //从版主、实习版主改成非版主
                    if (empty($newRole) || $newRole->name <> 'NodeMaster' || $newRole <> 'NodePraMaster') {
                        $user->moderators()->detach();
                        $user->praModerators()->detach();
                    } elseif ($oldRole->name == 'NodeMaster' && $newRole->name == 'NodePraMaster' ) {
                        //从版主改成实习版主
                        $user->praModerators()->attach($user->moderators->pluck('id'));
                        $user->moderators()->detach();
                    } elseif ($oldRole->name == 'NodePraMaster' && $newRole->name == 'NodeMaster' ) {
                        //从实习版主改成版主
                        $user->moderators()->attach($user->praModerators->pluck('id'));
                        $user->praModerators()->detach();
                    }
                }

                $user->role_id = $roleId;
                $this->updateOpLog($user, '修改用户信息');
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

        return Redirect::back()->withSuccess('头像删除成功');
    }

    //禁言或取消禁言
    public function comment(User $user)
    {
        $user->role_id = ($user->role_id == Role::NO_COMMENT) ? Role::USER : Role::NO_COMMENT;
        $this->updateOpLog($user, $user->role_id ? '取消禁言' : '禁言');
        return Redirect::back()->withSuccess('修改成功');
    }

    //禁止登录或者取消登录
    public function login(User $user)
    {
        $user->role_id = ($user->role_id == Role::NO_LOGIN) ? Role::USER : Role::NO_LOGIN;
        $this->updateOpLog($user, $user->role_id ? '取消禁止登录' : '禁止登录');
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

    //编辑用户标签
    public function tagUpdate(User $user)
    {
        $tagData = Input::get('userTags');
        $autoTags = $user->tags()->ofChannel(TAG::AUTO)->get()->pluck('id')->toArray();
        $user->tags()->sync(array_merge($tagData, $autoTags));
    }

}
