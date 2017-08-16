@extends('layouts.dashboard')

@section('content')
    @include('vendor.ueditor.assets')
<div class="content-wrapper">
    <div class="header sub-header" id="general">
        <span class="uppercase">
            {{ trans('dashboard.replies.edit.title') }}
        </span>
    </div>
     @if(isset($sub_menu))
    @include('dashboard.partials.sub-nav')
    @endif
    <div class="row">
        <div class="col-md-12">
            @include('partials.errors')
            @if(isset($reply))
                {!! Form::model($reply, ['route' => ['dashboard.reply.update', $reply->id], 'id' => 'reply-edit-form', 'method' => 'patch']) !!}
                <input type="hidden" name="id" value={{$reply->id}}>
            @else
                {!! Form::open(['route' => 'dashboard.reply.store','id' => 'reply-create-form', 'method' => 'post']) !!}
            @endif
                <fieldset>
                    <div class="form-group">
                        <label>{{ trans('hifone.replies.body') }}</label>
                        <script id="container" name="reply[body]" type="text/plain">{!!  isset($reply) ? $reply->body : null !!}</script>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label>{{ trans('hifone.replies.body') }}</label>--}}
                        {{--<div class='markdown-control'>--}}
                            {{--<textarea name="reply[body]" class="form-control" rows="10">{{ isset($reply) ? $reply->body_original : null }}</textarea>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </fieldset>

                <div class='form-group'>
                    <div class='btn-group'>
                        <button type="submit" class="btn btn-success">{{ trans('forms.update') }}</button>
                        <a class="btn btn-default" href="{{ route('dashboard.reply.index') }}">{{ trans('forms.cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UE.getEditor('container',{
        toolbars: [
            ['fontsize','forecolor','backcolor','bold', 'italic', 'underline', 'strikethrough', 'blockquote', 'insertunorderedlist', 'insertorderedlist', 'justifyleft','justifycenter', 'justifyright',  'link', 'insertimage', 'attachment','fullscreen']
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