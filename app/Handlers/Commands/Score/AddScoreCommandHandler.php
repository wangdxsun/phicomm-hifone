<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Score;

use GuzzleHttp\Client;
use Hifone\Commands\Score\AddScoreCommand;
use Hifone\Services\Dates\DateFactory;

class AddScoreCommandHandler
{
    protected $dates;

    public function __construct(DateFactory $dates)
    {
        $this->dates = $dates;
    }

    public function handle(AddScoreCommand $command)
    {
        $client = new Client([
            'base_uri' => env('SCORE_DOMAIN'),
        ]);
        $response = $client->post('score', [
            'headers'  => [
                'Authorization' => 'BBS HcjA2DBjzPmTWSQ7',
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                'userId' => $command->user->phicomm_id,
                'data' => [
                    [
                        'itemId'          => $command->action,
                        'serialNumber'    => $this->orderNumber($command),
                    ]
                ]
            ]
        ]);
        $content = $response->getBody()->getContents();
        return $content;
    }


    //生成唯一流水号
    public function orderNumber(AddScoreCommand $command)
    {
        return 'BBS_'.$command->action.'_'.date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }


}
