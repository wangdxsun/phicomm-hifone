<?php

namespace Hifone\Handlers\Jobs;

use Hifone\Jobs\RewardScore;
use Hifone\Services\Guzzle\Score;

class RewardScoreHandler
{
    //自定义积分项目（悬赏和采纳）
    public function handle(RewardScore $rewardScore)
    {
        $data = [
            'userId'          => $rewardScore->user->phicomm_id,
            'score'           => $rewardScore->score,
            'name'            => 'question_reward',
            'serialNumber'    => $this->orderNumber($rewardScore),
        ];

        app(Score::class)->customize($data);
    }

    //生成唯一流水号
    public function orderNumber(RewardScore $rewardScore)
    {
        return 'BBS_question_reward'.'_'.$rewardScore->user->phicomm_id.date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

}

