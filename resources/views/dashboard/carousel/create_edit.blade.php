@extends('layouts.dashboard')
@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
<div class="content-wrapper">
    <div class="row">
        @include('partials.errors')
        <div class="col-sm-12" id="app">
            @if(isset($carousel))
                {!! Form::model($carousel, ['route' => ['dashboard.carousel.update', $carousel->id], 'method' => 'patch', 'class' => 'create_form']) !!}
            @else
                {!! Form::open(['route' => 'dashboard.carousel.store','id' => 'carousel-create-form', 'method' => 'post', 'class' => 'create_form']) !!}
            @endif
                <fieldset>
                    <div class="form-group">
                        <div class="col-xs-6">
                            <label>{{ 'web端图片:' }}</label><br>
                            <el-upload
                                    class="avatar-uploader"
                                    action="/upload_image"
                                    :show-file-list="false"
                                    :on-success="imageUrlWebHandle">
                                <img v-if="imageUrlWeb" :src="imageUrlWeb" class="el-avatar">
                                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <el-input :value="imageUrlWeb" type="hidden" name="carousel[web_icon]"></el-input>
                            <el-button class="btn-danger" size="medium" @click="clearImageUrlWeb">清空图片</el-button>
                        </div>

                        <div class="col-xs-6">
                            <label>{{ 'H5端图片:' }}</label><br>
                            <el-upload
                                    class="avatar-uploader"
                                    action="/upload_image"
                                    :show-file-list="false"
                                    :on-success="imageUrlH5Handle">
                                <img v-if="imageUrlH5" :src="imageUrlH5" class="el-avatar">
                                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <el-input  :value="imageUrlH5" type="hidden" name="carousel[h5_icon]"></el-input>
                            <el-button class="btn-danger" size="medium" @click="clearImageUrlH5">清空图片</el-button>

                        </div>
                        <el-input  v-model="image" type="hidden" name="carousel[image]"></el-input>
                    </div>
                    <div class="form-group">
                        <label>描述</label>
                        {!! Form::textarea('carousel[description]', isset($carousel) ? $carousel->description : null , [
                        'class' => 'form-control', 'required', 'rows' => 5, 'style' => "overflow:hidden"]) !!}
                    </div>
                    <div class="form-group">
                        <label>类型</label>
                        {!!  Form::select('carousel[type]', [0 => '外部链接',1 => '帖子ID'], isset($carousel) ? $carousel->type : null,['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        <label>链接/ID</label>
                        {!! Form::text('carousel[url]', isset($carousel) ? $carousel->url : null, ['class' => 'form-control', 'required'])!!}
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
                            <a class="btn btn-default" href="{{ route('dashboard.carousel.web.show') }}">{{ trans('forms.cancel') }}</a>
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
                    //H5端版块图片
                    imageUrlH5: "{{ isset($carousel) ? ($carousel->h5_icon) : (Input::old('carousel')['h5_icon']) }}",
                    //Web端版块图片
                    imageUrlWeb: "{{ isset($carousel) ? ($carousel->web_icon) : (Input::old('carousel')['web_icon']) }}",
                    start_display:"{{ isset($carousel) ? ($carousel->start_display) : (Input::old('carousel')['start_display']) }}",
                    end_display:"{{ isset($carousel) ? ($carousel->end_display) : (Input::old('carousel')['end_display']) }}"
                };
            },
            methods: {
                //H5端图片
                imageUrlH5Handle: function (res) {
                    this.imageUrlH5 = res.filename;
                },

                //Web端图片
                imageUrlWebHandle: function (res) {
                    this.imageUrlWeb = res.filename;
                },

                clearImageUrlH5: function () {
                    this.imageUrlH5 = null;
                },

                clearImageUrlWeb: function () {
                    this.imageUrlWeb = null;
                },

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