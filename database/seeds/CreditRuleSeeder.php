<?php

use Illuminate\Database\Seeder;

class CreditRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('credit_rules')->insert([
            'name'   => '提问过审核',
            'slug'   => 'question_audited',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '提问被删除',
            'slug'   => 'question_deleted',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '提问被置顶',
            'slug'   => 'question_pin',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '回答过审核',
            'slug'   => 'answer_audited',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '提问被回答',
            'slug'   => 'question_answered',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '回答被回复',
            'slug'   => 'answer_commented',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '回答被删除',
            'slug'   => 'answer_deleted',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '回答被置顶',
            'slug'   => 'answer_pin',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '回帖被置顶',
            'slug'   => 'reply_pin',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '问题被加精',
            'slug'   => 'question_excellent',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->delete(51);

        DB::table('credit_rules')->where('slug', 'favorite')->update(['slug' => 'favorited']);
        DB::table('credit_rules')->where('slug', 'thread_favorite')->update(['slug' => 'favorite']);
        DB::table('credit_rules')->where('slug', 'favorite_removed')->update(['slug' => 'favorited_removed']);
        DB::table('credit_rules')->where('slug', 'thread_favorite_removed')->update(['slug' => 'favorite_removed']);

        DB::table('tag_types')->where('display_name', '自动标签')->update(['type' => '3']);

        DB::table('credit_rules')->insert([
            'name'   => '问题被关注',
            'slug'   => 'question_followed',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '关注问题',
            'slug'   => 'follow_question',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '问题被取消关注',
            'slug'   => 'question_followed_removed',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '取消关注问题',
            'slug'   => 'follow_question_removed',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '问题被下沉',
            'slug'   => 'question_down',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '回答被采纳',
            'slug'   => 'answer_adopted',
            'type'   => 1,
            'times'  => 40,
            'reward' => 2
        ]);

    }
}
