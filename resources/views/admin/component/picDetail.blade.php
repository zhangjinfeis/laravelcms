<div id="showpic" class="hide">
    <div id="move_content">
        <div class="form-group">
            <div class="m-picdetail js-picdetail">
                <div class="h10"></div>
                <div class="pic_warp">
                    <div class="pic">
                        <img src="/storage/201811/02/32edc76ddc64e3f5b2d62364750faaf3.jpeg"/>
                    </div>
                </div>
                <div class="h20"></div>
                <div>尺寸：<span class="px"></span></div>
                <div>大小：<span class="size"></span></div>
                <div>地址：<span class="path"></span></div>
                <div>原图：<span class="original_name"></span></div>
                <div>时间：<span class="time"></span></div>
            </div>
        </div>
        <div class="h10"></div>
    </div>
</div>

<script>
    function picDetail(md5){
        var loading = $boot.loading({text:"加载中..."});
        $.ajax({
            type:'post',
            url:"{{url('/admin/pic/ajax_detail')}}",
            data:{md5:md5},
            success:function(res){
                loading.close();
                if(res.status == 1){
                    $('.js-picdetail .pic img').attr('src',res.data.path);
                    $('.js-picdetail .px').text(res.data.px);
                    $('.js-picdetail .size').text(res.data.size);
                    $('.js-picdetail .path').text(res.data.path);
                    $('.js-picdetail .original_name').text(res.data.original_name);
                    $('.js-picdetail .time').text(res.data.created_at);
                    $boot.win({title:"图片详情",id:"#showpic"});
                }
            }
        });
        return false;
    }
</script>