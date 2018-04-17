@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper" id="app">
        <div class="header sub-header">
            <i class="fa fa-user"></i> 用户管理
            @if(Auth::user()->can('new_user'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.user.create') }}">新增用户</a>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" name="user[id]" class="form-control" value="" placeholder="用户ID">
                            <input type="text" name="user[username]" class="form-control" value="" placeholder="用户名">
                            <input type="text" name="user[regip]" class="form-control" value="" placeholder="注册IP">
                        </div>
                        <select class="form-control " name="user[orderType]">
                            <option value="" selected>排列方式</option>
                            @foreach ($orderTypes as $key => $orderType)
                                <option value="{{ $key }}">{{ $orderType }}</option>
                            @endforeach
                        </select>
                        {{--按标签筛选，提供所有的用户标签供选择--}}
                        <el-select v-model="allUserTags" multiple placeholder="选择标签">
                            <el-option-group
                                    v-for="userTagType in userTagTypes"
                                    :key="userTagType.id"
                                    :label="userTagType.display_name">
                                <el-option
                                        v-for="tag in userTagType.tags"
                                        :key="tag.id"
                                        :label="tag.name"
                                        :value="tag.id">
                                </el-option>
                            </el-option-group>
                        </el-select>
                        <input type="hidden" class="form-control" :value="allUserTags" name="user[tags]">

                        <select class="form-control " name="tag[tagCount]">
                            <option value="" selected>标签个数</option>
                            @foreach ($tagCounts as $tagCount)
                                <option value="{{ $tagCount }}">{{ $tagCount }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                <table class="table table-bordered table-striped table-condensed">
                <tbody>
                <tr class="head">
                    <td class="first">ID</td>
                    <td>头像</td>
                    <td>用户名</td>
                    <td>云账号id</td>
                    <td>用户组</td>
                    <td>发帖数</td>
                    <td>回帖数</td>
                    <td>关注数</td>
                    <td>粉丝数</td>
                    <td>经验值</td>
                    <td>注册时间</td>
                    <td>上次登录时间</td>
                    <td>注册IP</td>
                    <td>操作人</td>
                    <td>操作时间</td>
                    <td>标签</td>
                    <td>操作</td>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><img src="{{ $user->avatar_small }}" style="width: 20px; height: 20px;"></td>
                        <td><a href="{{ route('user.show', ['id'=>$user->id]) }}" target="_blank">{{ $user->username }}</a></td>
                        <td>{{ $user->phicomm_id }}</td>
                        <td>{{ $user->role }}</td>
                        <td>{{ $user->thread_count }}</td>
                        <td>{{ $user->reply_count }}</td>
                        <td>{{ $user->follow_count }}</td>
                        <td>{{ $user->follower_count }}</td>
                        <td>{{ $user->score }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->last_visit_time }}</td>
                        <td>{{ $user->regip }}</td>
                        <td>{{ $user->lastOpUser->username }}</td>
                        <td>{{ $user->last_op_time }}</td>
                        <td>
                            @foreach($user->tags as $tag)
                                {{ $tag->name . '， ' }}
                            @endforeach
                        </td>
                        <td>
                            @if(Auth::user()->id <> $user->id && $user->id != 0 )
                                @if(Auth::user()->can('edit_users'))
                                    <a href="/dashboard/user/{{ $user->id }}/edit" title="编辑"><i class="fa fa-pencil"></i></a>
                                    <a data-url="/dashboard/user/{{ $user->id }}/comment" data-method="post" title="禁止发言"><i class="{{ $user->comment }}"></i></a>
                                    <a data-url="/dashboard/user/{{ $user->id }}/login" data-method="post" title="禁止登录"><i class="{{ $user->login }}"></i></a>
                                    <span class="tag_info" @click="editTag({{ $user->id }}, {{ json_encode($user->tags->pluck('id')->toArray()) }})"><i class="fa fa-tag"></i></span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{--编辑用户标签弹窗--}}
            @if(count($users) > 0)
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="content-wrapper">
                                <div class="header sub-header">
                                <span class="uppercase">
                                     {{'编辑标签'}}
                                </span>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div>
                                            <label>{{ '用户标签' }}</label>
                                            <el-select v-model="userTags" id="user-tags" multiple placeholder="选择标签">
                                                <el-option-group
                                                        v-for="userTagType in userTagTypes"
                                                        :key="userTagType.id"
                                                        :label="userTagType.display_name">
                                                    <el-option
                                                            v-for="tag in userTagType.tags"
                                                            :key="tag.id"
                                                            :label="tag.name"
                                                            :value="tag.id">
                                                    </el-option>
                                                </el-option-group>
                                            </el-select>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <div class="btn btn-success" @click="saveTag">保存</div>
                                                    <div class="btn btn-default" @click="hideModel">取消</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Modal -->
                <div class="text-right">
                    <!-- Pager -->
                    {!! $users->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>

        </div>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: function () {
                return {
                    allUserTags:[],
                    editUserTags: {!! $userTags or json_encode([]) !!},
                    userTagTypes: {!! $userTagTypes !!},
                    userTags: '',
                    userId: null,
                };
            },
            methods: {
                editTag: function (user_id, tags) {
                    this.userTags = tags
                    this.userId = user_id
                    $("#myModal").modal('show');
                },
                hideModel: function () {
                    $("#myModal").modal('hide');
                },
                saveTag: function () {
                    $.ajax({
                        url: 'user/'+this.userId+'/tag/update',
                        type: 'PUT',
                        data: {
                            userTags: this.userTags
                        },
                        success: function () {
                            window.location.reload()
                        }
                    })
                }
            }
        })
    </script>
@stop