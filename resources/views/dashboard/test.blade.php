@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <table class="table table-bordered table-striped table-condensed">
            <tbody>
            <tr class="head">
                <td class="first">#</td>
                <td>话题</td>
                <td>板块</td>
                <td>回帖人</td>
                <td>操作</td>
            </tr>
            @foreach($threads as $thread)
                <tr>
                    <td>{{ $thread->id }}</td>
                    <td><a target="_blank" href="{{ $thread->url }}">{{ $thread->title }}</a></td>
                    <td><a href="{{ $thread->node->url }}" target="_blank">{{ $thread->node->name }}</a></td>
                    <td><a href="{{ $thread->author_url }}">{{ $thread->user->username }}</a></td>
                    <td>
                        <a data-url="/dashboard/thread/{{$thread->id}}/excellent" data-method="post" class="confirm-action"><i class="{{ $thread->excellent }}"></i></a>
                        <a data-url="/dashboard/thread/{{$thread->id}}/pin" data-method="post"><i class="{{ $thread->pin }}"></i></a>
                        <a data-url="/dashboard/thread/{{$thread->id}}/sink" data-method="post"><i class="{{ $thread->sink }}"></i></a>
                        <a href="/dashboard/thread/{{ $thread->id }}/edit"><i class="fa fa-pencil"></i></a>
                        <a data-url="/dashboard/thread/{{ $thread->id }}/trash" data-method="post" class="need-reason"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@stop