@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper" id="app">
        @if(isset($sub_nav))
            @include('dashboard.partials.sub-nav')
        @endif
        <div>
            {!! Form::open(['route' => 'dashboard.chat.store', 'method' => 'post', 'class' => 'create_form form-horizontal']) !!}
            <div class="form-group">
                <label class="col-sm-1 control-label">用户选择：</label>
                <div class="col-sm-6">
                    <el-radio-group v-model="usersType" name="chat[userType]">
                        <div><el-radio :label="3">社区全体用户</el-radio></div>
                        <div>
                            <el-radio :label="6">具体帖子内的回复用户</el-radio>
                            <el-input name="chat[thread_id]" placeholder="请输入帖子ID" resize="both" style="width: 200px; height: 10px;" value="{{ Input::old('chat[thread_id]') }}"></el-input>
                            <el-date-picker type="datetime" placeholder="开始时间" v-model="date_start" name="chat[date_start]"></el-date-picker>
                            <el-date-picker type="datetime" placeholder="结束时间" v-model="date_end"  name="chat[date_end]"></el-date-picker>
                            <el-input :value="date_start_str" type="hidden" name="chat[date_start]"></el-input>
                            <el-input :value="date_end_str" type="hidden" name="chat[date_end]"></el-input>
                        </div>
                        <div>
                            <el-radio :label="9">自主选择用户</el-radio>
                            <el-select v-model="userIds" multiple filterable remote placeholder="请输入用户名" :remote-method="getUser" :loading="loading">
                                <el-option v-for="user in users" :key="user.id" :label="user.username" :value="user.id"></el-option>
                            </el-select>
                            <input type="hidden" class="form-control" :value="userIds" name="chat[userIds]">
                        </div>
                    </el-radio-group>
                    <input type="hidden" class="form-control" v-model="usersType" name="chat[userType]">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-1 control-label">消息内容：</label>
                <div class="col-sm-6">
                    <el-input name="message" type="textarea" :rows="10" placeholder="文字、图片不能同时为空" value="{{ Input::old('message') }}"></el-input>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-1 control-label">消息图片：</label>
                <div class="col-sm-6">
                    <el-upload
                            class="avatar-uploader"
                            action="/upload_image"
                            :show-file-list="false"
                            :on-preview="handlePictureCardPreview"
                            list-type="picture-card"
                            :on-success="handleAvatarSuccess">
                        <img v-if="dialogImageUrl" :src="dialogImageUrl" class="el_avatar">
                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                    <el-input type="hidden" v-model="dialogImageUrl" name="imageUrl"></el-input>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-1 col-sm-6">
                    <button type="submit" class="btn btn-success">提交</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <script>
        new Vue({
            el: '#app',
            data: function () {
                return {
                    usersType: '',
                    dialogImageUrl: '',
                    dialogVisible: false,
                    loading:false,
                    userIds:[],
                    users:[],
                    date_start:"",
                    date_end:""
                }
            },
            computed: {
                date_start_str: function () {
                    return this.date_start === '' ? '' : this.date_start.format('yyyy-MM-dd hh:mm:ss');
                },
                date_end_str: function () {
                    return this.date_end === '' ? '' : this.date_end.format('yyyy-MM-dd hh:mm:ss');
                }
            },
            methods:{
                handlePictureCardPreview:function(file) {
                    this.dialogImageUrl = file.url;
                    this.dialogVisible = true;
                },
                handleAvatarSuccess:function(res) {
                    this.dialogImageUrl = res.filename;
                },
                getUser(query) {
                    if (query !== '') {
                        this.loading = true;
                        axios.get('/api/v1/user/search/' + query).then(response => {
                            this.loading = false;
                            this.users = response.data.data
                        })
                    } else {
                        this.users = [];
                    }
                }
            }

        });

    </script>
    <style>
        .avatar-uploader .el-upload {
            border: 1px dashed #d9d9d9;
            border-radius: 1px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .avatar-uploader .el-upload:hover {
            border-color: #20a0ff;
        }
        .el_avatar {
            width: 178px;
            height: 178px;
            display: block;
        }
        .el-upload__input {
            display: none!important;
        }
    </style>


@stop