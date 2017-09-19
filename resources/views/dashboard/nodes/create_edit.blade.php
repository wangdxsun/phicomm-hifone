@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper" id="app">
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
                    <div class="col-xs-4">
                        <label>{{ trans('dashboard.nodes.icon.hot') }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="handleAvatarSuccess1">
                            <img v-if="imageUrl" :src="imageUrl" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="imageUrl" type="hidden" placeholder="请输入内容" name="node[icon]" value="{{ $node->icon or null }}"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ trans('dashboard.nodes.icon.list') }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="handleAvatarSuccess2">
                            <img v-if="imageListUrl" :src="imageListUrl" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageListUrl" placeholder="请输入内容" type="hidden" name="node[icon_list]" value="{{ $node->icon_list or null }}"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ trans('dashboard.nodes.icon.detail') }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="handleAvatarSuccess3">
                            <img v-if="imageDetailUrl" :src="imageDetailUrl" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageDetailUrl" placeholder="请输入内容" type="hidden" name="node[icon_detail]" value="{{ $node->icon_detail or null }}"></el-input>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ trans('dashboard.nodes.name') }}</label>
                     {!! Form::text('node[name]', isset($node) ? $node->name : null, ['class' => 'form-control']) !!}
                </div>

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

                <div class="form-group">
                    <label>{{ trans('dashboard.nodes.prompt.node') }}</label>
                    <el-tooltip  placement="hidden" >
                        <el-switch
                                v-model="valuePrompt"
                                on-color="#13ce66"
                                off-color="#ff4949"
                                on-value=1
                                off-value=0>
                        </el-switch>
                    </el-tooltip>
                    <el-input  v-model="valuePrompt" placeholder="请输入内容" type="hidden" name="node[is_prompt]" value="{{ $node->prompt or null }}"></el-input>
                </div>
                <div class="form-group">
                    <label>{{ trans('dashboard.nodes.prompt.nodeDetail') }}</label>
                    {!! Form::textarea('node[prompt]', isset($node) ? $node->prompt : null , ['class' => 'form-control', 'rows' => 3]) !!}
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
<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                imageUrl: '',
                imageListUrl: '',
                imageDetailUrl: '',
                valuePrompt:'',
            };
        },
        methods: {
            handleAvatarSuccess1: function (res) {
                this.imageUrl = res.filename;
            },
            handleAvatarSuccess2: function (res) {
                this.imageListUrl = res.filename;
            },
            handleAvatarSuccess3: function (res) {
                this.imageDetailUrl = res.filename;
            },
        },
        mounted: function () {
            this.imageUrl = "{{ $node->icon or null }}"
            this.imageListUrl = "{{ $node->icon_list or null }}"
            this.imageDetailUrl = "{{ $node->icon_detail or null }}"
            this.valuePrompt = "{{  $node->is_prompt or 0  }}"
        }
    })
</script>
<style>
    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .avatar-uploader .el-upload:hover {
        border-color: #20a0ff;
    }
    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 178px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    .el-avatar {
        width: 178px;
        height: 178px;
        display: block;
    }
    .el-upload__input {
        display: none!important;
    }
</style>

@stop
