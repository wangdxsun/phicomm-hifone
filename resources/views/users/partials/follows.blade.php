<ul class="list-group">

  @foreach ($follows as $follow)
   <li class="list-group-item" >

      <a href="{{ route('user.show', [$follow->id]) }}" title="{{ $follow->title }}">
        {{ str_limit($follow->title, '100') }}
      </a>

      <span class="meta">

        <a href="{{ $follow->node->url }}" title="{{ $follow->node->name }}">
          {{ $follow->node->name }}
        </a>
        <span> • </span>
        {{ $follow->reply_count }} {{ trans('hifone.replies.replies') }}
        <span> • </span>
        <span class="timeago">{{ $follow->created_at }}</span>

      </span>

  </li>
  @endforeach

</ul>
