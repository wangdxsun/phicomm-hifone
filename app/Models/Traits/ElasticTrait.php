<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/4/19
 * Time: 17:13
 */

namespace Hifone\Models\Traits;

trait ElasticTrait
{
    public static function bootElasticTrait()
    {
        static::saved(function ($model) {
            $model->postSave();
        });

        static::created(function($model){
            $model->postCreate();
        });
    }

    public function postSave($model) {
        dump('postSave');
    }

    public function postCreate() {
        dump('postCreate');
    }
}