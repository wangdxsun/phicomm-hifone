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
                {{--安卓端版块图片--}}
                <div class="form-group row">
                    <div class="col-xs-4">
                        <label>{{ '安卓端首页热门版块图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageUrlAndroidHandle">
                            <img v-if="imageUrlAndroid" :src="imageUrlAndroid" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="imageUrlAndroid" type="hidden" name="node[android_icon]"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ '安卓端版块列表图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageListUrlAndroidHandle">
                            <img v-if="imageListUrlAndroid" :src="imageListUrlAndroid" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageListUrlAndroid" placeholder="请输入内容" type="hidden" name="node[android_icon_list]"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ '安卓端版块详情页图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageDetailUrlAndroidHandle">
                            <img v-if="imageDetailUrlAndroid" :src="imageDetailUrlAndroid" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageDetailUrlAndroid" placeholder="请输入内容" type="hidden" name="node[android_icon_detail]"></el-input>
                    </div>
                </div>
                {{--IOS端版块图片--}}
                <div class="form-group row">
                    <div class="col-xs-4">
                        <label>{{ 'IOS端首页热门版块图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageUrlIosHandle">
                            <img v-if="imageUrlIos" :src="imageUrlIos" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="imageUrlIos" type="hidden" name="node[ios_icon]"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ 'IOS端版块列表图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageListUrlIosHandle">
                            <img v-if="imageListUrlIos" :src="imageListUrlIos" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageListUrlIos" placeholder="请输入内容" type="hidden" name="node[ios_icon_list]"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ 'IOS端版块详情页图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageDetailUrlIosHandle">
                            <img v-if="imageDetailUrlIos" :src="imageDetailUrlIos" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageDetailUrlIos" type="hidden" name="node[ios_icon_detail]"></el-input>
                    </div>
                </div>
                {{--H5端版块图片--}}
                <div class="form-group row">
                    <div class="col-xs-4">
                        <label>{{ 'H5端首页热门版块图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageUrlHandle">
                            <img v-if="imageUrl" :src="imageUrl" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="imageUrl" type="hidden" name="node[icon]"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ 'H5端版块列表图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageListUrlHandle">
                            <img v-if="imageListUrl" :src="imageListUrl" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageListUrl" type="hidden" name="node[icon_list]"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ 'H5端版块详情页图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageDetailUrlHandle">
                            <img v-if="imageDetailUrl" :src="imageDetailUrl" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            <el-input  v-model="imageDetailUrl" type="hidden" name="node[icon_detail]"></el-input>
                        </el-upload>
                    </div>
                </div>
                {{--WEB端版块图片--}}
                <div class="form-group row">
                    <div class="col-xs-4">
                        <label>{{ 'WEB端版块详情页图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageDetailUrlWebHandle">
                            <img v-if="imageDetailUrlWeb" :src="imageDetailUrlWeb" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input v-model="imageDetailUrlWeb" type="hidden" name="node[web_icon_detail]"></el-input>
                    </div>

                    <div class="col-xs-4">
                        <label>{{ 'WEB端右侧列表页图片' }}</label><br>
                        <el-upload
                                class="avatar-uploader"
                                action="/upload_image"
                                :show-file-list="false"
                                :on-success="imageListUrlWebHandle">
                            <img v-if="imageListUrlWeb" :src="imageListUrlWeb" class="el-avatar">
                            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                        </el-upload>
                        <el-input  v-model="imageListUrlWeb" type="hidden" name="node[web_icon_list]"></el-input>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ trans('dashboard.nodes.name') }}</label>
                    {!! Form::text('node[name]', isset($node) ? $node->name : null, ['class' => 'form-control', 'required']) !!}
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
                {{--版块描述--}}
                <div class="form-group">
                    <label>{{ trans('dashboard.nodes.description') }}</label>
                    {!! Form::textarea('node[description]', isset($node) ? $node->description : null , ['class' => 'form-control', 'required', 'rows' => 5]) !!}
                </div>
                {{--添加版主--}}
                <div class="form-group">
                    <label >{{ trans('dashboard.nodes.moderator.add') }}</label>
                    <input type="text" name="user[name]" class="form-control"
                    @if (isset($user['name']))
                    value="{{ $user['name'] }}"
                    @endif >
                </div>

                {{--添加实习版主--}}
                <div class="form-group">
                    <label >{{ trans('dashboard.nodes.pre-moderator.add') }}</label>
                    <input type="text" name="user[name]" class="form-control"
                           @if (isset($user['name']))
                           value="{{ $user['name'] }}"
                            @endif >
                </div>


                <div class="form-group">
                    <label>{{ '主版块发帖时是否对普通用户开放' }}</label>
                    <el-tooltip  placement="hidden" >
                        <el-switch
                                v-model="valueShow"
                                on-color="#13ce66"
                                off-color="#ff4949"
                                on-value=1
                                off-value=0>
                        </el-switch>
                    </el-tooltip>
                    <el-input  v-model="valueShow" placeholder="请输入内容" type="hidden" name="node[is_show]"></el-input>
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
                    <el-input  v-model="valuePrompt" placeholder="请输入内容" type="hidden" name="node[is_prompt]"></el-input>

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
                //安卓端版块图片
                imageUrlAndroid: "{{ isset($node) ? ($node->android_icon) : (Input::old('node')['android_icon']) }}",
                imageListUrlAndroid: "{{ isset($node) ? ($node->android_icon_list) : (Input::old('node')['android_icon_list']) }}",
                imageDetailUrlAndroid: "{{ isset($node) ? ($node->android_icon_detail) : (Input::old('node')['android_icon_detail']) }}",
                //IOS端版块图片
                imageUrlIos: "{{ isset($node) ? ($node->ios_icon) : (Input::old('node')['ios_icon']) }}",
                imageListUrlIos: "{{ isset($node) ? ($node->ios_icon_list) : (Input::old('node')['ios_icon_list']) }}",
                imageDetailUrlIos: "{{ isset($node) ? ($node->ios_icon_detail) : (Input::old('node')['ios_icon_detail']) }}",
                //H5端版块图片
                imageUrl: "{{ isset($node) ? ($node->h5_icon) : (Input::old('node')['icon']) }}",
                imageListUrl: "{{ isset($node) ? ($node->h5_icon_list) : (Input::old('node')['icon_list']) }}",
                imageDetailUrl: "{{ isset($node) ? ($node->h5_icon_detail) : (Input::old('node')['icon_detail']) }}",
                //Web端版块图片
                imageDetailUrlWeb: "{{ isset($node) ? ($node->web_icon_detail) : (Input::old('node')['web_icon_detail']) }}",
                imageListUrlWeb: "{{ isset($node) ? ($node->web_icon_list) : (Input::old('node')['web_icon_list']) }}",
                valuePrompt: "{{  $node->is_prompt or 0  }}",
                valueShow: "{{  $node->is_show or 1  }}",
            };
        },
        methods: {
            //安卓端版块图片
            imageUrlAndroidHandle: function (res) {
                this.imageUrlAndroid = res.filename;
            },
            imageListUrlAndroidHandle: function (res) {
                this.imageListUrlAndroid = res.filename;
            },
            imageDetailUrlAndroidHandle: function (res) {
                this.imageDetailUrlAndroid = res.filename;
            },

            //IOS端版块图片
            imageUrlIosHandle: function (res) {
                this.imageUrlIos = res.filename;
            },
            imageListUrlIosHandle: function (res) {
                this.imageListUrlIos = res.filename;
            },
            imageDetailUrlIosHandle: function (res) {
                this.imageDetailUrlIos = res.filename;
            },

            //H5端版块图片
            imageUrlHandle: function (res) {
                this.imageUrl = res.filename;
            },
            imageListUrlHandle: function (res) {
                this.imageListUrl = res.filename;
            },
            imageDetailUrlHandle: function (res) {
                this.imageDetailUrl = res.filename;
            },

            //Web端版块图片
            imageDetailUrlWebHandle: function (res) {
                this.imageDetailUrlWeb = res.filename;
            },
            imageListUrlWebHandle: function (res) {
                this.imageListUrlWeb = res.filename;
            },

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
