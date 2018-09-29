{{--
上传多张图片组件-调用方法:
    @include('admin.component.uploadImgsResize',array())
    必填：input_id,input_name
    可选：width、height、size（单位M）
    赋值：input_value
    优先按宽度缩放，其次按高度，都未定义则上传原图
--}}

<style>
.m-uploadimg-group{border:#ddd 1px dashed;overflow: hidden; display:table-cell;padding:0 10px 0 0;}
.m-uploadimg-group ul{float:left;margin: 0px;padding:0px;}
.m-uploadimg-group ul li{float:left;width:110px;height:110px;margin:10px 0px 10px 10px;position:relative;border:#ddd 1px solid; background:#fff;overflow: hidden;}
.m-uploadimg-group ul li img{ width: 100%;}
.m-uploadimg-group ul li.default{display:none;}
.m-uploadimg-group ul li span.size{position:absolute;left:5px;top:5px;display:inline-block;height:18px;line-height:18px;border-radius:9px;padding:0 5px;background:rgba(0,0,0,0.5);color:#fff; font-size: 12px;}
.m-uploadimg-group ul li a.prev{position:absolute;left:30px;bottom:5px;display:inline-block;height:20px;line-height:18px;width:20px;border-radius:10px;text-align:center;background:rgba(0,0,0,0.5);color:#fff; cursor: pointer;}
.m-uploadimg-group ul li a.next{position:absolute;right:30px;bottom:5px;display:inline-block;height:20px;line-height:18px;width:20px;border-radius:10px;text-align:center;background:rgba(0,0,0,0.5);color:#fff;cursor: pointer;}
.m-uploadimg-group ul li a.delete{display:inline-block;line-height:20px;height:20px;width:20px;border-radius:10px;text-align:center;overflow:hidden;position:absolute;right:5px;top:5px;text-decoration:none;background: rgba(0,0,0,0.5);font-size:14px;color:#fff; cursor: pointer;}
.m-uploadimg-group ul li a:hover{color:#fff !important;background: rgba(0,0,0,0.8);}
</style>

@php
    $input_value = isset($input_value)&&$input_value?$input_value:'';
    $pics = [];
    $old_md5s = '';
    if(!empty($input_value)){
        $arr = explode(',',$input_value);
        foreach($arr as $key => $val){
            preg_match_all('/[a-z0-9]{32}/',$val,$res);
            $pics[$key]['path'] = $val;
            $pics[$key]['md5'] = $res[0][0];
            $old_md5s.= ','.$res[0][0];
        }
        $old_md5s = trim($old_md5s,',');
    }
@endphp

<div id="upload-{{$input_id}}">
    <label id="{{$input_id}}_btn" class="u-dmuploader">
        <span role="button" class="btn btn-sm btn-primary w110"><span class="fa fa-upload"></span> 多图上传</span>
        <input type="file" name="files[]" class="hide">
    </label>

    <input type="hidden" data-o="{{$old_md5s}}" name="{{$input_name}}" value="{{$input_value}}">
    <input type="hidden" name="pic_not_use_id[]" value="">
    <input type="hidden" name="pic_use_id[]" value="">


    <div class="m-uploadimg-group clearfix">
        <ul>
            @foreach($pics as $vo)
                <li data-path="{{$vo['path']}}" data-md5="{{$vo['md5']}}">
                    <img src="{{$vo['path']}}"/>
                    {{--<span class="size">{{ $vo['width'] or '' }}×{{ $vo['height'] or '' }}</span>--}}
                    <a class="prev">‹</a>
                    <a class="next">›</a>
                    <a class="delete">×</a>
                </li>
            @endforeach
        </ul>
        <div class="clear"></div>
    </div>

    <!--克隆代码段-->
    <li class="fun-li-clone hide">
        <img src=""/>
        <span class="size"></span>
        <a class="prev" href="#">‹</a>
        <a class="next" href="#">›</a>
        <a class="delete" href="#">×</a>
    </li>


</div>

<script>

    //插件地址：https://github.com/danielm/uploader
    $('#{{$input_id}}_btn').dmUploader({
        url: '/admin/upload/ajax_upload_img',
        dataType: 'json',
        maxFileSize : '{{isset($size)?$size:(int)ini_get('upload_max_filesize')*1024}}*1024',  //允许上传的大小，单位KB
        allowedTypes: 'image/*',
        multiple:true,
        extraData:{
        _token: $('meta[name="csrf-token"]').attr('content'),
        width : '{{$width or 0}}',
        height : '{{$height or 0}}',
    },
    onComplete: function(){
        //$.danidemo.addLog('#demo-debug', 'default', 'All pending tranfers completed');
        console.log('All pending tranfers completed');
    },
    onUploadProgress: function(id, percent){
        //var percentStr = percent + '%';
        //$.danidemo.updateFileProgress(id, percentStr);
    },
    onUploadSuccess: function(id, res){
        //如果上传失败
        if(res.status == 0){
            $boot.warn({text:res.msg});
        }else {
            //添加到ul后面
            add_group{{ $input_id }}(res.data.md5,res.data.path,res.data.width,res.data.height);

            //重新计算input值、no_use_id、use_id
            reset_group{{ $input_id }}();
            $boot.success({text:'上传成功'});
        }
    },
    onUploadError: function(id, message){
        console.log(message);
        console.log('Failed to Upload file #' + id + ': ' + message);
    },
    onFileTypeError: function(file){
        console.log('File \'' + file.name + '\' cannot be added: must be an image');
    },
    onFileSizeError: function(file){
        console.log('File \'' + file.name + '\' cannot be added: size excess limit');
    },
    onFileExtError: function(file){
        console.log('File \'' + file.name + '\' has a Not Allowed Extension');
    },
    onFallbackMode: function(message){
        console.log('Browser not supported(do something else here!): ' + message);
    }
    });


    //插入新图片
    function add_group{{ $input_id}}($md5,$path,$width,$height){
        var $a = $("#upload-{{$input_id}} .fun-li-clone").clone(true).removeClass().attr({'data-md5':$md5,'data-path':$path}); //移除所有class
        $a.find('.size').text($width+"×"+$height);
        $a.find('img').attr('src',$path);
        $a.appendTo('#upload-{{$input_id}} ul');
    };

    //重新计算
    function reset_group{{ $input_id}}(){
        //原md5值赋给not_use
        var $old_md5 = $("input[name='{{$input_name}}']").attr('data-o');
        $("#upload-{{$input_id}}").find("input[name='pic_not_use_id[]']").val($old_md5);
        //取新的md5值
        var $new_paths =[]; var $new_md5s = [];
        $("#upload-{{$input_id}} ul li").each(function(){
            $new_paths.push($(this).attr('data-path'));
            $new_md5s.push($(this).attr('data-md5'));
        });
        $new_paths = $new_paths.join(',');
        $new_md5s = $new_md5s.join(',');
        //新md5值赋给use
        $("#upload-{{$input_id}}").find("input[name='pic_use_id[]']").val($new_md5s);
        //新md5值赋给input
        $("input[name='{{$input_name}}']").val($new_paths);
    }

    //只重新计算位置更换（调整顺序情况下）
    function reset_group_sort{{ $input_id}}(){
        //取新的md5值
        var $new_paths = [];
        $("#upload-{{$input_id}} ul li").each(function(){
            $new_paths.push($(this).attr('data-path'));
        });
        $new_paths = $new_paths.join(',');
        //新md5值赋给input
        $("input[name='{{$input_name}}']").val($new_paths);
    }

    //删除当前选中缩略图
    $("#upload-{{ $input_id }} a.delete").click(function(){
        $(this).parent().remove();
        reset_group{{ $input_id}}();
        return false;
    });
    //图片前移
    $("#upload-{{ $input_id }} a.prev").click(function(){
        var $index = $(this).parent().prevAll().length;
        if($index != 0){
            var $target = $(this).parent().prev();
            $(this).parent().insertBefore($target);
        }
        reset_group_sort{{ $input_id}}();
        return false;
    });
    //图片后移
    $("#upload-{{ $input_id }} a.next").click(function(){
        var $index = $(this).parent().nextAll().length;
        if($index != 0){
            var $target = $(this).parent().next();
            $(this).parent().insertAfter($target);
        }
        reset_group_sort{{ $input_id}}();
        return false;
    });

</script>