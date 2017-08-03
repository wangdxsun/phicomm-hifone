<ul class="list-group">

  @foreach ($threads as $index => $thread)
   <li class="list-group-item" >

      <a href="{!! route('thread.show', [$thread->id]) !!}" title="{!! $thread->title !!}">
        {!! str_limit($thread->title, '100') !!}
      </a>

      <span class="meta">

        <a href="{!! $thread->node->url !!}" title="{!! $thread->node->name !!}">
          {!! $thread->node->name !!}
        </a>
        <span> • </span>
        {!! $thread->reply_count !!} {!! trans('hifone.replies.replies') !!}
        <span> • </span>
        <span class="timeago">{!! $thread->created_at !!}</span>

        @if($thread->status == -2)
            <strong class="label-warning-light pull-right">{!! '审核中' !!}</strong>
        @elseif($thread->status == -1)
            <strong class="label-warning pull-right" >{!! '审核未通过' !!}</strong>
        @endif

      </span>

  </li>
  @endforeach

</ul>
