@extends('layouts.default')

@section('title')
@if(Request::is('/'))
@elseif (isset($node))
{{ $node->name }}
 - @parent
@else
{{ trans('hifone.threads.list') }}
 - @parent
@endif
@stop

@section('content')

<div class="col-md-9 threads-index main-col">
    <div class="panel panel-default">

        <div class="panel-heading">
            <div class="pull-left hidden-sm hidden-xs">
                @if (Request::is('/'))
                    <i class="fa fa-list"></i> {{ trans('hifone.home') }}
                @elseif (isset($node))
                    <div class="node-info">
                        <strong>{{ $node->name }}</strong>
                        <span class="total">{{ trans('hifone.threads.thread_count', ['threads' => $node->thread_count ]) }}</span>
                        @if($node->description)<div class="summary">{{ $node->description }}</div>@endif
                    </div>
                @elseif (isset($tag))
                    <div class="node-info">
                        {{ trans('hifone.tags.name') }}: <strong>{{ $tag->name }}</strong>
                        <span class="total">, {{ trans('hifone.threads.thread_count', ['threads' => $tag->threads->count() ]) }}</span>
                    </div>
                @else
                    <i class="fa fa-comments-o"></i> {{ trans('hifone.threads.threads') }}
                @endif
            </div>
            @if (!isset($tag))
            @include('threads.partials.filter')
            @endif
            <div class="clearfix"></div>
        </div>

        @if ( ! $threads->isEmpty())
            <div class="panel-body remove-padding-horizontal">
                @if (count($threads))

                    <ul class="list-group row thread-list">
                        @foreach ($threads as $thread)
                            <li class="list-group-item media" style="margin-top: 0px;">
                                <a class="pull-right" href="{{ route('thread.show', [$thread->id]) }}" >
                                    <span class="badge badge-reply-count"> {{ $thread->reply_count.' / '.$thread->view_count }} </span>
                                </a>
                                <div class="avatar pull-left">
                                    <a href="{{ $thread->user->url }}">
                                        <img class="media-object img-thumbnail avatar-48" alt="{{ $thread->user->username }}" src="{{ $thread->user->avatar_small}}"/>
                                    </a>
                                </div>
                                <div class="infos">
                                    <div class="media-heading">
                                        @if (!Input::get('filter') && Route::currentRouteName() != 'excellent' )
                                            @foreach($thread->icons as $icon)<i class="{{ $icon }}"></i>@endforeach
                                        @endif
                                        <a href="{{ route('thread.show', [$thread->id]) }}">
                                            {!! isset($thread['search']['title']) ? $thread['search']['title'] : $thread->title !!}
                                        </a>
                                        <div>{!! isset($thread['search']['body']) ? $thread['search']['body'] : $thread->body !!}</div>
                                    </div>
                                    <div class="media-body meta">
                                        @if ($thread->like_count > 0)
                                            <a href="{{ route('thread.show', [$thread->id]) }}" class="remove-padding-left" id="pin-{{ $thread->id }}">
                                                <span class="fa fa-thumbs-o-up"> {{ $thread->like_count }} </span>
                                            </a>
                                            <span> • </span>
                                        @endif

                                        @if(!isset($node))
                                            <a href="{{ $thread->node->url }}" title="{{ $thread->node->name }}" {{ $thread->like_count == 0 || 'class="remove-padding-left"'}}>
                                                {{ $thread->node->name }}
                                            </a>
                                            <span> • </span>
                                        @endif
                                        @if($thread->tagsList)
                                            <span class="tag-list hidden-xs">
                                                @foreach($thread->tags as $tag)
                                                    <a href="/tag/{{ urlencode($tag->name) }}"><span class="tag">{{ $tag->name }}</span></a>
                                                @endforeach
                                                <span> • </span>
                                            </span>
                                        @endif
                                        @if ($thread->reply_count == 0)
                                            <a href="{{ $thread->user->url }}" title="{{ $thread->user->username }}">{{ $thread->user->username }}
                                            </a>
                                            <span> • </span>
                                            <span class="{{ $thread->highlight }}" data-toggle="tooltip" data-placement="top" title="{{ $thread->created_time }}">{{ $thread->created_at }}</span>
                                        @endif
                                        @if ($thread->reply_count > 0 && count($thread->lastReplyUser))
                                            <span>{{ trans('hifone.threads.last_reply_by') }}</span>
                                            <a href="{{ route('user.home', [$thread->lastReplyUser->username]) }}">{{ $thread->lastReplyUser->username }}</a>
                                            <span> • </span>
                                            <span class="{{ $thread->highlight }}" data-toggle="tooltip" data-placement="top">{{ $thread->created_at }}</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                @else
                    <div class="empty-block">{{ trans('hifone.noitem') }}</div>
                @endif
            </div>
        @else
            <div class="panel-body">
                <div class="empty-block">{{ trans('hifone.noitem') }}</div>
            </div>
        @endif

    </div>

    <!-- Nodes List -->
    @include('nodes.partials.list')

</div>

@include('partials.sidebar')

<style>
    em {
        color: #ff8000;
    }
</style>
@stop
