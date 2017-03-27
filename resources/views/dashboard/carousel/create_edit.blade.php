@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header" id="nodes">
        <span>{{ $page_title }}</span>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @include('partials.errors')
            @if(isset($carousel))
                {!! Form::model($carousel, ['route' => ['dashboard.carousel.update', $carousel->id], 'method' => 'patch', 'class' => 'create_form']) !!}
            @else
                {!! Form::open(['route' => 'dashboard.carousel.store','id' => 'carousel-create-form', 'method' => 'post', 'class' => 'create_form']) !!}
            @endif
                <fieldset>
                    <div class="form-group">
                        <a href="javascript:void(0);" class="btn-upload">
                            <img src="{{ $carousel->image  or '/images/blank.png' }}" class="ImagePreviewBox" style="max-height: 400px; max-width: 500px; cursor: pointer;">
                        </a>
                        <input type="file" name="file" class="input-file hide">
                        <input id="imageUrl" type="hidden" class="form-control" name="carousel[image]" value="{{ $carousel->image or null }}">
                    </div>
                    <div class="form-group">
                        <label>链接</label>
                        {!! Form::url('carousel[url]', isset($carousel) ? $carousel->url : null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <label>描述</label>
                        {!! Form::textarea('carousel[description]', isset($carousel) ? $carousel->description : null , [
                        'class' => 'form-control', 'rows' => 5, 'style' => "overflow:hidden"]) !!}
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url('dashboard.node.index') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop