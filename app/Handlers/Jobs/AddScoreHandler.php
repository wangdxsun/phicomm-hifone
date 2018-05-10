<?php

namespace Hifone\Handlers\Jobs;


use GuzzleHttp\Client;
use Hifone\Jobs\AddScore;
use Hifone\Services\Guzzle\Score;

class AddScoreHandler
{
    public function handle(AddScore $addScore)
    {
        $data =  [
            'userId' => $addScore->user->phicomm_id,
            'data' => [
                [
                    'itemId'          => $addScore->action,
                    'serialNumber'    => $this->orderNumber($addScore),
                ]
            ]
        ];
        app(Score::class)->post('score', $data);
    }

//生成唯一流水号
    public function orderNumber(AddScore $addScore)
    {
        return 'BBS_'.$addScore->action.'_'.$addScore->user->phicomm_id.$addScore->from.$addScore->object;
    }

}

