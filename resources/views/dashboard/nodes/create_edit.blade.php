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
                <fieldset>
                    {{--插入图片相关代码--}}
                    @include('dashboard.nodes.upload_image')
                    {{--板块相关信息--}}
                    <div>
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
                        {{--板块描述--}}
                        <div class="form-group">
                            <label>{{ trans('dashboard.nodes.description') }}</label>
                            {!! Form::textarea('node[description]', isset($node) ? $node->description : null , ['class' => 'form-control', 'required', 'rows' => 5]) !!}
                        </div>
                    </div>
                    {{--添加版主--}}
                    <div>
                        <label>添加版主:</label>
                        <el-select v-model="nodeModerators" multiple placeholder="添加版主">
                            <el-option
                                    v-for="moderator in moderators"
                                    :key="moderator.id"
                                    :label="moderator.username"
                                    :value="moderator.id">
                            </el-option>
                        </el-select>
                        <input  type="hidden" class="form-control" :value="nodeModerators" name="nodeModerators">
                    </div>
                    {{--添加实习版主--}}
                    <div>
                        <label>添加实习版主:</label>
                        <el-select v-model="nodePraModerators" multiple placeholder="添加实习版主">

                            <el-option
                                    v-for="praModerator in praModerators"
                                    :key="praModerator.id"
                                    :label="praModerator.username"
                                    :value="praModerator.id">
                            </el-option>
                        </el-select>
                        <input type="hidden" class="form-control" :value="nodePraModerators" name="nodePraModerators">
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
                    valueFeedback: "{{  $node->is_feedback or 0  }}",
                    nodeModerators: {!! $nodeModerators or json_encode([])  !!} ,
                    nodePraModerators: {!! $nodePraModerators or json_encode([])  !!} ,
                    praModerators: {!! $praModerators !!},
                    moderators: {!! $moderators !!},
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
        .el-select {
            width:100%;
        }
    </style>

@stop
