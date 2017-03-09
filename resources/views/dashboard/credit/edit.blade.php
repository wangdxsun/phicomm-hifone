@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header" id="sections">
        <span class="uppercase">修改积分规则</span>
    </div>
    <div class="row">
        <div class="col-sm-12">
            {!! Form::model($credit_rule, ['route' => ['dashboard.creditRule.update', $credit_rule->id], 'method' => 'patch']) !!}
            @include('partials.errors')
            <fieldset>
                <div class="form-group">
                    <label>积分规则名称</label>
                    {!! Form::text('creditRule[name]', $credit_rule->name, ['class' => 'form-control', 'disabled']) !!}
                </div>
                <div class="form-group">
                    <label>频率</label>
                    {!! Form::text('creditRule[frequency]', $credit_rule->frequency, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    <label>奖励积分</label>
                    {!! Form::text('creditRule[reward]', $credit_rule->reward, ['class' => 'form-control']) !!}
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