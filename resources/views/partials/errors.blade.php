@if ($errors->any())
@include('partials._error', ['level' => 'danger', 'title' => Session::get('title'), 'message' => $errors->all(':message')])
@endif

@if ($message = Session::get('success'))
<script>
    $().ready(function() {
        {{--Messenger().post("{{ $message }}");--}}
        swal({
            title: "{{ $message }}",
            {{--text: "{{ $message }}",--}}
            timer: 1000,
            type: "success",
            showConfirmButton: false
        });
    });
</script>
@endif

@if ($message = Session::get('warning'))
@include('partials._error', ['level' => 'warning', 'title' => Session::get('title'), 'message' => $message])
@endif

@if ($message = Session::get('info'))
@include('partials._error', ['level' => 'info', 'title' => Session::get('title'), 'message' => $message])
@endif