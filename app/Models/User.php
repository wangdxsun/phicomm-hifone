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
use Cmgmyr\Messenger\Traits\Messagable;
use Elasticquent\ElasticquentTrait;
use Hifone\Models\Traits\SearchTrait;
use Hifone\Presenters\UserPresenter;
use Hifone\Services\Tag\TaggableInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use McCool\LaravelAutoPresenter\HasPresenter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Hifone\Models\Traits\Taggable;

class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract, HasPresenter, TaggableInterface
{
    use Authenticatable, CanResetPassword, EntrustUserTrait, Messagable, SearchTrait, ElasticquentTrait, Taggable;

    // Enable hasRole( $name ), can( $permission ),
    //   and ability($roles, $permissions, $options)

    // Enable soft delete

    //邀请回答状态
    const ANSWERED = 2;
    const INVITED = 1;
    const TO_INVITE = 0;

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
    protected $hidden = [
        'phone',
        'password',
        'remember_token',
        'salt',
        'refresh_token',
        'is_banned',
        'image_url',
        'location',
        'location_id',
        'bio',
        'website',
        'company',
        'signature',
        'locale',
        'regip',
        'last_op_user_id',
        'last_op_time',
        'last_op_reason',
        'last_visit_time',
        'created_at',
        'updated_at',
        'deleted_at',
        'nickname',
        'email',
        'last_active_time',
        'last_active_time_app',
        'last_active_time_web',
        'last_visit_time_app',
        'last_visit_time_web',
        'roles',
        'pivot'
    ];

    protected $searchable = [
        'username',
    ];

    protected $mappingProperties = [
        'username' => ['type' => 'string', 'analyzer' => 'standard'],
        'score' => ['type' => 'integer'],
        'role' => ['type' => 'string', 'index' => 'no'],
        'avatar_url' => ['type' => 'string', 'index' => 'no'],
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

    //follows表多态关联
    public function followers()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function follows()
    {
        return $this->hasMany(Follow::class);
    }

    //当前用户关注的人
    public function followUsers()
    {
        return $this->morphedByMany(User::class, 'followable', 'follows')->orderBy('follows.created_at', 'desc');
    }

    //关注当前用户的人
    public function followedUsers()
    {
        return $this->morphToMany(User::class, 'followable', 'follows')->orderBy('follows.created_at', 'desc');
    }

    public function followQuestions()
    {
        return $this->morphedByMany(Question::class, 'followable', 'follows')->withPivot('answer_count');
    }

    public function identities()
    {
        return $this->hasMany(Identity::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    //我投了哪些项
    public function votes()
    {
        return $this->hasMany(OptionUser::class);
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_user');
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
        return $this->attributes['avatar_url'] ?: env('APP_URL') . '/images/phiwifi.png';
    }

    public function getAvatarSmallAttribute()
    {
        return $this->attributes['avatar_url'] ?: '/images/phiwifi.png';
    }

    public function getAvatarUrlAttribute($value)
    {
        if ($value) {
            if (substr($value, 0, 4) <> 'http') {
                return env('APP_URL') . $value;
            }
            return $value;
        }
        return env('APP_URL') . '/images/phiwifi.png';
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
        unset($this->roles); //修改roleId之后强制重新读取数据库中的roles
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
        return Auth::user()->follows()->ofType(Thread::class)->ofId($thread->id)->count() > 0;
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

    //判断用户是否关注版块
    public static function hasFollowNode(Node $node)
    {
        if (Auth::guest()) {
            return 'unFollow';
        }
        if (Auth::user()->follows()->ofType(Node::class)->ofId($node->id)->count() > 0){
            return "followed";
        } else {
            return "unFollow";
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

    public function hasLikeAnswer(Answer $answer)
    {
        return $this->likes()->ofType(Answer::class)->ofId($answer->id)->count() > 0;
    }

    public function hasLikeComment(Comment $comment)
    {
        return $this->likes()->ofType(Comment::class)->ofId($comment->id)->count() > 0;
    }

    public function hasReportThread(Thread $thread)
    {
        return $this->reports()->ofType(Thread::class)->ofId($thread->id)->count() > 0;
    }

    public function hasReportReply(Reply $reply)
    {
        return $this->reports()->ofType(Reply::class)->ofId($reply->id)->count() > 0;
    }

    public function hasReportQuestion(Question $question)
    {
        return $this->reports()->ofType(Question::class)->ofId($question->id)->count() > 0;
    }

    public function hasReportAnswer(Answer $answer)
    {
        return $this->reports()->ofType(Answer::class)->ofId($answer->id)->count() > 0;
    }

    public function hasReportComment(Comment $comment)
    {
        return $this->reports()->ofType(Comment::class)->ofId($comment->id)->count() > 0;
    }

    public function hasFavoriteThread(Thread $thread)
    {
        return $this->favorites()->ofThread($thread->id)->count() > 0;
    }

    public function hasVoteThread(Thread $thread)
    {
        return $this->votes()->ofThread($thread)->count() > 0;
    }

    public function hasVoteOption(Option $option)
    {
        return $this->votes()->ofOption($option)->count() > 0;
    }

    public function hasCommentThread(Thread $thread)
    {
        return $this->replies()->ofThread($thread)->count() > 0;
    }

    public function hasFollowQuestion(Question $question)
    {
        return $this->follows()->ofType(Question::class)->ofId($question->id)->count() > 0;
    }

    public function hasAnswerQuestion(Question $question)
    {
        return $this->answers()->ofQuestion($question)->count() > 0;
    }

    public function hasBeenInvited(Question $question)
    {
        return Invite::question($question)->where('to_user_id', $this->id)->count() > 0;
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    //邀请别人的记录
    public function invites()
    {
        return $this->hasMany(Invite::class, 'from_user_id');
    }

    //被邀请的记录
    public function inviters()
    {
        return $this->hasMany(Invite::class, 'to_user_id');
    }

    public function inviteUsers(Question $question)
    {
        return $this->belongsToMany(User::class, 'invites', 'from_user_id', 'to_user_id')->wherePivot('question_id', $question->id);
    }

    //V5.0.0版本后弃用，使用者自行组合各分项的count
    public function getNotificationCountAttribute()
    {
        return $this->attributes['notification_reply_count'] + $this->attributes['notification_at_count'] +
            $this->attributes['notification_system_count'] + $this->attributes['notification_chat_count'] +
            $this->attributes['notification_follow_count'];
    }

    //根据用户版主信息查询版块信息
    public function moderators()
    {
        return $this->belongsToMany(Node::class, 'moderators', 'user_id','node_id');
    }

    //根据用户实习版主信息查询版块信息
    public function praModerators()
    {
        return $this->belongsToMany(Node::class,'pra_moderators', 'user_id','node_id');
    }

    public function followedNodes()
    {
        return $this->morphedByMany(Node::class, 'followable', 'follows');
    }

    //问题
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    //回答
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    //回复
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeExpertSort($query)
    {
        return $query->orderBy('answer_count', 'desc')->orderBy('score', 'desc')->orderBy('id');
    }

    public function isAdmin()
    {
        return $this->hasRole(['Admin', 'Founder']);
    }

}
