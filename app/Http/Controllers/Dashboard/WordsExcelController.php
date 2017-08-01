<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Services\Filter\Utils\TrieTree;
use Hifone\Services\Filter\WordInit;
use Illuminate\Http\Request;

use Hifone\Http\Requests;
use Hifone\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Input;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use Hifone\Models\Word;
use Illuminate\Support\Facades\Cache;
use Mockery\Exception;

class WordsExcelController extends  Controller
{
    //Excel导出
    public function export()
    {
        $data = Word::get(['type', 'word', 'status', 'replacement'])->toArray();
        Excel::create('illegal_words',function($excel) use ($data){
            $excel->sheet('words', function($sheet) use ($data){
                //$sheet->rows($data);  rows()方法不输出字段名，第一行即为数据
                $sheet->fromArray($data);//第一行输出字段名
            });
        })->export('xls');

    }

    public function import(TrieTree $trieTree)
    {
        if(Input::hasFile('import_file')) {
            $path = Input::file('import_file')->getRealPath();
            $original_name = Input::file('import_file')->getClientOriginalName();

            $file_types = explode('.' , $original_name);
            $file_type = $file_types[count($file_types)-1];
            //判断是否.xls文件
            if(strtolower($file_type =='xls') || strtolower($file_type =='xlsx') ) {
                $data = Excel::load($path, function($reader) {})->all();
                if ($data->count() <= 5000) {
                    if ($data) {
                        $insert = [];
                        $words = Word::all()->pluck('word')->toArray();
                        foreach ($data as $key => $value) {
                            //删除重复敏感词数据库记录
                            if (array_search($value->word, $words) !== false) {
                                Word::where('word',$value->word)->delete();
                            }
                            if(!empty($value->word)){
                                $insert[] = [
                                    'last_op_user_id' => Auth::id(),
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
                        $this->cacheAll($trieTree);//更新缓存
                        return Redirect::route('dashboard.word.index')
                            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), '导入成功'));
                    }
                } else {
                    return Redirect::route('dashboard.word.index')
                        ->withSuccess(sprintf('%s %s', trans('hifone.failure'), '敏感词条目不大于5000条'));
                }

            } else {
                return Redirect::route('dashboard.word.index')
                    ->withSuccess(sprintf('%s %s', trans('hifone.failure'), '文件格式不正确'));
            }
        }
    }

    protected function cacheAll(TrieTree $trieTree)
    {
        $cacheTime = 30 * 24 * 60; // 单位为分钟
        $words = Word::pluck('word');
        $tree = $trieTree->importBadWords($words);
        Cache::put('words', $tree, $cacheTime);
    }
}


