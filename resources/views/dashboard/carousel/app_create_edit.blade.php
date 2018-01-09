@extends('layouts.dashboard')
@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper" id="app">
        <div class="row">
            <div class="col-sm-12">
                @include('partials.errors')
                @if(isset($carousel))
                    {!! Form::model($carousel, ['route' => ['dashboard.carousel.update.app', $carousel->id], 'method' => 'patch', 'class' => 'create_form']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.carousel.store.app','id' => 'carousel-create-form', 'method' => 'post', 'class' => 'create_form']) !!}
                @endif
                <fieldset>
                    <div class="form-group">
                        <div class="col-xs-6">
                            <label>{{ 'Android端图片:' }}</label><br>
                            <el-upload
                                    class="avatar-uploader"
                                    action="/upload_image"
                                    :show-file-list="false"
                                    :on-success="imageUrlAndroidHandle">
                                <img v-if="imageUrlAndroid" :src="imageUrlAndroid" class="el-avatar">
                                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <el-input v-model="imageUrlAndroid" type="hidden" name="carousel[android_icon]"></el-input>
                            <el-button class="btn-danger" size="medium" @click="clearImageUrlAndroid">清空图片</el-button>
                        </div>

                        <div class="col-xs-6">
                            <label>{{ 'iOS端图片:' }}</label><br>
                            <el-upload
                                    class="avatar-uploader"
                                    action="/upload_image"
                                    :show-file-list="false"
                                    :on-success="imageUrlIosHandle">
                                <img v-if="imageUrlIos" :src="imageUrlIos" class="el-avatar">
                                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <el-input  v-model="imageUrlIos" placeholder="请输入内容" type="hidden" name="carousel[ios_icon]"></el-input>
                            <el-button class="btn-danger" size="medium" @click="clearImageUrlIos">清空图片</el-button>

                        </div>
                    </div>
                    <div class="form-group">
                        <label>描述</label>
                        {!! Form::textarea('carousel[description]', isset($carousel) ? $carousel->description : null , [
                        'class' => 'form-control', 'rows' => 5, 'style' => "overflow:hidden"]) !!}
                    </div>
                    <div class="form-group">
                        <label>版本选择：</label>
                        <el-radio-group v-model="version" name="carousel[version]">
                            <div><el-radio :label="3">默认全部版本</el-radio></div>
                            <div>
                                <el-radio :label="6">自定义版本</el-radio><br>
                                <el-input name="carousel[start_version]" placeholder="起始版本" resize="both" style="width: 100px; height: 10px;" value="{{ Input::old('carousel[start_version]') }}"></el-input>
                                <el-input name="carousel[end_version]"  placeholder="截止版本" resize="both" style="width: 100px; height: 10px;" value="{{ Input::old('carousel[end_version]') }}"></el-input>

                            </div>
                        </el-radio-group>
                        <input type="hidden" class="form-control" v-model="version" name="carousel[version]">
                    </div>

                    <div class="form-group">
                        <label>类型</label>
                        {!!  Form::select('carousel[type]', [0 => '外部链接',1 => '帖子ID'], isset($carousel) ? $carousel->type : null,['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        <label>链接/ID</label>
                        {!! Form::text('carousel[url]', isset($carousel) ? $carousel->url : null, ['class' => 'form-control']) !!}
                    </div>


                    <div class="form-group">
                        <span>展现时间段：</span>
                        <el-date-picker type="datetime" placeholder="开始时间" v-model="start_display" name="carousel[start_display]"></el-date-picker>
                        <el-date-picker type="datetime" placeholder="结束时间" v-model="end_display"  name="carousel[end_display]"></el-date-picker>
                        <el-input :value="date_start_str" type="hidden" name="carousel[start_display]"></el-input>
                        <el-input :value="date_end_str" type="hidden" name="carousel[end_display]"></el-input>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url() }}">{{ trans('forms.cancel') }}</a>
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
                    //Android端版块图片
                    imageUrlAndroid: "{{ isset($carousel) ? ($carousel->android_icon) : (Input::old('carousel')['android_icon']) }}",
                    //Ios端版块图片
                    imageUrlIos: "{{ isset($carousel) ? ($carousel->ios_icon) : (Input::old('carousel')['ios_icon']) }}",
                    start_display:"{{ isset($carousel) ? ($carousel->start_display) : (Input::old('carousel')['start_display']) }}",
                    end_display:"{{ isset($carousel) ? ($carousel->end_display) : (Input::old('carousel')['end_display']) }}",
                    version:''
                };
            },
            methods: {
                //Android端图片
                imageUrlAndroidHandle: function (res) {
                    this.imageUrlAndroid = res.filename;
                },

                //IOS端图片
                imageUrlIosHandle: function (res) {
                    this.imageUrlIos = res.filename;
                },

                clearImageUrlAndroid: function () {
                    this.imageUrlAndroid = null;
                },

                clearImageUrlIos: function () {
                    this.imageUrlIos = null;
                }

            },
            computed: {
                date_start_str: function () {
                    return typeof this.start_display === 'string' ? this.start_display : this.start_display.format('yyyy-MM-dd hh:mm:ss')
                },
                date_end_str: function () {
                    return typeof this.end_display === 'string' ? this.end_display : this.end_display.format('yyyy-MM-dd hh:mm:ss')
                },
            },
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