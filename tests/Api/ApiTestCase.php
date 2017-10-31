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

    public function get($uri, array $headers = [])
    {
        $uri = $this->baseApi . $uri;
        return parent::get($uri, $headers);
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        $uri = $this->baseApi . $uri;
        return parent::post($uri, $data, $headers);
    }

    public function put($uri, array $data = [], array $headers = [])
    {
        $uri = $this->baseApi . $uri;
        return parent::put($uri, $data, $headers);
    }

    public function delete($uri, array $data = [], array $headers = [])
    {
        $uri = $this->baseApi . $uri;
        return parent::delete($uri, $data, $headers);
    }

    public function patch($uri, array $data = [], array $headers = [])
    {
        $uri = $this->baseApi . $uri;
        return parent::patch($uri, $data, $headers);
    }
}