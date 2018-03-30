@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                <i class="fa fa-file-text-o"></i> {{ $sub_header }}
            </span>
            <div class="clearfix"></div>
        </div>
        <div class="uppercase pull-right">
            <span class="uppercase">
                截止当前列表的帖子总数：{{ $threadCount }}
            </span>
        </div>
    @if(isset($sub_nav))
        @include('dashboard.partials.sub-nav')
    @endif
        <div class="row" id="app">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" name="thread[id]" class="form-control" placeholder="帖子ID" style="width: 100px;"
                                   @if (isset($search['id']))
                                      value="{{ $search['id'] }}"
                                   @endif >
                            <input type="text" name="thread[title]" class="form-control" placeholder="帖子标题" style="width: 160px;"
                                   @if (isset($search['title']))
                                        value="{{ $search['title'] }}"
                                   @endif >
                            <input type="text" name="thread[user_id]" class="form-control" placeholder="发帖人" style="width: 100px;"
                                   @if (isset($search['user_id']))
                                        value="{{ $search['user_id'] }}"
                                   @endif >
                        </div>
                        <select class="form-control" name="thread[node_id]">
                            <option value = "" selected>全部版块</option>
                            @foreach ($sections as $section)
                                <optgroup label="{{ $section->name }}">
                                    @if(isset($section->nodes))
                                        @foreach ($section->nodes as $node)
                                            <option value="{{ $node->id }}" >{{ $node->name }}</option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach
                        </select>
                        <select class="form-control" name="thread[sub_node_id]">
                            <option value = "" selected>全部子版块</option>
                            @foreach ($nodes as $node)
                                <optgroup label="{{ $node->name }}">
                                    @if(isset($node->subNodes))
                                        @foreach ($node->subNodes as $subNode)
                                            <option value="{{ $subNode->id }}" >{{ $subNode->name }}</option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach
                        </select>
                        <el-date-picker type="datetime" placeholder="开始时间" v-model="date_start"></el-date-picker>
                        <el-date-picker type="datetime" placeholder="结束时间" v-model="date_end"></el-date-picker>
                        <select class="form-control " name="thread[orderType]">
                            <option value="" selected>排列方式</option>
                            @foreach ($orderTypes as $key => $orderType)
                                <option value="{{ $key }}">{{ $orderType }}</option>
                            @endforeach
                        </select>
                        <select class="form-control " name="thread[channel]">
                            <option value="" selected>发帖来源</option>
                            <option value="0">社区</option>
                            <option value="-1">意见反馈</option>
                        </select>
                        <button class="btn btn-default">搜索</button>
                        <el-input :value="date_end_str" placeholder="请输入内容"  type="hidden" resize=" both"  style="width: 60px; height: 10px;" name="thread[date_end]"></el-input>
                        <el-input :value="date_start_str" placeholder="请输入内容"  type="hidden" resize=" both"  style="width: 60px; height: 10px;" name="thread[date_start]"></el-input>
                    </form>
                </div>
                <form action="{{ URL('dashboard/thread/batchMove')}}"  id="batchMoveThread" method="POST">
                    {!! csrf_field() !!}
                    <div class="btn btn-default move_thread" onclick="moveThread()">批量移动</div>
                    <table class="table table-bordered table-striped table-condensed">
                        <tbody>
                        <tr class="head">
                            <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                            <td style="width: 70px;">#</td>
                            <td style="width: 180px;">标题</td>
                            <td style="width: 80px;">主版块</td>
                            <td style="width: 80px;">子版块</td>
                            <td style="width: 80px;">发帖人</td>
                            <td style="width: 60px;">来源</td>
                            <td style="width: 90px;">用户设备信息</td>
                            <td style="width: 90px;">IP地址</td>
                            <td style="width: 60px;">热度值</td>
                            <td style="width: 50px;">回帖</td>
                            <td style="width: 50px;">查看</td>
                            <td style="width: 90px;">发帖时间</td>
                            <td style="width: 80px;">操作人</td>
                            <td style="width: 90px;">操作时间</td>
                            <td style="width: 120px;">操作</td>
                        </tr>
                        @foreach($threads as $thread)
                            <tr>
                                <td><input class="checkAll" type="checkbox" name="batch[]" value="{{ $thread->id }}"></td>
                                <td>{{ $thread->id }}</td>
                                <td><a target="_blank" href="{{ $thread->url }}">{{ $thread->title }}</a></td>
                                <td><a href="{{ $thread->node->url }}" target="_blank">{{ $thread->node->name }}</a></td>
                                <td>{{ $thread->subNode->name }}</td>
                                <td>
                                    @if(!isset($thread->user))
                                        {{ '' }}
                                    @else
                                        <a href="{{ $thread->user->url }}" target="_blank">{{ $thread->user->username }}</a>
                                    @endif
                                </td>
                                <td>{{ $thread->channel == 0 ? "社区" : "意见反馈" }}</td>
                                <td>
                                    @if(sizeof($thread->dev_info) > 0)
                                        <a data-toggle="collapse" href="#dev_info{{ $thread->id }}" aria-expanded="false">查看更多</a>
                                        <div class="collapse well" id="dev_info{{ $thread->id }}" style="min-width: 230px">
                                            @foreach($thread->dev_info as $info)
                                                @foreach($info as $key => $item)
                                                    {{$key." : ".$item}}<br>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $thread->ip }}</td>
                                <td>{{ $thread->heat }}</td>
                                <td>{{ $thread->reply_count }}</td>
                                <td>{{ $thread->view_count }}</td>
                                <td>{{ $thread->created_time }}</td>
                                <td>{{ $thread->lastOpUser->username }}</td>
                                <td>{{ $thread->last_op_time }}</td>
                                <td>
                                    <a data-url="/dashboard/thread/{{$thread->id}}/node/pin" data-method="post" title="版块置顶"><i class="{{ $thread->nodePin }}"></i></a>
                                    <a data-url="/dashboard/thread/{{$thread->id}}/excellent" data-method="post" title="精华"><i class="{{ $thread->excellent }}"></i></a>
                                    <a data-url="/dashboard/thread/{{$thread->id}}/pin" data-method="post" title="置顶"><i class="{{ $thread->pin }}"></i></a>
                                    <a data-url="/dashboard/thread/{{ $thread->id }}/heat_offset" get-url="/dashboard/thread/{{ $thread->id }}/heat_offset" data-title="修改热度值偏移" data-method="post" class="getAndSet" title="提升"><i class="fa fa-level-up"></i></a>
                                    <a data-url="/dashboard/thread/{{$thread->id}}/sink" data-method="post" title="下沉"><i class="{{ $thread->sink }}"></i></a>
                                    <a href="/dashboard/thread/{{ $thread->id }}/edit"><i class="fa fa-pencil" title="编辑"></i></a>
                                    <a data-url="/dashboard/thread/{{ $thread->id }}/index/to/trash" data-title="帖子移入垃圾站" data-method="post" class="need-reason" title="删除"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="modal fade" id="moveThreadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="content-wrapper">
                                    <div class="header sub-header">
                                        <span class="uppercase">移动帖子</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <select class="form-control" name="thread[sub_node_id]">
                                                @foreach ($nodes as $node)
                                                    <optgroup label="{{ $node->name }}">
                                                        @if(isset($node->subNodes))
                                                            @foreach ($node->subNodes as $subNode)
                                                                <option value="{{ $subNode->id }}" >{{ $subNode->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            <hr>
                                            <div class="form-group">
                                                {!! Form::submit('保存',['class'=>'btn btn-success', 'id' => 'commit']) !!}
                                                {!! Form::button('取消',['class'=>'btn btn-default', 'id' => 'cancel']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="text-right">
                {!! $threads->appends(Request::except('page', '_pjax'))->render() !!}
            </div>
        </div>
    </div>
<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                date_start:"",
                date_end:"",
            };
        },
        computed: {
            date_start_str: function () {
                return this.date_start === '' ? '' : this.date_start.format('yyyy-MM-dd hh:mm:ss');
            },
            date_end_str: function () {
                return this.date_end === '' ? '' : this.date_end.format('yyyy-MM-dd hh:mm:ss');
            }
        }
    });
</script>
<script type="text/javascript" src="{{ URL::asset('/js/moveThread.js') }}"></script>
@stop
