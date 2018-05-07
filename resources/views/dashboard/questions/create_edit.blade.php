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

        <div class="row">
            <div class="col-md-12">
                {!! Form::model($question, ['route' => ['dashboard.questions.update', $question->id], 'id' => 'thread-edit-form', 'method' => 'patch']) !!}
                <input type="hidden" name="id" value={{$question->id}}>
                <fieldset>
                    <div class="form-group">
                        <label for="question-title">问题标题</label>
                        <input type="text" class="form-control" name="question[title]" id="question-title" required value="{{ isset($question) ? $question->title : null }}">
                    </div>


                    <div class="form-group">
                        <label>问题内容</label>
                        <script id="container" name="question[body]" type="text/plain">{!!  isset($question) ? $question->body : null !!}</script>
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
@stop