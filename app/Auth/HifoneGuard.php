<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/2
 * Time: 16:32
 */

namespace Hifone\Auth;

use Hifone\Exceptions\HifoneException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider as UserProviderContract;
use Illuminate\Http\Request;

class HifoneGuard extends SessionGuard implements Guard
{
    protected $inputKey = 'token';

    public $bind = true;

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
            if (is_null($user)) {
                $this->bind = false;
            }
        } else {
            $id = $this->session->get($this->getName());
            if (! is_null($id)) {
                $user = $this->provider->retrieveById($id);
            }
        }

        return $this->user = $user;
    }

    public function phicommId()
    {
        $token = $this->getTokenForRequest();
        if (!is_null($token)) {
            $phicommId = $this->getIdFromToken($token);
        } elseif ($this->user) {
            $phicommId = $this->user->phicomm_id;
        } else {
            $phicommId = $this->session->get('phicommId');
        }

        return $phicommId;
    }

    public function bind()
    {
        return $this->bind;
    }

    public function token()
    {
        $token = $this->getTokenForRequest();
        if (empty($token)) {
            $token = $this->session->get('access_token');
        }

        return $token;
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
        $tokens = explode('.', $token);
        if (! is_array($tokens) || count($tokens) <> 3) {
            throw new HifoneException('token格式不正确');
        }
        $tokenInfo = json_decode(base64_decode($tokens[1]), true);
        return $tokenInfo['uid'];
    }

    public function logout()
    {
        $this->session->remove('access_token');
        $this->session->remove('phicommId');
        $this->session->remove('user_active_date');
        $this->session->remove('active_date');
        parent::logout();
    }
}