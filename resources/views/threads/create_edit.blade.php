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
                                        @foreach ($section->nodes as $item)
                                            @if($item->name == '公告活动' || $item->subNodes()->count() > 0)
                                                <option value="{{ $item->id }}" disabled style="font-size:15px;font-weight:600">{{ $item->name }}</option>
                                            @else
                                                <option value="{{ $item->id }}" style="font-size:15px;font-weight:600">{{ $item->name }}</option>
                                            @endif

                                            @if(isset($item->subNodes))
                                                @foreach($item->subNodes as $subItem)
                                                    <option value="{{ $subItem->id }}" {!! (Input::old('sub_node_id') == $subItem->id || (isset($subNode) && $subNode->id==$subItem->id)) ? 'selected' : '' !!} >
                                                        -- {{ $subItem->name }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                        </div>


                        <!-- editor start -->
                        {{--@include('threads.partials.editor_toolbar')--}}
                    <!-- end -->
                            {{--注释markdown编辑器，修改成新的编辑器用于发帖--}}

                        {{--<div class="form-group">--}}
                            {{--{!! Form::textarea('thread[body]', isset($thread) ? $thread->body_original : null, ['class' => 'post-editor form-control',--}}
                                                              {{--'rows' => 15,--}}
                                                              {{--'style' => "overflow:hidden",--}}
                                                              {{--'id' => 'body_field',--}}
                                                              {{--'placeholder' => trans('hifone.markdown_support')]) !!}--}}
                        {{--</div>--}}
                            @include('vendor.ueditor.assets')
                            <div class="form-group">
                                <label>{{ trans('hifone.threads.body') }}</label>
                                <script id="container" name="thread[body]" type="text/plain">{!! isset($thread) ? $thread->body : null !!}</script>
                            </div>

                        {{--<div class="form-group">--}}
                            {{--<select class="form-control js-tag-tokenizer" multiple="multiple" name="thread[tags][]">--}}
                                {{--@if(isset($thread))--}}
                                    {{--@foreach($thread->tags as $tag)--}}
                                        {{--<option selected="selected">{{ $tag->name }}</option>--}}
                                    {{--@endforeach--}}
                                {{--@endif--}}
                            {{--</select>--}}
                            {{--<small>--}}
                                {{--{{ trans('hifone.tags.tags_help') }}--}}
                            {{--</small>--}}
                        {{--</div>--}}

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

            {{--@if ( $node )--}}
                {{--<div class="panel panel-default corner-radius help-box">--}}
                    {{--<div class="panel-heading text-center">--}}
                        {{--<h3 class="panel-title">{{ trans('hifone.nodes.current') }} : {{ $node->name }}</h3>--}}
                    {{--</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--{{ $node->description }}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--@endif--}}

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
