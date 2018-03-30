@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            {{ isset($tag) ? '编辑标签' : '新增标签' }}
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if(isset($tag))
                    {!! Form::model($tag, ['route' => ['dashboard.tag.update', $tag->id], 'method' => 'patch', 'class' => 'create_form']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.tag.store', 'method' => 'post', 'class' => 'create_form']) !!}
                @endif

                <fieldset>
                    <div class="form-group">
                        <label>{{ '标签名' }}</label>
                        {!! Form::text('tag[name]', isset($tag) ? $tag->name : null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    @if($tagTypes->count() > 0)
                        <div class="form-group">
                            <label>{{ '标签分类' }}</label>
                            <select name="tag[type]" class="form-control">
                                @foreach($tagTypes as $tagType)
                                <option value="{{ $tagType->id }}" {{ option_is_selected([$tagType, 'type', isset($tag) ? $tag : null]) }}>{{ $tagType->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                </fieldset>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url('dashboard.tag.index') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>


    </div>
@stop