@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper" id="app">
        <div class="header sub-header">
            {{ isset($tagType) ? '编辑标签分类' : '新增标签分类' }}
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if(isset($tagType))
                    {!! Form::model($tagType, ['route' => ['dashboard.tag.type.update', $tagType->id], 'method' => 'patch', 'class' => 'create_form']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.tag.type.store', 'method' => 'post', 'class' => 'create_form']) !!}
                @endif

                <fieldset>
                    <div class="form-group">
                        <label>{{ '标签分类名' }}</label>
                        {!! Form::text('tagType[display_name]', isset($tagType) ? $tagType->display_name : null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </fieldset>

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                                <a class="btn btn-default" href="{{ back_url('dashboard.tag.type.index') }}">{{ trans('forms.cancel') }}</a>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
            </div>
        </div>
    </div>
<script>
        new Vue({
            el: '#app',
            data: function () {
                return {
                    types: {!! $types or json_encode([])  !!}
                };
            },

        })
</script>
@stop