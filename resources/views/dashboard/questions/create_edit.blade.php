@extends('layouts.dashboard')

@section('content')
    @include('vendor.ueditor.assets')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        @if(isset($sub_menu))
            @foreach($sub_menu as $key => $item)
                @if ($key == $current_menu && array_get($item, 'sub_nav'))
                    @include('dashboard.partials.sub-nav', ['sub_nav' => $item['sub_nav']])
                @endif
            @endforeach
        @endif

        <div class="row" id="app">
            <div class="col-md-12">
                {!! Form::model($question, ['route' => ['dashboard.question.update', $question->id], 'id' => 'thread-edit-form', 'method' => 'patch']) !!}
                <fieldset>
                    <div class="form-group">
                        <label for="question-title">问题标题</label>
                        <input type="text" class="form-control" name="question[title]" id="question-title" required value="{{ isset($question) ? $question->title : null }}">
                    </div>

                    <div>
                        <label>{{ '问题类型' }}</label>
                        <el-select v-model="questionTags" id="question-tags" multiple placeholder="选择标签">
                            <el-option-group
                                    v-for="questionTagType in questionTagTypes"
                                    :key="questionTagType.id"
                                    :label="questionTagType.display_name">
                                <el-option
                                        v-for="tag in questionTagType.tags"
                                        :key="tag.id"
                                        :label="tag.name"
                                        :value="tag.id">
                                </el-option>
                            </el-option-group>
                        </el-select>
                        <el-input :value="questionTags"  type="hidden" resize="both"  style="width: 60px; height: 10px;" name="question[questionTags]"></el-input>
                    </div>



                    <div class="form-group">
                        <label>问题内容</label>
                        <script id="container" name="question[body]" type="text/plain">{!!  old('question.body') ?: (isset($question) ? $question->body : null) !!}</script>
                    </div>
                </fieldset>

                <div class='form-group'>
                    <div class='btn-group'>
                        <button type="submit" class="btn btn-success" >{{ trans('forms.update') }}</button>
                        <a class="btn btn-default" href="{!! ($question->status == 0) ? route('dashboard.questions.index') : route('dashboard.questions.audit') !!} ">{{ trans('forms.cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        var ue = UE.getEditor('container',{
            toolbars: [
                ['fontsize','forecolor','backcolor', 'bold', 'italic', 'underline', 'strikethrough', 'blockquote', 'insertunorderedlist',
                    'insertorderedlist', 'justifyleft','justifycenter', 'justifyright',  'link', 'insertimage', 'attachment','insertvideo','fullscreen']
            ],
            elementPathEnabled: false,
            enableContextMenu: false,
            autoClearEmptyNode:true,
            wordCount:false,
            imagePopup:false,
            initialFrameHeight:500,
            autotypeset:{ indent: true,imageBlockLine: 'center' }
        });
        ue.ready(function() {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
        });
    </script>

    <script>
        new Vue({
            el: '#app',
            data: function () {
                return {
                    questionTagTypes: {!! $questionTagTypes !!},
                    questionTags: {!! $questionTags or json_encode([])  !!} ,
                };
            },
        })
    </script>

    <style>
        .el-select {
            width:100%;
        }
    </style>
@stop