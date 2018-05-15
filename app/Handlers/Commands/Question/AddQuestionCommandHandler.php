<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/4
 * Time: 10:33
 */

namespace Hifone\Handlers\Commands\Question;

use Hifone\Commands\Question\AddQuestionCommand;
use Hifone\Models\Question;
use Hifone\Models\Thread;
use Hifone\Services\Filter\WordsFilter;
use Hifone\Services\Tag\AddTag;
use Agent;

class AddQuestionCommandHandler
{
    private $filter;

    public function __construct(WordsFilter $filter)
    {
        $this->filter = $filter;
    }

    public function handle(AddQuestionCommand $command)
    {
        $thumbnails = getFirstImageUrl($command->body);

        $data = [
            'title' => $command->title,
            'body_original' => $command->body,
            'thumbnails' => $thumbnails,
            'excerpt' => Thread::makeExcerpt($command->body),
            'bad_word' => $this->filter->filterWord($command->title.$command->body),
            'score' => $command->score,
            'user_id' => $command->userId,
            'device' => $command->device,
            'ip' => $command->ip
        ];
        $command->body = app('parser.at')->parse($command->body);
        $command->body = app('parser.emotion')->parse($command->body);
        //只有H5和app发帖需要自动转义链接，web端不需要
        if (Agent::match('iPhone') || Agent::match('Android')) {
            $command->body = app('parser.link')->parse($command->body);
        }
        $data['body'] = clean($command->body);
        $question = Question::create($data);

        app(AddTag::class)->attach($question, $command->tagIds);

        return $question;
    }

}