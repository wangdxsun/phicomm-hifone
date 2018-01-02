@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-filter pull-left"></i>{{ trans('dashboard.words.word') }}
            <a class="btn btn-sm btn-success pull-right" style="margin-left: 15px;" href="{{ route('dashboard.word.create') }}">
                {{trans('dashboard.words.add.title') }}
            </a>
            <a class="btn btn-sm btn-success pull-right" style="margin-left: 15px;" href="{{ route('dashboard.wordsExcel.export') }}">
                {{trans('dashboard.words.edit.batch_out') }}
            </a>
            <form action="{{ URL('dashboard/wordsExcel/import')}}"  id="importExcel" method="POST" class="form-inline pull-right"
                  enctype='multipart/form-data'  style="margin-left: 15px;">
                {!! csrf_field() !!}
                <div class="btn btn-sm btn-success head_import">
                    <span>批量导入</span>
                </div>
                <input type="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="import_file" id="import" onChange="commitForm()" class="hide"/>
            </form>
            <form action="{{ URL('dashboard/wordsExcel/check')}}"  id="checkExcel" method="POST" class="form-inline pull-right"
                  enctype='multipart/form-data'  style="margin-left: 15px;">
                {!! csrf_field() !!}
                <div class="btn btn-sm btn-success head_check">
                    <span>检查词汇</span>
                </div>
                <input type="file" name="check_file" id="check" onChange="commitCheckForm()" class="hide"/>
            </form>
        </div>
        <div class="row">
            <div class="col-sm-12 toolbar">
                <div>
                    <form class="form-inline pull-right">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <input type="text" name="word[word]" class="form-control" placeholder="敏感词汇"
                                   @if (isset($search['word']))
                                   value="{{ $search['word'] }}"
                                    @endif >
                        </div>
                        <select class="form-control " name="word[status]" style="width: 200px">
                            <option value="" selected>过滤状态</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                        <select class="form-control " name="word[type]" style="width: 200px">
                            <option value="" selected>词语分类</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>
                <form class="form-inline" method="post" action="/dashboard/word/batchDestroy" id="batchForm">
                    {!! csrf_field() !!}
                    <div class="btn btn-danger btn-confirm-action">
                        {{ trans('dashboard.words.edit.batch_del') }}
                    </div>
                    @if (isset($word_count))
                        <label>{{ '共计'.$word_count.'条' }}</label>
                    @endif

                    @include('partials.errors')
                    <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                        <td>编号</td>
                        <td style="width:20%">敏感词汇</td>
                        <td>过滤状态</td>
                        <td>替换后的词语</td>
                        <td>词语分类</td>
                        <td>操作人</td>
                        <td style="width:15%">时间</td>
                        <td style="width:5%">操作</td>
                    </tr>
                    @foreach($words as $word)
                        <tr>
                            <td><input class="checkAll" type="checkbox" name="batch[]" value="{{ $word->id }}"></td>
                            <td>{{ $word->id }}</td>
                            <td>{{ $word->word }}</td>
                            <td>{{ $word->status }}</td>
                            <td>{{ $word->replacement }}</td>
                            <td>{{ $word->type}}</td>
                            <td><a href="{{ $word->lastOpUser->url }}" target="_blank">{{ $word->lastOpUser->username }}</a></td>
                            <td style="width:15%">{{ $word->last_op_time }}</td>
                            <td style="width:5%">
                                <span class="modify_info" data-name="{{$word->id}},{{$word->type}},{{$word->word}},{{$word->status}},{{$word->replacement}}"><i class="fa fa-pencil"></i></span>
                                <a data-url="{{ route('dashboard.word.destroy',['id'=>$word->id]) }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </form>
                <!-- Modal -->
                @if(count($words) > 0)
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
                                        {!! Form::open(['route' => ['dashboard.word.update', $word->id], 'method' => 'PUT']) !!}
                                        {!! Form::hidden('word[id]', $word->id, ['class' => 'form-control', 'id' => 'word-id']) !!}
                                        <fieldset>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.type.title') }}</label><label style="margin-left: 10px;">(*为必填项)</label>
                                            {!!  Form::select('word[type]', ['政治' => '政治', '广告' => '广告',
                                                '涉枪涉爆' => '涉枪涉爆', '网络招嫖' => '网络招嫖', '淫秽信息' => '淫秽信息',
                                                '默认' => '默认'], null, ['class' => 'form-control', 'id' => 'word-type'])!!}
                                        </div>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.content') }}*</label>
                                            <input type="text" id="word-word" name="word[word]" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.action.title') }}*</label>
                                            {!!  Form::select('word[status]', ['审核敏感词' => '审核敏感词','禁止敏感词' => '禁止敏感词',
                                                 '替换敏感词' => '替换敏感词'], null,
                                                 ['class' => 'form-control', 'id' => 'word-status'])!!}
                                        </div>
                                        <div class="form-group">
                                            <label>{{ trans('dashboard.words.replacement') }}</label>
                                            {!! Form::text('word[replacement]', '', ['class' => 'form-control', 'id' => 'word-replacement']) !!}
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
                @endif
                <!-- Modal -->
                <div class="text-right">
                    <!-- Pager -->
                    {!! $words->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ URL::asset('/js/words.js') }}"></script>
@stop
