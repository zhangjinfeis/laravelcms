{{--
上传单张图片组件-调用方法:
    @include('admin.component.upload_file',array())
    必填：input_id、input_name
    可选：size
    赋值：input_value(json字符串)
--}}


@php
    $input_value = isset($input_value) ? $input_value : '';
        $value = json_decode($input_value,true);
@endphp


<style>
    .m-uploadfile-one{width:260px;height:50px;overflow:hidden;border:#dee2e6 1px solid; background:#fff;position: relative;}
    .m-uploadfile-one .ext{width:50px;height: 50px; line-height: 50px; text-align: center; float: left;border-right:#dee2e6 1px solid; font-size: 14px; background:#e9ecef;}
    .m-uploadfile-one .right{ float: left;width: 170px;margin:5px 0 0 10px;}
    .m-uploadfile-one .right .name{ word-break: break-all; height: 18px; line-height: 18px; overflow: hidden;}
    .m-uploadfile-one a.delete{display:inline-block;line-height:20px;height:20px;width:20px;border-radius:10px;text-align:center;overflow:hidden;position:absolute;right:5px;top:5px;text-decoration:none;background: rgba(0,0,0,0.5);font-size:14px;color:#fff;cursor: pointer;}
    .m-uploadfile-one a.delete:hover{color:#fff;background: rgba(0,0,0,0.8);}
</style>
<div id="upload-{{$input_id}}">

    <label id="{{$input_id}}_btn" class="u-dmuploader">
        <span role="button" class="btn btn-sm btn-primary w110"><span class="fa fa-upload"></span> 上传文件</span>
        <input type="file" name="files[]" class="hide">
    </label>


    <input type="hidden" data-o-md5="{{$value['md5'] ?? ''}}" name="{{$input_name}}" value="{{$input_value}}">
    <input type="hidden" name="file_not_use_id[]" value="">
    <input type="hidden" name="file_use_id[]" value="">

    <div class="m-uploadfile-one clearfix {{$input_value?'':'hide'}}">
        <div class="ext">{{$value['ext'] ?? ''}}</div>
        <div class="right">
            <div class="name">{{$value['original_name'] ?? ''}}</div>
            <div class="size">{{$value['size'] ?? ''}}</div>
        </div>
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
                    //赋值操作
                    reset_group{{ $input_id}}(res.data.md5,JSON.stringify(res.data))

                    $("#upload-{{$input_id}}").find('.m-uploadfile-one').removeClass('hide');
                    $("#upload-{{$input_id}}").find('.ext').text(res.data.ext);
                    $("#upload-{{$input_id}}").find('.name').text(res.data.original_name);
                    $("#upload-{{$input_id}}").find('.size').text(res.data.size);
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

        //删除当前选中缩略图
        $("#upload-{{$input_id}} .m-uploadfile-one a.delete").click(function(){

            $("#upload-{{$input_id}}").find('input[name="{{ $input_name }}"]').val('');
            $("#upload-{{$input_id}} .m-uploadfile-one").addClass('hide');
            //原md5值赋给not_use
            var $old_md5 = $("input[name='{{$input_name}}']").attr('data-o-md5');
            $("#upload-{{$input_id}}").find("input[name='file_not_use_id[]']").val($old_md5);
        });

        //上传图片后重新赋值
        function reset_group{{ $input_id}}($new_md5,$json){
            //原md5值赋给not_use
            var $old_md5 = $("input[name='{{$input_name}}']").attr('data-o-md5');
            $("#upload-{{$input_id}}").find("input[name='file_not_use_id[]']").val($old_md5);
            //新md5值赋给use
            $("#upload-{{$input_id}}").find("input[name='file_use_id[]']").val($new_md5);
            //新md5值赋给input
            $("input[name='{{$input_name}}']").val($json);
        }
    })();
</script>