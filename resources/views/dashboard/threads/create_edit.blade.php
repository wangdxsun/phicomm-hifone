@extends('layouts.dashboard')

@section('content')
    @include('vendor.ueditor.assets')
<div class="content-wrapper">
    <div class="header sub-header" id="general">
        <span class="uppercase">
            {{ trans('dashboard.threads.edit.title') }}
        </span>
    </div>
     @if(isset($sub_nav))
    @include('dashboard.partials.sub-nav')
    @endif
    <div class="row">
        <div class="col-md-12">
            @include('partials.errors')
            @if(isset($thread))
                {!! Form::model($thread, ['route' => ['dashboard.thread.update', $thread->id], 'id' => 'thread-edit-form', 'method' => 'patch']) !!}
                <input type="hidden" name="id" value={{$thread->id}}>
            @else
                {!! Form::open(['route' => 'dashboard.thread.store','id' => 'thread-create-form', 'method' => 'post']) !!}
            @endif
                <fieldset>
                    <div class="form-group">
                        <label for="thread-title">{{ trans('hifone.threads.title') }}</label>
                        <input type="text" class="form-control" name="thread[title]" id="thread-title" required value="{{ isset($thread) ? $thread->title : null }}">
                    </div>
                    <div class="form-group">
                        <select class="selectpicker form-control" name="thread[sub_node_id]" >
                            @foreach ($nodes as $node)
                                <optgroup label="{{ $node->name }}">
                                    @foreach($node->subNodes as $subNode)
                                        <option value={{ $subNode->id }} {!! (Input::old('sub_node_id') == $subNode->id || (isset($thread) && $thread->subNode->id == $subNode->id)) ? 'selected' : '' !!} >
                                            -- {{ $subNode->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('hifone.threads.body') }}</label>
                        <script id="container" name="thread[body]" type="text/plain">{!!  isset($thread) ? $thread->body : null !!}</script>
                    </div>
                </fieldset>

                <div class='form-group'>
                    <div class='btn-group'>
                        <button type="submit" class="btn btn-success" >{{ trans('forms.update') }}</button>
                        <a class="btn btn-default" href="{!! ($thread->status == 0) ? route('dashboard.thread.index') : route('dashboard.thread.audit') !!} ">{{ trans('forms.cancel') }}</a>
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