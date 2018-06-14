<?php
namespace Hifone\Handlers\Commands\Question;

use Hifone\Commands\Question\UpdateQuestionCommand;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Question;
use Agent;
use Auth;
use Carbon\Carbon;
use Hifone\Services\Filter\WordsFilter;
use Redirect;

class UpdateQuestionCommandHandler
{
    private $filter;

    public function __construct(WordsFilter $filter)
    {
        $this->filter = $filter;
    }
    public function handle(UpdateQuestionCommand $command)
    {
        $question = $command->question;
        if ($command->data['questionTags'] == '') {
            throw new HifoneException('类型选择需在1-4个');
        } else {
            $tagData = explode(',', $command->data['questionTags']);
            if(count($tagData) > 4) {
                throw new HifoneException('类型选择需在1-4个');
            }
            $question->tags()->sync($tagData);
            unset($command->data['questionTags']);
        }

        if (isset($command->data['body']) && $command->data['body']) {
            $command->data['body_original'] = $command->data['body'];
            $command->data['excerpt'] = app('parser.emotion')->makeExcerpt($command->data['body']);
            $command->data['bad_word'] = $this->filter->filterWord($command->data['title'] . $command->data['body']);
            $command->data['body'] = app('parser.at')->parse($command->data['body']);
            $command->data['body'] = app('parser.emotion')->parse($command->data['body']);
            //只有H5和app发帖需要自动转义链接，web端不需要
            if (Agent::match('iPhone') || Agent::match('Android')) {
                $command->data['body'] = app('parser.link')->parse($command->data['body']);
            }
            $command->data['thumbnails'] = getFirstImageUrl($command->data['body_original']);
        }
        //更新编辑时间 if (created_at != edit_time) 问题被修改过
        $command->data['edit_time'] = Carbon::now()->toDateTimeString();

        $question->update($this->filter($command->data));

        return $question;
    }

    protected function filter($data)
    {
        return array_filter($data, function ($val) {
            return $val !== null;
        });
    }
}