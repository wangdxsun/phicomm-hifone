@extends('layouts.dashboard')
@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper" id="app">
        <div class="header sub-header" id="nodes">
        <span class="uppercase">
            {{ trans(isset($subNode) ? 'dashboard.nodes.edit.sub_title' : 'dashboard.nodes.add.sub_title') }}
        </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if(isset($subNode))
                    {!! Form::model($subNode, ['route' => ['dashboard.subNode.update', $subNode->id], 'method' => 'patch', 'class' => 'create_form']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.subNode.store', 'method' => 'post', 'class' => 'create_form']) !!}
                @endif
                @include('partials.errors')
                <fieldset>
                    <div class="form-group">
                        <label>{{ trans('dashboard.nodes.sub_name') }}</label>
                        {!! Form::text('subNode[name]', isset($subNode) ? $subNode->name : null, ['class' => 'form-control']) !!}
                    </div>
                    @if($nodes->count() > 0)
                        <div class="form-group">
                            <label>{{ trans('dashboard.nodes.name') }}</label>
                            <select name="subNode[node_id]" class="form-control">
                                @foreach($nodes as $node)
                                    <option value="{{ $node->id }}" {{ option_is_selected([$node, 'node_id', isset($subNode) ? $subNode : null]) }}>{{ $node->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="subNode[node_id]" value="0">
                    @endif
                    <div class="form-group">
                        <label>{{ trans('dashboard.nodes.description') }}</label>
                        {!! Form::textarea('node[description]', isset($subNode) ? $subNode->description : null , ['class' => 'form-control', 'rows' => 5]) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.nodes.prompt.subNode') }}</label>
                        <el-tooltip :content="'子版块: ' + valuePrompt" placement="top" >
                            <el-switch
                                v-model="valuePrompt"
                                on-color="#13ce66"
                                off-color="#ff4949"
                                on-value="打开"
                                off-value="关闭">
                            </el-switch>
                        </el-tooltip>
                        <el-input  v-model="valuePrompt" placeholder="请输入内容" type="hidden" name="subNode[is_prompt]" value="{{ $subNode->prompt or null }}"></el-input>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('dashboard.nodes.prompt.subNodeDetail') }}</label>
                        {!! Form::textarea('subNode[prompt]', isset($subNode) ? $subNode->prompt : null , ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url('dashboard.subNode.index') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                valuePrompt:'打开',
            };
        }
    })
</script>
@stop