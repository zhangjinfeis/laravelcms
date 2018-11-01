<div class="u-breadcrumb">
    @if(isset($is_back) && $is_back)
        <a class="back" href="javascript:history.back();" title="返回"><span class="fa fa-chevron-left"></span></a>
        {{--<a class="back" href="{{ url()->previous() }}" title="返回"><span class="fa fa-chevron-left"></span></a>--}}
    @endif
    <a class="back" href="javascript:window.location.reload();" title="刷新"><span class="fa fa-repeat"></span></a>
    <span class="name">{{$name}}</span>
</div>