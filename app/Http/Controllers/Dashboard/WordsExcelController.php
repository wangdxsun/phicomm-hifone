<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Services\Filter\Utils\TrieTree;

use Hifone\Http\Requests;
use Hifone\Http\Controllers\Controller;
use \Auth;
use Input;
use \Excel;
use \Redirect;
use Hifone\Models\Word;
use \Cache;

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
        $path = Input::file('import_file')->getRealPath();
        $fileType = $this->parseFileType('import_file');

        if (strtolower($fileType) != 'xls' && strtolower($fileType) != 'xlsx') {
            return Redirect::route('dashboard.word.index')->withErrors(trans('hifone.failure').'文件格式不正确');
        }
        $data = Excel::load($path)->all();
        if ($data->count() > 5000) {
            return Redirect::route('dashboard.word.index')->withErrors(trans('hifone.failure').'敏感词条目不大于5000条');
        }

        $insert = [];
        $addCount = 0;
        $coverCount = 0;
        $words = Word::all()->pluck('word')->toArray();
        foreach ($data as $key => $value) {
            if (empty($value->word)) {
                continue;
            }
            if (array_search($value->word, $words) !== false) {
                Word::where('word',$value->word)->delete();
                $coverCount++;
            }
            $insert[] = [
                'last_op_user_id' => Auth::id(),
                'type' => $value->type,
                'word' => $value->word,
                'status' => $value->status,
                'replacement' => $value->replacement,
                'created_at' => date('Y-m-d H:i:s'),
                'last_op_time' => date('Y-m-d H:i:s'),
            ];
            $addCount++;
        }
        Word::insert($insert);//批量创建
        $this->cacheAll($trieTree);//更新缓存
        return Redirect::route('dashboard.word.index')
            ->withSuccess(trans('hifone.awesome').'本次成功导入'.$addCount.'条，\\n重复'.$coverCount.'条');
    }

    public function check(TrieTree $trieTree)
    {
        $path = Input::file('check_file')->getRealPath();
        $fileType = $this->parseFileType('check_file');

        if (strtolower($fileType) != 'xls' && strtolower($fileType) != 'xlsx') {
            return Redirect::route('dashboard.word.index')->withErrors(trans('hifone.failure').'文件格式不正确');
        }
        $data = Excel::load($path)->get(['word']);
        if ($data->count() > 5000) {
            return Redirect::route('dashboard.word.index')->withErrors(trans('hifone.failure').'敏感词条目不大于5000条');
        }
        $cacheTime = 30 * 24 * 60; // 单位为分钟
        $tree = Cache::remember('words', $cacheTime, function () use ($trieTree){
            $words = Word::pluck('word');
            return $trieTree->importBadWords($words);
        });
        foreach ($data as $key => $value) {
            if (($temp = $trieTree->contain($value->word, $tree)) !== false) {//包含于缓存字典树
                $data[$key]['exist'] = $temp;
            } else {
                $data[$key]['exist'] = '否';
            }
        }
        Excel::create('check_results',function($excel) use ($data){
            $excel->sheet('words', function($sheet) use ($data){
                $sheet->fromArray($data);//第一行输出字段名
            });
        })->export('xls');
        return Redirect::route('dashboard.word.index');
    }

    private function cacheAll(TrieTree $trieTree)
    {
        $cacheTime = 30 * 24 * 60; // 单位为分钟
        $words = Word::pluck('word');
        $tree = $trieTree->importBadWords($words);
        Cache::put('words', $tree, $cacheTime);
    }

    private function parseFileType($key)
    {
        $original_name = Input::file($key)->getClientOriginalName();
        $fileTypes = explode('.' , $original_name);
        $fileType = $fileTypes[count($fileTypes)-1];
        return $fileType;
    }
}


