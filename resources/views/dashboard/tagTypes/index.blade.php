@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-tag"></i> 标签分类
            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.tag.type.create', ['user']) }}">新增标签分类</a>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-striped table-condensed" id="batchForm">
                    <tbody>
                    <tr class="head">
                        <td class="first">标签分类id</td>
                        <td>分类名</td>
                        <td>已有标签</td>
                        <td>操作</td>
                    </tr>
                    @foreach($tagTypes as $tagType)
                        <tr>
                            <td>{{ $tagType->id }}</td>
                            <td>{{ $tagType->display_name }}</td>
                            <td>
                                @foreach($tagType->tags as $tag)
                                    {{ $tag->name . '， ' }}
                                @endforeach
                            </td>
                            @if ($tagType->display_name != '自动标签')
                            <td>
                                <a href="/dashboard/tag/type/{{ $tagType->id }}/edit/user" title="编辑"><i class="fa fa-pencil"></i></a>
                                <a data-url="{{ route('dashboard.tag.type.destroy',['id'=>$tagType->id, 'user']) }}" data-method="delete" class="confirm-action" data-title="是否删除该分类？"><i class="fa fa-trash"></i></a>
                            </td>
                            @endif
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>

@stop