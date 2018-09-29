<div class="u-breadcrumb">
    @if(isset($is_back) && $is_back)
        <a class="back" href="{{ url()->previous() }}" ><span class="fa fa-chevron-left"></span> 后退</a>
    @endif
    <a class="back" href="javascript:window.location.reload();" ><span class="fa fa-repeat"></span> 刷新</a>
    <span class="name">{{$name}}</span>
</div>