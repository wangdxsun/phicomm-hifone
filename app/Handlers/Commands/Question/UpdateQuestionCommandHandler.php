<?php
namespace Hifone\Handlers\Commands\Question;

use Hifone\Commands\Question\UpdateQuestionCommand;
use Hifone\Models\Question;
use Agent;
use Auth;
use Carbon\Carbon;
use Redirect;

class UpdateQuestionCommandHandler
{
    public function handle(UpdateQuestionCommand $command)
    {
        $question = $command->question;
        if (isset($command->data['body']) && $command->data['body']) {
            $command->data['body_original'] = $command->data['body'];
            //$command->data['excerpt'] = Question::makeExcerpt($command->data['body']);
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
            $command->data['status'] = Question::AUDIT;
            $command->data['is_excellent'] = 0;
        }

        $question->update($this->filter($command->data));
        if ($command->data['questionTags'] == '') {
            return Redirect::route('dashboard.questions.edit', $question->id)
                ->withErrors('请选择问题类型');
        } else {
            $tagData = explode(',', $command->data['questionTags']);
            $question->tags()->sync($tagData);
        }
        return $question;
    }

    protected function filter($data)
    {
        return array_filter($data, function ($val) {
            return $val !== null;
        });
    }
}