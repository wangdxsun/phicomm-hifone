@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header" id="sections">
        <span class="uppercase">修改经验值规则</span>
    </div>
    <div class="row">
        <div class="col-sm-12">
            {!! Form::model($credit_rule, ['route' => ['dashboard.creditRule.update', $credit_rule->id], 'method' => 'patch']) !!}
            @include('partials.errors')
            <fieldset>
                <div class="form-group">
                    <label>经验值规则名称</label>
                    {!! Form::text('creditRule[name]', $credit_rule->name, ['class' => 'form-control', 'disabled']) !!}
                </div>
                <div class="form-group">
                    <label>奖励周期</label>
                    <select name="creditRule[type]" class="form-control">
                        @foreach($credit_rule->types as $key => $type)
                            <option value="{{ $key }}" {{ $credit_rule->type == $key ? "selected" : null }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>奖励次数</label>
                    {!! Form::number('creditRule[times]', $credit_rule->times, ['class' => 'form-control', 'placeholder' => '奖励周期选择每日时才需要填']) !!}
                </div>
                <div class="form-group">
                    <label>奖励经验值</label>
                    {!! Form::number('creditRule[reward]', $credit_rule->reward, ['class' => 'form-control', 'max' => 99, 'min' => -99]) !!}
                </div>
            </fieldset>

            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                        <a class="btn btn-default" href="{{ back_url('dashboard.creditRule.index') }}">{{ trans('forms.cancel') }}</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop