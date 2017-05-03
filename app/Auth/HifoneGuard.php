<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/2
 * Time: 16:32
 */

namespace Hifone\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HifoneGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The session used by the guard.
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * The Illuminate cookie creator service.
     *
     * @var \Illuminate\Contracts\Cookie\QueueingFactory
     */
    protected $cookie;

    protected $inputKey = 'token';

    protected $name = 'session';

    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
        $this->request = app('request');
        $this->session = app('session.store');
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $token = $this->getTokenForRequest();

        if (! empty($token)) {
            $user = $this->provider->retrieveByCredentials([
                'phicomm_id' => $this->getIdFromToken($token)
            ]);
        } else {
            $id = $this->session->get($this->getName());
            if (! is_null($id)) {
                $user = $this->provider->retrieveById($id);
            }
        }

        return $this->user = $user;
    }

    protected function getTokenForRequest()
    {
        $token = $this->request->input($this->inputKey);

        if (empty($token)) {
            $token = $this->request->header('Authorization');
        }

        return $token;
    }

    private function getIdFromToken($token) {
        //$tokens = explode('.', $token);
        //$tokenInfo = json_decode(base64_decode($tokens[1]), true);
        //return $tokenInfo['uid'];
        return $token;
    }

    public function getName()
    {
        return 'login_'.$this->name.'_'.sha1(static::class);
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
    }
}