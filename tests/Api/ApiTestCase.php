<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/23
 * Time: 16:51
 */

namespace Hifone\Test\Api;

use Hifone\Test\AbstractTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiTestCase extends AbstractTestCase
{
    use DatabaseTransactions;

    private $baseApi = '/api/v1';

    private $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1aWQiOiIxMjE0IiwiY29kZSI6ImZlaXh1bi5TSF8xIiwidHlwZSI6ImFjY2Vzc190b2tlbiIsImlzcyI6IlBoaWNvbW0iLCJuYmYiOjE0OTQyMjA3MDYsImV4cCI6MTQ5NDI4NTUwNiwicmVmcmVzaFRpbWUiOiIyMDE3LTA1LTA4IDE5OjE4OjI2In0.W5Xj8ZVjDncS5LMJ0IHFG8W97LTMxRFGWZYgfrasJR4';

    public function get($uri, array $headers = [])
    {
        $this->init($uri, $headers);
        return parent::get($uri, $headers);
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        $this->init($uri, $headers);
        return parent::post($uri, $data, $headers);
    }

    public function put($uri, array $data = [], array $headers = [])
    {
        $this->init($uri, $headers);
        return parent::put($uri, $data, $headers);
    }

    public function delete($uri, array $data = [], array $headers = [])
    {
        $this->init($uri, $headers);
        return parent::delete($uri, $data, $headers);
    }

    public function patch($uri, array $data = [], array $headers = [])
    {
        $this->init($uri, $headers);
        return parent::patch($uri, $data, $headers);
    }

    private function init(&$uri, &$headers)
    {
        $uri = $this->baseApi . $uri;
        if (!isset($headers['Authorization'])) {
            $headers['Authorization'] = $this->token;
        }
    }
}