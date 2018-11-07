<!--
页面中引入该组件，onclick调用fileDetail方法
-->
<style>
    .m-filedetail{}
    .m-filedetail .ext{width:100px;height: 100px; line-height: 100px; text-align: center; font-size: 18px; background:#e9ecef;border:#ddd 1px solid;margin:0 auto;}
</style>
<div id="showfile" class="hide">
    <div id="move_content">
        <div class="form-group">
            <div class="m-filedetail js-filedetail">
                <div class="h10"></div>
                <div class="ext">

                </div>
                <div class="h20"></div>
                <div>大小：<span class="size"></span></div>
                <div>路径：<span class="path"></span></div>
                <div>源文件：<span class="original_name"></span></div>
                <div>是否使用：<span class="is_used"></span></div>
                <div>时间：<span class="time"></span></div>
            </div>
        </div>
        <div class="h10"></div>
    </div>
</div>

<script>
    function fileDetail(md5){
        var loading = $boot.loading({text:"加载中..."});
        $.ajax({
            type:'post',
            url:"{{url('/admin/file/ajax_detail')}}",
            data:{md5:md5},
            success:function(res){
                loading.close();
                if(res.status == 1){
                    $('.js-filedetail .ext').text(res.data.ext);
                    $('.js-filedetail .path').text(res.data.path);
                    $('.js-filedetail .size').text(res.data.size);
                    $('.js-filedetail .original_name').text(res.data.original_name);
                    $('.js-filedetail .is_used').text(res.data.is_used);
                    $('.js-filedetail .time').text(res.data.created_at);
                    $boot.win({title:"文件详情",id:"#showfile"});
                }
            }
        });
        return false;
    }
</script>