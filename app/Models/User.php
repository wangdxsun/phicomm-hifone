<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Models;

use Auth;
use AltThree\Validator\ValidatingTrait;
use Cmgmyr\Messenger\Traits\Messagable;
use Hifone\Models\Traits\SearchTrait;
use Hifone\Presenters\UserPresenter;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use McCool\LaravelAutoPresenter\HasPresenter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract, HasPresenter
{
    use Authenticatable, CanResetPassword, EntrustUserTrait, ValidatingTrait, Messagable, SearchTrait;

    // Enable hasRole( $name ), can( $permission ),
    //   and ability($roles, $permissions, $options)

    // Enable soft delete

    protected $dates = ['deleted_at', 'last_op_time'];

    /**
     * The properties that cannot be mass assigned.
     *
     * @var string[]
     */
    protected $guarded = ['id', 'notifications', 'is_banned'];

    /**
     * The hidden properties.
     *
     * These are excluded when we are serializing the model.
     *
     * @var string[]
     */
    protected $hidden = ['password', 'remember_token'];
    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'username' => ['required', 'max:15', 'regex:/\A[\x{4e00}-\x{9fa5}A-Za-z0-9\-\_\.]+\z/u'],
        'password' => 'required|string|min:6',
    ];

    protected $searchable = [
        'username',
    ];

    /**
     * Find by username, or throw an exception.
     *
     * @param string $username The username.
     * @param mixed  $columns The columns to return.
     *
     * @throws ModelNotFoundException if no matching User exists.
     *
     * @return User
     */
    public static function findByUsernameOrFail($username, $columns = ['*'])
    {
        if (!is_null($user = static::whereUsername($username)->first($columns))) {
            return $user;
        }

        throw new NotFoundHttpException;
    }

    public static function findUserByPhicommId($phicommId)
    {
        return static::where('phicomm_id', $phicommId)->first();
    }

    public function favoriteThreads()
    {
        return $this->belongsToMany(Thread::class, 'favorites')->withTimestamps();
    }

    /**
     * Users can have many threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Users can have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Users can have many credits.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    public function followers()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function follows()
    {
        return $this->hasMany(Follow::class);
    }

    public function identities()
    {
        return $this->hasMany(Identity::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * ----------------------------------------
     * UserInterface
     * ----------------------------------------.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * ----------------------------------------
     * RemindableInterface
     * ----------------------------------------.
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function getAvatarAttribute()
    {
        return $this->attributes['avatar_url'] ?: '/images/discuz_big.gif';
    }

    public function getAvatarSmallAttribute()
    {
        return $this->attributes['avatar_url'] ?: '/images/discuz_small.gif';
    }

    public function getAvatarUrlAttribute($value)
    {
        return $value ?: '/images/discuz_big.gif';
    }

    /**
     * Cache github avatar to local.
     */
    public function cacheAvatar()
    {
        //Download Image
        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->get($this->image_url);

        //Get ext
        $content_type = explode('/', $response->getHeader('Content-Type'));
        $ext = array_pop($content_type);

        $avatar_name = $this->id . '_' . time() . '.' . $ext;
        $save_path = public_path('uploads/avatars/') . $avatar_name;

        //Save File
        $content = $response->getBody()->getContents();
        file_put_contents($save_path, $content);

        //Delete old file
        if ($this->avatar) {
            @unlink(public_path('uploads/avatars/') . $this->avatar);
        }

        //Save to database
        $this->avatar = $avatar_name;
        $this->save();
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return UserPresenter::class;
    }

    public function getUrlAttribute()
    {
        return route('user.home', $this->username);
    }

    public function getRoleIdAttribute()
    {
        return implode(',', array_column($this->roles->toArray(), 'id'));
    }

    public function setRoleIdAttribute($roleId)
    {
        if ($roleId == 0) {
            $this->roles()->detach();
        } else {
            $this->roles()->sync([$roleId]);
        }
    }

    public function getRoleAttribute()
    {
        $adminGroup = implode(',', array_column($this->roles->toArray(), 'display_name'));
        if ($adminGroup) {
            return $adminGroup;
        }
        $userGroup = '未知用户组';
        $groups = Role::userGroup()->orderBy('credit_low')->get();
        foreach ($groups as $group) {
            if ($this->score >= $group->credit_low && $this->score <= $group->credit_high) {
                $userGroup = $group->display_name;
            }
        }
        return $userGroup;
    }

    public function getCommentAttribute()
    {
        return $this->role_id == Role::NO_COMMENT ? 'fa fa-comment text-danger' : 'fa fa-comment';
    }

    public function getLoginAttribute()
    {
        return $this->role_id == Role::NO_LOGIN ? 'fa fa-sign-in text-danger' : 'fa fa-sign-in';
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function hasFollowThread($thread)
    {
        return $thread->follows()->forUser($this->id)->count() > 0;
    }

    public function isFavoriteThread($thread)
    {
        return $thread->favorites()->forUser($this->id)->count() > 0;
    }

    public function hasFollowUser(User $user)
    {
        return $this->follows()->ofType(User::class)->ofId($user->id)->count() > 0;
    }

    public function hasLikeThread(Thread $thread)
    {
        return $this->likes()->ofType(Thread::class)->ofId($thread->id)->count() > 0;
    }

    public function hasLikeReply(Reply $reply)
    {
        return $this->likes()->ofType(Reply::class)->ofId($reply->id)->count() > 0;
    }

    public function hasReportThread(Thread $thread)
    {
        return $this->reports()->ofType(Thread::class)->ofId($thread->id)->count() > 0;
    }

    public function hasReportReply(Reply $reply)
    {
        return $this->reports()->ofType(Reply::class)->ofId($reply->id)->count() > 0;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
