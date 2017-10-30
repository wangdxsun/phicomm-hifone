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

class AbstractApiTestCase extends AbstractTestCase
{
    use DatabaseTransactions;

    public function get($uri, array $headers = [])
    {
        $uri = '/api/v1' . $uri;
        parent::get($uri, $headers);
    }
}