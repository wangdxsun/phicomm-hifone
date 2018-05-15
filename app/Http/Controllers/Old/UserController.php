<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers;

use AltThree\Validator\ValidationException;
use Auth;
use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Exceptions\HifoneException;
use Hifone\Hashing\PasswordHasher;
use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Http\Bll\UserBll;
use Hifone\Models\Identity;
use Hifone\Models\Location;
use Hifone\Models\Provider;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Events\Image\AvatarWasUploadedEvent;
use Input;
use Intervention\Image\ImageManagerStatic as Image;
use Redirect;

class UserController extends Controller
{
    protected $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
        $this->middleware('auth', ['only' => ['edit', 'update', 'destroy', 'unbind']]);
        $this->middleware('active:web',['only' => ['show']]);
    }

    public function index()
    {
        $users = User::recent()->take(48)->get();

        return $this->view('users.index')
            ->withUsers($users);
    }

    public function show(User $user, FollowBll $followBll)
    {
        $threads = Thread::forUser($user->id)->recent()->limit(10)->get();
        $replies = Reply::forUser($user->id)->recent()->limit(10)->get();
        $followers = $followBll->followers($user);
        $follows = $followBll->follows($user);

        return $this->view('users.show')
            ->withUser($user)
            ->withThreads($threads)
            ->withReplies($replies)
            ->withFollowers($followers)
            ->withFollows($follows);
    }

    public function showByUsername($username, FollowBll $followBll, UserBll $userBll)
    {
        return $this->show($username, $followBll,$userBll);
    }

    public function edit(User $user)
    {
        $this->needAuthorOrAdminPermission($user->id);
        $providers = Provider::recent()->get();
        $ids = $user->identities()->pluck('provider_id')->all();

        return $this->view('users.edit')
            ->withProviders($providers)
            ->withTab(Input::get('tab'))
            ->withBindOauthIds($ids)
            ->withUser($user);
    }

    public function update(User $user)
    {
        $this->needAuthorOrAdminPermission($user->id);
        $data = Input::only('nickname', 'company', 'website', 'signature', 'bio', 'location');
        try {
            $user->update($data);
        } catch (ValidationException $e) {
            return Redirect::route('user.edit')
                ->withInput(Input::all())
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('user.edit', $user->id)
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function destroy(User $user)
    {
        $this->needAuthorOrAdminPermission($user->id);
    }

    public function replies(User $user)
    {
        $replies = Reply::forUser($user->id)->visible()->recent()->paginate(15);

        return $this->view('users.replies')
            ->withUser($user)
            ->withReplies($replies);
    }

    public function threads(User $user)
    {
        //web端查看自己或管理员查看帖子，包括自己未审核通过的贴子
        if (Auth::check() && ($user->id == Auth::id() || Auth::user()->can('view_thread'))) {
            $threads = $user->threads()->recent()->paginate(15);
        } else {
            $threads = $user->threads()->visible()->recent()->paginate(15);
        }

        return $this->view('users.threads')
            ->withUser($user)
            ->withThreads($threads);
    }

    public function favorites(User $user)
    {
        $threads = $user->favoriteThreads()->paginate(15);

        return $this->view('users.favorites')
            ->withUser($user)
            ->withThreads($threads);
    }

    public function credits(User $user)
    {
        $credits = $user->credits()->paginate(15);

        return $this->view('users.credits')
            ->withUser($user)
            ->withCredits($credits);
    }

    public function follows(User $user)
    {
        $follows = $user->follows()->ofType(User::class)->with('follower')->get();

        return $this->view('users.follows')->withUser($user)->withFollows($follows);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->with('follower')->get();

        return $this->view('users.followers')->withUser($user)->withFollowers($followers);
    }

    public function city($name)
    {
        $location = Location::where('name', $name)->firstOrFail();
        $users = $location->users()->paginate(15);

        return $this->view('users.city')
            ->withLocation($location)
            ->withUsers($users);
    }

    public function blocking(User $user)
    {
        $user->is_banned > 0 ? $user->decrement('is_banned') : $user->increment('is_banned');

        return Redirect::route('user.home', $user->username);
    }

    public function unbind(User $user)
    {
        $this->needAuthorOrAdminPermission($user->id);
        $record = Identity::where('user_id', '=', $user->id)->where('provider_id', '=', Input::get('provider_id'))->first();

        $record ? $record->delete() : null;

        return Redirect::route('user.edit', $user->id)
            ->withSuccess(trans('hifone.login.oauth.unbound_success'));
    }

    public function avatarupdate(PhicommBll $phicommBll)
    {
        if (!request()->hasFile('avatar')) {
            throw new HifoneException('没有上传图片');
        }
        $avatar = dispatch(new UploadImageCommand(request()->file('avatar')));
        Auth::user()->update(['avatar_url' => $avatar['filename']]);
        if (Auth::phicommId()) {
            $phicommBll->upload($avatar['localFile']);
        }
        event(new AvatarWasUploadedEvent(Auth::user()));

        return Redirect::back()
            ->withSuccess(trans('hifone.users.avatar_upload_success'));
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

    protected function resetPassword()
    {
        $user = Auth::user();
        if ($this->hashPassword(Input::get('old_password'),$user->salt) == $user->password) {
            $password = Input::get('password');

            $password_confirmation = Input::get('password_confirmation');
            if (!($password == $password_confirmation)) {
                return Redirect::back()
                    ->withInfo('当前输入新密码与确认密码不一致, 请重新输入.');
            } else {
                $user->password = $this->hashPassword($password,$user->salt);
                $user->save();
                return Redirect::back()
                    ->withInfo('密码修改成功!');
            }
        } else {
            return Redirect::back()
                ->withInfo('当前密码输入错误, 请重新输入.');
        }
    }
}