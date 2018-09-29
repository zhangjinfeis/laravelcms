<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    @section('cssjs')
        @include('admin.include.cssjs')
        {{--ajax请求csrf--}}
        <script>
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
            });
        </script>
    @show
</head>
<body>


@section('page')
<div class="pl15 pr15 pt15 pb15">
    <div class="g-page-in">
        @yield("content")
    </div>
</div>
<script>
    (function(){
        var h = $(window).height();
        $('.g-page-in').css({'min-height':h-30});
    })();
</script>
@show


<script src="/resources/admin/js/my.js"></script>
</body>
</html>