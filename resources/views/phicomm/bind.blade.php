@extends('layouts.default')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">创建社区用户</div>
                    <div class="panel-body">
                        <form role="form" method="POST" action="{{ url('/phicomm/bind') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <input class="form-control" name="username" value="{{ Input::old('username') }}" placeholder="社区用户名" required>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="创建社区用户" class="btn btn-primary btn-block">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
