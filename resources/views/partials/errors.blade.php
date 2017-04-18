@if ($errors->any())
    <script>
        $().ready(function() {
            swal({
                title: "{{ $errors->all(':message')[0] }}",
                timer: 2000,
                type: "error",
                showConfirmButton: false
            });
        });
    </script>
@endif

@if ($message = Session::get('success'))
<script>
    $().ready(function() {
        swal({
            title: "{{ $message }}",
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