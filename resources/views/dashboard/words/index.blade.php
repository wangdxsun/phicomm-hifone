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
            {{ trans('dashboard.words.add.title') }}
        </a>
        <a class="btn btn-sm btn-success pull-right"  href="{{ route('dashboard.word.create') }}">
             {{trans('dashboard.words.edit.batch_out')}}
        </a>
        <div class="clearfix"></div>
    </div>
    <div class="content-wrapper header-fixed">
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" value="" placeholder="标题">
                        </div>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">#</td>
                        <td>类别</td>
                        <td style="width:20%">词语内容</td>
                        <td style="width:20%">操作</td>
                        <td>替代词</td>
                        <td>创建时间</td>
                        <td style="width:15%">操作</td>
                    </tr>
                    @foreach($words as $word)
                        <tr>
                            <td>{{ $word->id }}</td>
                            <td>{{ $word->type }}</td>
                            <td>{{ $word->word }}</td>
                            <td>{{ $word->action }}</td>
                            <td>{{ $word->substitute }}</td>
                            <td>{{ $word->created_at }}</td>
                            <td>
                                <a href="{{ route('dashboard.word.edit',['id'=>$word->id]) }}"><i class="fa fa-pencil"></i></a>
                                <a data-url="{{ route('dashboard.word.destroy',['id'=>$word->id]) }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <!-- Pager -->
              {{--      {!! $notices->appends(Request::except('page', '_pjax'))->render() !!}--}}
                </div>
            </div>
        </div>
    </div>
@stop