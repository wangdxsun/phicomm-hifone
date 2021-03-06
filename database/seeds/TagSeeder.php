<?php

use Illuminate\Database\Seeder;


class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tag_type_id = \Hifone\Models\TagType::where('display_name', '自动标签')->first()->id;
        DB::table('tags')->insert([
            'name'   => '盒子',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => 'R1',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => 'K2系列',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => 'K3系列',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '内容贡献量多',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '回帖质量高',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '发帖质量高',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '新用户',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '近期登陆',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '2周活跃高',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '4周活跃高',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);
        DB::table('tags')->insert([
            'name'   => '8周活跃高',
            'channel' => 0,
            'count'  => 0,
            'tag_type_id' => $tag_type_id,
        ]);

    }
}
