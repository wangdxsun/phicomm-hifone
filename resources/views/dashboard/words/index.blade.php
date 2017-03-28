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
        <form action="{{ URL('dashboard/wordsExcel/import')}}"  id="importExcel" method="POST" class="form-inline pull-right"
              enctype='multipart/form-data'>
            <div class="">
                <div class="btn btn-sm btn-success head_portrait">
                    <span>批量导入</span>
                </div>
                <input type="file" name="import_file" id="import" onChange="commitForm()" class="hide"/>
            </div>
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
                            <input type="text" name="query[type]" class="form-control" value="" placeholder="类型">
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
                                <span class="modify_info" data-name="{{$word->id}},{{$word->find}},{{$word->substitute}}"><i class="fa fa-pencil"></i></span>
                                <a data-url="{{ route('dashboard.word.destroy',['id'=>$word->id]) }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="content-wrapper">
                                <div class="header sub-header">
                                            <span class="uppercase">
                                                 {{ trans('dashboard.words.edit.title') }}
                                             </span>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        {!! Form::open(['url'=>'dashboard/word/editInfo']) !!}
                                        {!! Form::hidden('word[id]', $word->id, ['class' => 'form-control', 'id' => 'word-id', 'placeholder' => '']) !!}
                                        <fieldset>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.type.title') }}</label><label style="margin-left: 10px;">(*为必填项)</label>
                                            {!!  Form::select('word[type]', ['政治' => trans('dashboard.words.type.type_1'),'广告' => trans('dashboard.words.type.type_2'),
                                            trans('dashboard.words.type.type_3') => trans('dashboard.words.type.type_3'),trans('dashboard.words.type.type_4') =>
                                             trans('dashboard.words.type.type_4'),trans('dashboard.words.type.type_5') => trans('dashboard.words.type.type_5'),
                                            '默认' => trans('dashboard.words.type.type_0')],null,
                                            ['class' => 'form-control', 'id' => 'word-type', 'placeholder' =>'—选择类别—'])!!}
                                        </div>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.content') }}*</label>
                                            {!! Form::text('word[find]', $word->find, ['class' => 'form-control', 'id' => 'word-find', 'placeholder' => '']) !!}
                                        </div>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.action.title') }}*</label>
                                            {!!  Form::select('word[replacement]', ['审核关键词' => trans('dashboard.words.action.type_1'),'禁止关键词' => trans('dashboard.words.action.type_2'),
                                                 '替换关键词' =>trans('dashboard.words.action.type_3')], null,
                                                 ['class' => 'form-control', 'id' => 'word-replacement', 'placeholder' =>'—过滤状态—'])!!}

                                        </div>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.substitute') }}</label>
                                            {!! Form::text('word[substitute]', $word->substitute, ['class' => 'form-control', 'id' => 'word-substitute', 'placeholder' => '']) !!}
                                        </div>
                                        </fieldset>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    {!! Form::submit('保存',['class'=>'btn btn-success']) !!}
                                                    {!! Form::button('取消',['class'=>'btn btn-default']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="text-right">
                    <!-- Pager -->
                    {!! $words->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ URL::asset('js/words.js') }}"></script>
@stop
