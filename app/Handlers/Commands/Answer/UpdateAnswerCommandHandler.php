<?php
namespace Hifone\Handlers\Commands\Answer;

use Agent;
use Auth;
use Carbon\Carbon;
use Hifone\Commands\Answer\UpdateAnswerCommand;
use Hifone\Models\Answer;

class UpdateAnswerCommandHandler
{
    public function handle(UpdateAnswerCommand $command)
    {
        $answer = $command->answer;
        if (isset($command->data['body']) && $command->data['body']) {
            $command->data['body_original'] = $command->data['body'];
            $command->data['body'] = app('parser.at')->parse($command->data['body']);
            $command->data['body'] = app('parser.emotion')->parse($command->data['body']);
            //只有H5和app发帖需要自动转义链接，web端不需要
            if (Agent::match('iPhone') || Agent::match('Android')) {
                $command->data['body'] = app('parser.link')->parse($command->data['body']);
            }
            $command->data['thumbnails'] = getFirstImageUrl($command->data['body_original']);
        }
        //更新编辑时间 if (created_at != edit_time) 帖子被修改过
        $command->data['edit_time'] = Carbon::now()->toDateTimeString();

        //用户编辑状态回退、精华失效
        if (!Auth::user()->hasRole(['Admin', 'Founder'])) {
            $command->data['status'] = Answer::AUDIT;
        }

        $answer->update($this->filter($command->data));
        return $answer;
    }

    protected function filter($data)
    {
        return array_filter($data, function ($val) {
            return $val !== null;
        });
    }
}