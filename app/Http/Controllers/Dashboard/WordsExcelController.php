<?php

namespace Hifone\Http\Controllers\Dashboard;

use Illuminate\Http\Request;

use Hifone\Http\Requests;
use Hifone\Http\Controllers\Controller;
use Input;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;
use Hifone\Models\Word;
use DB;

class WordsExcelController extends  Controller
{
    //Excel导出
    public function export(){

        $data = Word::get()->toArray();
        Excel::create('illegal_words',function($excel) use ($data){
            $excel->sheet('words', function($sheet) use ($data){
                //$sheet->rows($data);  rows()方法不输出字段名，第一行即为数据
                $sheet->fromArray($data);
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
                    foreach ($data as $key => $value) {
                        if(!empty($value->find)){
                            $insert[] = ['admin' => $value->admin, 'type' => $value->type,'find' => $value->find,'replacement' => $value->replacement,
                                'substitute' =>$value->substitute ,'created_at' =>date('Y-m-d H:i:s')];
                        }
                    }
                    foreach ($insert as $key =>$value){
                        Word::create($value);
                    }
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