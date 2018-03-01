@extends('layouts.default')

@section('title')
    {{ trans('hifone.threads.add') }}_@parent
@stop

@section('content')

    <div class="thread_create">

        <div class="col-md-9 main-col">
            <div class="panel panel-default corner-radius">
                <div class="panel-heading">{{ trans('hifone.threads.add') }}</div>
                <div class="panel-body">
                    <div class="reply-box form box-block">
                        @if (isset($thread))
                            {!! Form::model($thread, ['route' => ['thread.update', $thread->id], 'id' => 'thread_edit_form', 'class' => 'create_form', 'method' => 'patch']) !!}
                        @else
                            {!! Form::open(['route' => 'thread.store','id' => 'thread_create_form', 'class' => 'create_form', 'method' => 'post']) !!}
                        @endif

                        <div class="form-group">
                            {!! Form::text('thread[title]', isset($thread) ? $thread->title : null, ['class' => 'form-control', 'id' => 'thread_title', 'placeholder' => trans('hifone.threads.title')]) !!}
                        </div>

                        <div class="form-group">
                            <select class="form-control selectpicker" name="thread[sub_node_id]">
                                @foreach ($sections as $section)
                                    @if(isset($section->nodes))
                                        @foreach ($section->nodes as $node)
                                            <option disabled value="{{ $node->id }}" style="font-size:15px;font-weight:600">{{ $node->name }}</option>
                                            @foreach($node->subNodes as $subNode)
                                                <option value="{{ $subNode->id }}" {!! (Input::old('thread')['sub_node_id'] == $subNode->id || (isset($sub_node) && $sub_node->id==$subNode->id)) ? 'selected' : '' !!} >
                                                    -- {{ $subNode->name }}</option>
                                            @endforeach
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        @include('vendor.ueditor.assets')
                        <div class="form-group">
                            <label>{{ trans('hifone.threads.body') }}</label>
                            <script id="container" :name="thread[body]" type="text/plain">{!! isset($thread) ? $thread->body : null !!}</script>
                        </div>

                        <div class="form-group status-post-submit">
                            {!! Form::submit(trans('forms.publish'), ['class' => 'btn btn-primary col-xs-2', 'id' => 'thread-create-submit']) !!}
                            <div class="pull-right">
                                <small>{!! trans('hifone.photos.drag_drop') !!}</small>
                                <a href="/markdown" target="_blank"><i
                                            class="fa fa-lightbulb-o"></i> {{ trans('hifone.photos.markdown_desc') }}
                                </a>
                                </small>
                            </div>
                        </div>

                        <div class="box preview markdown-body" id="preview-box" style="display:none;"></div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-3 side-bar">

            <div class="panel panel-default corner-radius help-box">
                <div class="panel-heading text-center">
                    <h3 class="panel-title">{{ trans('hifone.threads.posting_tips.title') }}</h3>
                </div>
                <div class="panel-body">
                    <ul class="list">
                        <li>{{ trans('hifone.threads.posting_tips.pt1_title') }}
                            <p>{{ trans('hifone.threads.posting_tips.pt1_desc') }}</p>
                        </li>
                        <li>{{ trans('hifone.threads.posting_tips.pt2_title') }}
                            <p>{{ trans('hifone.threads.posting_tips.pt2_desc') }}</p>
                        </li>
                        <li>{{ trans('hifone.threads.posting_tips.pt3_title') }}
                            <p>{!! trans('hifone.threads.posting_tips.pt3_desc') !!}</p>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="panel panel-default corner-radius help-box">
                <div class="panel-heading text-center">
                    <h3 class="panel-title">{{ trans('hifone.threads.community_guidelines.title') }}</h3>
                </div>
                <div class="panel-body">
                    <ul class="list">
                        <li>{{ trans('hifone.threads.community_guidelines.cg1_title') }}
                            <p>{{ trans('hifone.threads.community_guidelines.cg1_desc') }}</p>
                        </li>
                        <li>{{ trans('hifone.threads.community_guidelines.cg2_title') }}
                            <p>{{ trans('hifone.threads.community_guidelines.cg2_desc') }}</p>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
<script type="text/javascript">
    if (Hifone.Config.role == '创始人' || Hifone.Config.role == '管理员'){
        var data =['fontsize','forecolor','backcolor','bold', 'italic', 'underline', 'strikethrough', 'blockquote', 'insertunorderedlist', 'insertorderedlist', 'justifyleft','justifycenter', 'justifyright',  'link', 'insertimage','attachment','fullscreen'];
    }else{
        var data =['fontsize','bold', 'italic', 'underline', 'strikethrough', 'blockquote', 'insertunorderedlist', 'insertorderedlist', 'justifyleft','justifycenter', 'justifyright',  'link', 'insertimage','fullscreen'];
    }
    var ue = UE.getEditor('container',{
        toolbars: [
            data
        ],
        elementPathEnabled: false,
        enableContextMenu: false,
        autoClearEmptyNode:true,
        wordCount:false,
        imagePopup:false,
        initialFrameHeight:350,
        autotypeset:{ indent: true,imageBlockLine: 'center' }
    });
    ue.ready(function() {
        ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
    });
</script>

@stop
