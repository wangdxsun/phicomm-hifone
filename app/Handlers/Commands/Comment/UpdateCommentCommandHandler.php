<?php
namespace Hifone\Handlers\Commands\Comment;

use Agent;
use Auth;
use Hifone\Commands\Comment\UpdateCommentCommand;
use Hifone\Models\Answer;

class UpdateCommentCommandHandler
{
    public function handle(UpdateCommentCommand $command)
    {
        $comment = $command->comment;
        if (isset($command->data['body']) && $command->data['body']) {
            $command->data['body_original'] = $command->data['body'];
            $command->data['body'] = app('parser.at')->parse($command->data['body']);
            $command->data['body'] = app('parser.emotion')->parse($command->data['body']);
            //只有H5和app回复需要自动转义链接，web端不需要
            if (Agent::match('iPhone') || Agent::match('Android')) {
                $command->data['body'] = app('parser.link')->parse($command->data['body']);
            }
            $command->data['thumbnails'] = getFirstImageUrl($command->data['body_original']);
        }

        //用户编辑状态回退、精华失效
        if (!Auth::user()->hasRole(['Admin', 'Founder'])) {
            $command->data['status'] = Answer::AUDIT;
        }

        $comment->update($this->filter($command->data));
        return $comment;
    }

    protected function filter($data)
    {
        return array_filter($data, function ($val) {
            return $val !== null;
        });
    }
}