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
            'name'   => '提问置顶被取消',
            'slug'   => 'question_pin_removed',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);

        DB::table('credit_rules')->insert([
            'name'   => '回答置顶被取消',
            'slug'   => 'answer_pin_removed',
            'type'   => 1,
            'times'  => 40,
            'reward' => -2
        ]);
    }
}
