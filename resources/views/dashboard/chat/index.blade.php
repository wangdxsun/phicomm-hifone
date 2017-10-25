@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper" id="app">
        @if(isset($sub_menu))
            @include('dashboard.partials.sub-nav')
        @endif
        @include('partials.errors')
        <div>
            {!! Form::open(['route' => 'dashboard.chat.store', 'method' => 'post', 'class' => 'create_form']) !!}
            <div class="form-group">
                <span style="position: relative;top: -67px;"> {{'用户选择：'}}</span>
                <template>
                    <el-radio-group v-model="usersType" name="chat[userType]">
                        <div>
                            <el-radio :label="3">社区全体用户</el-radio>
                        </div>

                        <div class="div-inline">
                            <el-radio :label="6">具体帖子内的回复用户</el-radio>
                            <el-input v-model="threadId" name="chat[thread_id]" placeholder="请输入帖子ID,若按回贴时间筛选请选择回复的开始时间和结束时间"></el-input>
                            <el-date-picker type="datetime" placeholder="开始时间" v-model="date_start" name="chat[date_start]"></el-date-picker>
                            <el-date-picker type="datetime" placeholder="结束时间" v-model="date_end"  name="chat[date_end]"></el-date-picker>
                            <el-input :value="date_start_str" placeholder="请输入内容" type="hidden" resize="both"  style="width: 60px; height: 10px;" name="chat[date_start]"></el-input>
                            <el-input :value="date_end_str" placeholder="请输入内容" type="hidden" resize="both"  style="width: 60px; height: 10px;" name="chat[date_end]"></el-input>
                        </div>

                        <div>
                            <el-radio :label="9">自主选择用户</el-radio>
                            <el-select
                                v-model="userIds"
                                multiple
                                filterable
                                remote
                                placeholder="请输入用户名"
                                :remote-method="getUser"
                                :loading="loading">
                                <el-option
                                    v-for="user in users"
                                    :key="user.id"
                                    :label="user.username"
                                    :value="user.id">
                                </el-option>
                            </el-select>
                            <input type="hidden" class="form-control" :value="userIds" name="chat[userIds]">
                        </div>
                    </el-radio-group>
                    <input type="hidden" class="form-control" v-model="usersType" name="chat[userType]">
                </template>
            </div>

            <div class="form-group">
                <span style="display: block; float: left;">{{'消息内容：'}}</span>
                <template>
                    <el-input style="width: 80%;" name="message"
                              type="textarea"
                              :rows="10"
                              placeholder="文字、图片不能同时为空"
                              v-model="chatBody">
                    </el-input>
                </template>

            </div>
            <div style="position: relative;left: 67px;">
                <template>
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
                </template>
            </div>
            <div class="form-group">
                <span display="inline">{{'发送方式：'}}</span>
                <span >{{'私信'}}</span>
            </div>

            <div class="col-xs-12" style="text-align: center">
                <div class="form-group">
                    <button type="submit" class="btn btn-success" style="width: 100px;height: 60px;">{{ trans('forms.submit') }}</button>
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
                    chatBody:'',
                    threadId: null,
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
                        axios.get('http://hifone1.dm.dev.phiwifi.com:1885/api/v1/user/search?q=' + query).then(response => {
                            this.loading = false;
                            this.users = response.data
                        })
                    } else {
                        this.users = [];
                    }
                }
            }

        });

    </script>
    <style>
        .div-inline {
            display:inline
        }
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