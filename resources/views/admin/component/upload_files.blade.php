{{--
上传单张图片组件-调用方法:
    @include('admin.component.upload_file',array())
    必填：input_id、input_name
    可选：size
    赋值：input_value(json字符串)
--}}


@php
    $input_value = isset($input_value) ? $input_value : '';
    $files = [];
    $old_md5s = '';
    if(!empty($input_value)){
        $files = json_decode($input_value,true);
        foreach ($files as $ke => $vo){
            $old_md5s.= ','.$vo['md5'];
            $files[$ke]['item'] = json_encode($vo);
        }
        $old_md5s = trim($old_md5s,',');
    }



@endphp


<style>
    .m-uploadfiles .item{width:260px;height:50px;overflow:hidden;border:#dee2e6 1px solid;margin:0 0 10px 0; background:#fff;position: relative;}
    .m-uploadfiles .item .ext{width:50px;height: 50px; line-height: 50px; text-align: center; float: left;border-right:#dee2e6 1px solid; font-size: 14px; background:#e9ecef;}
    .m-uploadfiles .item .right{ float: left;width: 170px;margin:5px 0 0 10px;}
    .m-uploadfiles .item .right .name{ word-break: break-all; height: 18px; line-height: 18px; overflow: hidden;}
    .m-uploadfiles .item a.delete{display:inline-block;line-height:20px;height:20px;width:20px;border-radius:10px;text-align:center;overflow:hidden;position:absolute;right:3px;top:3px;text-decoration:none;background: rgba(0,0,0,0.5);font-size:14px;color:#fff;cursor: pointer;}
    .m-uploadfiles a.delete:hover{color:#fff;background: rgba(0,0,0,0.8);}
    .m-uploadfiles a.prev{position:absolute;right:25px;bottom:3px;display:inline-block;height:16px;line-height:12px;width:16px;border-radius:10px;text-align:center;border:#999 1px solid;color:#666;cursor: pointer;}
    .m-uploadfiles a.next{position:absolute;right:5px;bottom:3px;display:inline-block;height:16px;line-height:12px;width:16px;border-radius:10px;text-align:center;border:#999 1px solid;color:#666;cursor: pointer;}
</style>
<div id="upload-{{$input_id}}">

    <label id="{{$input_id}}_btn" class="u-dmuploader">
        <span role="button" class="btn btn-sm btn-primary w110"><span class="fa fa-upload"></span> 上传文件</span>
        <input type="file" name="files[]" class="hide">
    </label>


    <input type="hidden" data-o-md5="{{$old_md5s}}" name="{{$input_name}}" value="{{$input_value}}">
    <input type="hidden" name="file_not_use_id[]" value="">
    <input type="hidden" name="file_use_id[]" value="">


    <div class="m-uploadfiles">
        @foreach($files as $vo)
        <div class="item clearfix" data-md5="{{$vo['md5']??''}}" data-item="{{$vo['item']??''}}">
            <div class="ext">{{$vo['ext'] ?? ''}}</div>
            <div class="right">
                <div class="name" title="{{$value['original_name'] ?? ''}}">{{$vo['original_name'] ?? ''}}</div>
                <div class="size">{{$vo['size'] ?? ''}}</div>
            </div>
            <a class="prev">⇧</a>
            <a class="next">⇩</a>
            <a class="delete">×</a>
        </div>
        @endforeach
    </div>


    <div class="item clearfix fun-li-clone hide">
        <div class="ext"></div>
        <div class="right">
            <div class="name"></div>
            <div class="size"></div>
        </div>
        <a class="prev">⇧</a>
        <a class="next">⇩</a>
        <a class="delete">×</a>
    </div>
</div>



<script>
    (function(){
        var loading;
        //插件地址：https://github.com/danielm/uploader
        $('#{{$input_id}}_btn').dmUploader({
            url: '/admin/upload/ajax_upload_file',
            dataType: 'json',
            maxFileSize : '{{isset($size)&&$size?$size*1024:(int)ini_get('upload_max_filesize')*1024}}*1024',  //允许上传的大小，单位b
            allowedTypes: '*',
            multiple:false,
            extraData:{
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            onComplete: function(){
                //$.danidemo.addLog('#demo-debug', 'default', 'All pending tranfers completed');
                console.log('All pending tranfers completed');
            },
            onUploadProgress: function(id, percent){
                loading = $boot.loading({text:'文件上传中...'});
                //var percentStr = percent + '%';
                //$.danidemo.updateFileProgress(id, percentStr);
            },
            onUploadSuccess: function(id, res){
                loading.close();
                //如果上传失败
                if(res.status == 0){
                    $boot.warn({text:res.msg});
                }else {
                    //添加到ul后面
                    add_group{{ $input_id }}(res.data.md5,res.data.ext,res.data.original_name,res.data.size,JSON.stringify(res.data));

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
        function add_group{{ $input_id}}($md5,$ext,$original_name,$size,$item){
            var $a = $("#upload-{{$input_id}} .fun-li-clone").clone(true).removeClass("fun-li-clone").removeClass("hide").attr({'data-md5':$md5,'data-item':$item}); //移除所有class
            $a.find('.ext').text($ext);
            $a.find('.name').text($original_name).attr('title',$original_name);
            $a.find('.size').text($size);
            $a.appendTo('#upload-{{$input_id}} .m-uploadfiles');
        };

        //重新计算
        function reset_group{{ $input_id}}(){
            //原md5值赋给not_use
            var $old_md5 = $("input[name='{{$input_name}}']").attr('data-o-md5');
            $("#upload-{{$input_id}}").find("input[name='file_not_use_id[]']").val($old_md5);
            //取新的md5值
            var $new_items =[]; var $new_md5s = [];
            $("#upload-{{$input_id}} .m-uploadfiles .item").each(function(){
                $new_items.push(JSON.parse($(this).attr('data-item')));
                $new_md5s.push($(this).attr('data-md5'));
            });
            $new_items = JSON.stringify($new_items);
            $new_md5s = $new_md5s.join(',');
            //新md5值赋给use
            $("#upload-{{$input_id}}").find("input[name='file_use_id[]']").val($new_md5s);
            //新md5值赋给input
            $("input[name='{{$input_name}}']").val($new_items);
        }

        //只重新计算位置更换（调整顺序情况下）
        function reset_group_sort{{ $input_id}}(){
            //取新的md5值
            var $new_items = [];
            $("#upload-{{$input_id}} .m-uploadfiles .item").each(function(){
                $new_items.push(JSON.parse($(this).attr('data-item')));
            });
            $new_items = JSON.stringify($new_items);
            //新md5值赋给input
            $("input[name='{{$input_name}}']").val($new_items);
        }
        
        //删除当前选中文件
        $("#upload-{{$input_id}} .m-uploadfiles a.delete").click(function(){
            $(this).parent().remove();
            reset_group{{ $input_id}}();
            return false;
        });

        //文件上移
        $("#upload-{{ $input_id }} a.prev").click(function(){
            var $index = $(this).parent().prevAll().length;
            if($index != 0){
                var $target = $(this).parent().prev();
                $(this).parent().insertBefore($target);
            }
            reset_group_sort{{ $input_id}}();
            return false;
        });
        //文件下移
        $("#upload-{{ $input_id }} a.next").click(function(){
            var $index = $(this).parent().nextAll().length;
            if($index != 0){
                var $target = $(this).parent().next();
                $(this).parent().insertAfter($target);
            }
            reset_group_sort{{ $input_id}}();
            return false;
        });



    })();
</script>