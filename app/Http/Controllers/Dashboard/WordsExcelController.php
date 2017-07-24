<?php

namespace Hifone\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use Hifone\Http\Requests;
use Hifone\Http\Controllers\Controller;
use Input;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use Hifone\Models\Word;
use Illuminate\Support\Facades\Cache;

class WordsExcelController extends  Controller
{
    //Excel导出
    public function export(){

        $data = Word::get()->toArray();
        Excel::create('illegal_words',function($excel) use ($data){
            $excel->sheet('words', function($sheet) use ($data){
                //$sheet->rows($data);  rows()方法不输出字段名，第一行即为数据
                $sheet->fromArray($data);//第一行输出字段名
            });
        })->export('xls');

    }

    public function import()
    {
        if(Input::hasFile('import_file')){
            $path = Input::file('import_file')->getRealPath();
            $original_name = Input::file('import_file')->getClientOriginalName();

            $file_types = explode('.' , $original_name);
            $file_type = $file_types[count($file_types)-1];

            //判断是否.xls文件
            if(strtolower($file_type =='xls') || strtolower($file_type =='xlsx') ){
                $data = Excel::load($path, function($reader) {})->all();
                if(!empty($data) && $data->count()){
                    $insert=[];
                    //放入缓存
                    $words = Word::all()->pluck('word');
                    foreach ($words as $key => $value) {
                        Cache::put($value, $value, 1);
                    }
                    foreach ($data as $key => $value) {
                        //删除重复敏感词数据库记录
                        if (Cache::has($value->word)) {
                            Word::where('word',$value->word)->delete();
                            Cache::pull($value->word);
                        }
//                        dd(Word::limit(5)->get()->pluck('word'));//pluck返回集合
//                        && !array_search($value->word, $insert)
                        if(!empty($value->word)){
                            $insert[] = [
                                'last_op_user_id' => $value->last_op_user_id,
                                'type' => $value->type,
                                'word' => $value->word,
                                'status' => $value->status,
                                'replacement' => $value->replacement,
                                'created_at' => date('Y-m-d H:i:s'),
                                'last_op_time' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }
                    Word::insert($insert);//批量创建
                    return Redirect::route('dashboard.word.index')
                        ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), '导入成功'));

                }
            }else{
                return Redirect::route('dashboard.word.index')
                    ->withSuccess(sprintf('%s %s', trans('hifone.failure'), '文件格式不正确'));
            }
        }
    }
}
