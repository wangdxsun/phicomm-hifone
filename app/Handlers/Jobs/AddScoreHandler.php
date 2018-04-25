<?php

namespace Hifone\Handlers\Jobs;


use GuzzleHttp\Client;
use Hifone\Jobs\AddScore;

class AddScoreHandler
{
    public function handle(AddScore $addScore)
    {
        if ($addScore->attempts() < 3) {
            $client = new Client([
                'base_uri' => env('SCORE_DOMAIN'),
            ]);
            $response = $client->post('score', [
                'headers'  => [
                    'Authorization' => env('SCORE_SECRET'),
                    'Content-Type'  => 'application/json'
                ],
                'json' => [
                    'userId' => $addScore->user->phicomm_id,
                    'data' => [
                        [
                            'itemId'          => $addScore->action,
                            'serialNumber'    => $this->orderNumber($addScore),
                        ]
                    ]
                ]
            ]);
            $content = $response->getBody()->getContents();
            return $content;
        }
    }


//生成唯一流水号
    public function orderNumber(AddScore $addScore)
    {
        return 'BBS_'.$addScore->action.'_'.date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

}

