<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/16
 * Time: 16:12
 */

namespace Hifone\Handlers\Commands\Answer;


use Hifone\Commands\Answer\AddAnswerCommand;
use Hifone\Models\Answer;
use Hifone\Services\Filter\WordsFilter;
use Agent;

class AddAnswerCommandHandler
{
    private $filter;

    public function __construct(WordsFilter $filter)
    {
        $this->filter = $filter;
    }

    public function handle(AddAnswerCommand $command)
    {
        $thumbnails = getFirstImageUrl($command->body);

        $data = [
            'body_original' => $command->body,
            'thumbnails' => $thumbnails,
            'excerpt' => app('parser.emotion')->makeExcerpt($command->body),
            'bad_word' => $this->filter->filterWord($command->body),
            'user_id' => $command->userId,
            'question_id' => $command->questionId,
            'device' => $command->device,
            'ip' => $command->ip,
            'status' => $command->status,
            'comment_count' => 0
        ];
        $command->body = app('parser.at')->parse($command->body);
        $command->body = app('parser.emotion')->parse($command->body);
        //只有H5和app发帖需要自动转义链接，web端不需要
        if (Agent::match('iPhone') || Agent::match('Android')) {
            $command->body = app('parser.link')->parse($command->body);
        }
        $data['body'] = clean($command->body);
        $answer = Answer::create($data);

        return $answer;
    }

}