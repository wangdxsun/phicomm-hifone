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
use Elasticquent\ElasticquentTrait;
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
    use Authenticatable, CanResetPassword, EntrustUserTrait, ValidatingTrait, Messagable, SearchTrait, ElasticquentTrait;

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
    protected $hidden = ['password', 'remember_token', 'salt', 'refresh_token', 'is_banned', 'image_url', 'location', 'location_id', 'bio',
        'website', 'company', 'signature', 'locale', 'regip', 'last_op_user_id', 'last_op_time', 'last_op_reason', 'last_visit_time',
        'created_at', 'updated_at', 'deleted_at', 'nickname', 'email', 'phicomm_id'];
    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'username' => 'required',
        'password' => 'required|string|min:6',
    ];

    protected $searchable = [
        'username',
    ];

    protected $mappingProperties = [
        'username' => [
            'type' => 'string',
            'analyzer' => 'standard'
        ],
        'score' => [
            'type' => 'integer'
        ]
    ];

    public static $orderTypes = [
        'id' => '用户ID',
        'thread_count' => '发帖数',
        'score'  => '经验值',
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

    public function favorites()
    {
        return $this->hasMany(Favorite::class)->orderBy('id', 'desc');
    }

    /**
     * Users can have many threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Users can have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Users can have many credits.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
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
        return $this->attributes['avatar_url'] ?: request()->getSchemeAndHttpHost() . '/images/phiwifi.png';
    }

    public function getAvatarSmallAttribute()
    {
        return $this->attributes['avatar_url'] ?: '/images/phiwifi.png';
    }

    public function getAvatarUrlAttribute($value)
    {
        return $value ?: env('APP_URL') . '/images/phiwifi.png';
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
        $roles = $this->roles;
        if (!is_array($roles)) {
            $roles = $roles->toArray();
        }
        $adminGroup = implode(',', array_column($roles, 'display_name'));
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

    public function hasFollowThread(Thread $thread)
    {
        if (Auth::guest()) {
            return 'unFollow';
        }
        if (Auth::user()->follows()->ofType(Thread::class)->ofId($thread->id)->count() > 0){
            return "followed";
        } else {
            return "unFollow";
        }
    }

    public static function hasFollowUser(User $user)
    {

        if (Auth::guest()) {
            return 'unFollow';
        }

        $iFollowUser = Auth::user()->follows()->ofType(User::class)->ofId($user->id)->count() > 0;
        $userFollowMe = $user->follows()->ofType(User::class)->ofId(Auth::id())->count() > 0;

        if ($userFollowMe && $iFollowUser) {
            return 'followEachOther';
        } elseif ($iFollowUser) {
            return 'followed';
        } else {
            return 'unFollow';
        }
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

    public function hasFavoriteThread(Thread $thread)
    {
        return $this->favorites()->ofThread($thread->id)->count() > 0;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }


    public function getNotificationCountAttribute()
    {
        return $this->attributes['notification_reply_count'] + $this->attributes['notification_at_count'] +
            $this->attributes['notification_system_count'] + $this->attributes['notification_chat_count'] +
            $this->attributes['notification_follow_count'];
    }
}
