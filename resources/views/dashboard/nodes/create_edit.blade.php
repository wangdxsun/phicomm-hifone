@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header" id="nodes">
        <span class="uppercase">
            {{ trans(isset($node) ? 'dashboard.nodes.edit.title' : 'dashboard.nodes.add.title') }}
        </span>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @if(isset($node))
            {!! Form::model($node, ['route' => ['dashboard.node.update', $node->id], 'method' => 'patch', 'class' => 'create_form']) !!}
            @else
            {!! Form::open(['route' => 'dashboard.node.store', 'method' => 'post', 'class' => 'create_form']) !!}
            @endif
            @include('partials.errors')
                <fieldset>
                <div class="form-group">
                    <label>icon</label><br>
                    <a href="javascript:void(0);" class="btn-upload">
                        <img src="{{ isset($node) ? $node->icon : '/images/blank.png' }}" class="ImagePreviewBox" style="max-height: 200px; max-width: 300px; cursor: pointer;">
                    </a>
                    <input type="file" name="file" class="input-file hide">
                    <input id="imageUrl" type="hidden" class="form-control" name="node[icon]" value="{{ $node->icon or null }}">
                </div>
                <div class="form-group">
                    <label>{{ trans('dashboard.nodes.name') }}</label>
                     {!! Form::text('node[name]', isset($node) ? $node->name : null, ['class' => 'form-control']) !!}
                </div>
                {{--<div class="form-group">--}}
                    {{--<label>{{ trans('dashboard.nodes.slug') }}</label>--}}
                    {{--{!! Form::text('node[slug]', isset($node) ? $node->slug : null, ['class' => 'form-control']) !!}--}}
                {{--</div>--}}
                @if($sections->count() > 0)
                <div class="form-group">
                    <label>{{ trans('dashboard.sections.sections') }}</label>
                    <select name="node[section_id]" class="form-control">
                        @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ option_is_selected([$section, 'section_id', isset($node) ? $node : null]) }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="node[section_id]" value="0">
                @endif
                <div class="form-group">
                <label>{{ trans('dashboard.nodes.description') }}</label>
                {!! Form::textarea('node[description]', isset($node) ? $node->description : null , ['class' => 'form-control', 'rows' => 5]) !!}
                </div>
                <div clas="form-group">
                    <label >{{ trans('dashboard.nodes.moderator.add') }}</label>
                    <input type="text" name="user[name]" class="form-control"
                           @if (isset($user['name']))
                           value="{{ $user['name'] }}"
                            @endif >
                </div>
                <div class="form-group">
                    <label>{{ trans('dashboard.nodes.moderator.type') }}</label>
                    <select name="moderator[role]" class="form-control" >
                            <option value="3">版主</option>
                            <option value="12">实习版主</option>
                    </select>
                </div>
                @if(isset($node))
                    <div>
                        <label>{{ trans('dashboard.nodes.moderator.list') }}</label>
                        <table class="table table-bordered table-striped table-condensed">
                            <tbody>
                            <tr class="head">
                                <td>版主用户名</td>
                                <td>所在组别</td>
                                <td>操作</td>
                            </tr>
                            @foreach($node->moderators as $moderator)
                                <tr>
                                    <td>{{ $moderator->user->username }}</td>
                                    <td>{{ $moderator->user->role }}</td>
                                    <td>
                                        <a data-url="/dashboard/node/{{ $moderator->id }}/audit/to/trash" data-method="post" class="need-reason" title="删除"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                @endif

                </fieldset>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url('dashboard.node.index') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop