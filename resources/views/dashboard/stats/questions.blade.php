@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div>
            <div class="text-right">
                <span>
                    当前页：新增提问总数：{{ $questionsCount }}
                </span>
            </div>
            <div class="text-right">
                <span>
                    新增回答总数：{{ $answersCount }}
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日新增提问数</td>
                        <td>每日新增回答数</td>
                    </tr>
                    @foreach($statsArr as $key => $value)
                        <tr>
                            <td>{{ $value['date'] }}</td>
                            <td>{{ $value['question_count'] }}</td>
                            <td>{{ $value['answer_count'] }}</td>
                        </tr>
                        @endforeach
                </table>
            </div>
        </div>
    </div>
@stop