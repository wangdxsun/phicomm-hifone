<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/30
 * Time: 10:40
 */

namespace Hifone\Console\Commands;

use Hifone\Events\Adopt\AnswerWasAdoptedEvent;
use Hifone\Exceptions\Handler;
use Hifone\Jobs\RewardScore;
use Hifone\Models\Question;
use Hifone\Models\User;
use Illuminate\Console\Command;

class AutoAdopt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:adopt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "system auto adopt answer for question's author when author and manager does not adopt on time.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 从第一个回答的算起5天后提问者未处理，10天后管理员未处理，系统将自动给分；
     * 给分规则为：按照点赞数最高的给分，同等点赞给到第一个回答用户；
     * @param Handler $handler
     * @return mixed
     */
    public function handle(Handler $handler)
    {
        //系统自动采纳最佳回答并发回答被采纳的通知
        Question::visible()->autoAdopted()->chunk(20, function ($questions) use ($handler) {
            foreach ($questions as $question) {
                try {
                    $answer = $question->answers()->visible()->notSelf($question->user_id)->notAdopted()->likeMost()->first();
                    if ($answer <> null) {
                        $question->update(['answer_id', $answer->id]);
                        $answer->update(['adopted' => 1]);
                        event(new AnswerWasAdoptedEvent(User::find(0), $answer->user, $answer));
                        //给被采纳人加悬赏值
                        dispatch(new RewardScore($answer->user, $answer->question->score));
                    } else {//自动采纳失败，则不再自动采纳该问题
                        $question->update(['answer_id', 0]);
                    }
                } catch (\Exception $e) {
                    \Log::info('AutoAdopt:question', $question->toArray());
                    \Log::info('AutoAdopt:answer', $answer == null ? [] : $answer->toArray());
                    $handler->report($e);
                }
            }
        });

    }
}