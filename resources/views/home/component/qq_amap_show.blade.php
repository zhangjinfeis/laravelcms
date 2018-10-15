<!--
腾讯地图设置经纬度插件
param必填参数:
id:唯一性标识
width:地图宽度
height:地图高度
lng:经度默认值
lat:纬度默认值
zoom:缩放级别默认值
title:标题
address:标题
-->
<div id="{{$id}}map_show" style="width:600px;height:400px;overflow: hidden;margin:0;font-family:'微软雅黑';"></div>
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=WHPBZ-TRDW3-VUH37-3VKPD-AWRNZ-ZJBH7"></script>
<!-- UI组件库 1.0 -->
<script type="text/javascript">
    var init = function() {
        var id="{{$id}}"+'map_show';
        var _zoom='{{$zoom}}';
            _zoom=Number(_zoom);
        var center = new qq.maps.LatLng('{{$lat}}',{{$lng}});
        var map = new qq.maps.Map(document.getElementById(id),{
            center: center,
            zoom: _zoom,
        });
        //创建标记
        //注意anchor ，其中数值要对照实际坐标调整
        var anchor = new qq.maps.Point(18, 32),
            size = new qq.maps.Size(36, 36),
            origin = new qq.maps.Point(0,0),
            icon = new qq.maps.MarkerImage('https://www.cdlhome.com.sg/mobile_assets/images/icon-location.png', size, origin, anchor);
        var marker = new qq.maps.Marker({
            position: center,
            map: map,
            icon:icon

        });

        //添加到提示窗
        var info = new qq.maps.InfoWindow({
            map: map,
        });

        //获取标记的点击事件
        qq.maps.event.addListener(marker, 'click', function() {
            info.open();
            info.setContent('<div style="text-align:center;white-space:nowrap;'+
                'margin:10px;">{{$title}}<br>{{$address}}</div>');
            info.setPosition(center);
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


