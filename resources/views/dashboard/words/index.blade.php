@extends('layouts.dashboard')

@section('content')
    <div class="header fixed">
        <div class="sidebar-toggler visible-xs">
            <i class="fa fa-navicon"></i>
        </div>
        <span class="uppercase">
             <i class="fa fa-filter"></i>  {{ trans('dashboard.words.word') }}
        </span>
        <a class="btn btn-sm btn-success pull-right" style="margin-left: 15px;" href="{{ route('dashboard.word.create') }}">
            {{trans('dashboard.words.add.title') }}
        </a>

        <form action="{{ URL('dashboard/wordsExcel/import')}}" method="POST" class="form-inline pull-right"
           enctype="multipart/form-data">
            <div class="form-group">
                <input name="import_file" type="file">
            </div>
           {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
           <button type="submit" class="btn btn-primary">批量导入</button>
        </form>

        <div class="clearfix"></div>
    </div>
    <div class="content-wrapper header-fixed">
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <span class="" style="margin-left: 6px;" href="">
                        □
                    </span>
                    <span class="" href="">
                        全选
                    </span>
                    <a class="" style="margin-left: 20px;" href="{{ route('dashboard.wordsExcel.export') }}">
                        {{trans('dashboard.words.edit.batch_out') }}
                    </a>
                    <a class="" style="margin-left: 20px;" href="">
                        {{ trans('dashboard.words.edit.batch_del') }}
                    </a>
                    <form class="form-inline pull-right">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" value="" placeholder="类型">
                        </div>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">□</td>
                        <td>编号</td>
                        <td style="width:20%">敏感词汇</td>
                        <td>过滤状态</td>
                        <td>替换后的词语</td>
                        <td>词语分类</td>
                        <td>操作人</td>
                        <td>时间</td>
                        <td>操作</td>
                    </tr>
                    @foreach($words as $word)
                        <tr>
                            <td>□</td>
                            <td>{{ $word->id }}</td>
                            <td>{{ $word->find }}</td>
                            <td>{{ $word->replacement }}</td>
                            <td>{{ $word->substitute }}</td>
                            <td>{{$word->type}}</td>
                            <td>{{ $word->admin }}</td>
                            <td style="width:15%">{{ $word->created_at }}</td>
                            <td style="width:10%">
                                <a href="{{ route('dashboard.word.edit',['id'=>$word->id]) }}"><i class="fa fa-pencil"></i></a>
                                <a data-url="{{ route('dashboard.word.destroy',['id'=>$word->id]) }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- Modal -->
                <div class="text-right">
                    <!-- Pager -->
                    {!! $words->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop