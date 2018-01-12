<div class="row">
    <div class="panel-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="sidebar-toggler visible-xs">
                <i class="fa fa-navicon"></i>
            </li>
            @foreach($sub_nav as $key => $item)
                @if ( $item['src'] == $src)
                    <li class="{{ $key == $current_tap  ? 'active' : null }}">
                        <a href="{{ $item['url'] }}">
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>