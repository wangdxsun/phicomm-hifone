<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Services\Filter\WordsFilter;

class CheckController extends  Controller
{
    public function check(){
        $words_filter = new WordsFilter();

        //$data = WordsFilter::wordReplace();
        $post ='这几天来，方鸿渐白天昏昏想睡，晚上倒又清醒。早晨方醒，听见窗外树上鸟叫，无理由地高兴，无目的地期待，
        心似乎减轻重量，直升上去。可是这欢喜是空的，像小孩子放的气球，上去不到几尺，便爆烈归于乌有，只留下忽忽若失的无名怅惘。
        他坐立不安地要活动，却颓唐使不出劲来，好比杨花在春风里飘荡，而身轻无力，终飞不远。他自觉这种惺忪迷怠的心绪，
        完全像填词里所写幽闺伤春的情境。现在女人都不屑伤春了，自己枉为男人，还脱不了此等刻板情感，岂不可笑！
        譬如鲍小姐那类女人，决没工夫伤春，但是苏小姐呢？她就难说了；她像是多愁善感的古美人模型。船上一别，不知她近来怎样。
        自己答应过去看她，何妨去一次呢？明知也许从此多事，可是实在生活太无聊，现成的女朋友太缺乏了！好比睡不着的人，
        顾不得安眠药片的害处，先要图眼前的舒服';

        $data = $words_filter->wordsFilter($post);
        dd($data);
    }
}
