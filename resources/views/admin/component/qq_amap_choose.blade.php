{{--
腾讯地图设置经纬度插件
param参数:
width:地图宽度，默认100%，,选填
height:地图高度，默认450px,选填
input_lng:经度input名称，必填
input_lat:纬度input名称，必填
input_zoom:缩放级别input名称,选填
lng:经度默认值,选填
lat:纬度默认值,选填
zoom:缩放级别默认值,选填
--}}

<div id="{{$input_lng}}map" style="width:{{$width ?? '100%'}};height:{{$height ?? '400px'}};overflow: hidden;margin:0;font-family:'微软雅黑';">
    <div style="height:22px;"></div>
    <div id="fitBoundsDiv"></div>
    <div id="centerDiv"></div>
    <div id="zoomDiv"></div>
    <div id="containerDiv"></div>
    <div id="mapTypeIdDiv"></div>
    <div id="projection"></div>
</div>
<div style="margin:5px 0 0 0;">
    经度：<span class="{{$input_lng}}fun-lng">{{$lng ?? '0'}}</span>&nbsp;&nbsp;&nbsp;&nbsp;纬度：<span class="{{$input_lng}}fun-lat">{{$lat ?? '0'}}</span>
    <span class="c999">&nbsp;&nbsp;&nbsp;&nbsp;提示：拖拽地图选择位置</span>
</div>
<input type="hidden" name="{{$input_lng}}" value="{{$lng ?? ''}}">
<input type="hidden" name="{{$input_lat}}" value="{{$lat ?? ''}}">
@if(isset($input_zoom))
    <input type="hidden" name="{{$input_zoom}}"
           @if(isset($zoom)&&$zoom!=0)
           value="{{$zoom}}"
           @else
           value="15"
           @endif
           id="_zoom">
@endif
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=WHPBZ-TRDW3-VUH37-3VKPD-AWRNZ-ZJBH7"></script>

<script>
    function init() {
        var id='{{$input_lng}}'+'map';
        var _zoom=$('#_zoom').val();
            _zoom=Number(_zoom);
        var lats=30.556187,
            lngs=114.329605;
                @if(isset($lng) && $lng && isset($lat) && $lat)
                    lats='{{$lat}}';
                    lngs='{{$lng}}';
                @endif

                //div容器
                var container = document.getElementById(id);
                var centerDiv = document.getElementById("centerDiv");

                //初始化地图
                var map = new qq.maps.Map(container, {
                    // 地图的中心地理坐标
                    center: new qq.maps.LatLng(lats,lngs),
                    zoom: _zoom
                });
                //创建自定义控件
        //设置标注点位置
        //left top 所减的值不可随意更改，要配合客户端显示地图的图标大小调整
               var width=  $('#'+id).width();
               var left=width/2-18+'px';
               var height =$('#'+id).height();
               var top=height/2-72+'px';

        var middleControl = document.createElement("div");
                middleControl.style.left=left;
                middleControl.style.top=top;
                middleControl.style.position="relative";
                middleControl.style.width="36px";
                middleControl.style.height="36px";
                middleControl.style.zIndex="100000";
                middleControl.innerHTML ='<img src="https://www.cdlhome.com.sg/mobile_assets/images/icon-location.png" />';
                document.getElementById(id).appendChild(middleControl);
                //返回地图当前中心点地理坐标
                centerDiv.innerHTML = "latlng:" + map.getCenter();
                //当地图中心属性更改时触发事件
                qq.maps.event.addListener(map, 'center_changed', function() {
                    centerDiv.innerHTML = "latlng:" + map.getCenter();
                    var _map=map.getCenter();
                    var lat=_map.lat;
                    var lng=_map.lng;
                    $('input[name={{$input_zoom}}]').val(map.getZoom());
                    $('input[name={{$input_lng}}]').val(lng);
                    $('input[name={{$input_lat}}]').val(lat);
                    $('.{{$input_lng}}fun-lng').text(lng);
                    $('.{{$input_lng}}fun-lat').text(lat);
                });
    }

    function loadScript() {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "https://map.qq.com/api/js?v=2.exp&callback=init";
        document.body.appendChild(script);
    }

    window.onload = loadScript;
</script>

