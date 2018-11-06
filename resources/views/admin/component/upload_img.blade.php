{{--
上传单张图片组件-调用方法:
    @include('admin.component.uploadImg',array())
    必填：input_id、input_name
    可选：size、width、height
    赋值：input_value
--}}


@php
    $input_value = isset($input_value)&&$input_value?$input_value:'';
    if($input_value){
        preg_match_all('/[a-z0-9]{32}/',$input_value,$res);
        $md5 = $res[0][0];
    }
@endphp


<style>
.m-uploadimg-one{width:110px;height:110px;overflow:hidden;display:table-cell; text-align:center;vertical-align:middle;border:#dee2e6 1px solid; background:#fff;position: relative; background: url('/resources/admin/images/pic_bg.png');}
.m-uploadimg-one img{max-width:110px;max-height:110px; display: inline;}
.m-uploadimg-one span.size{display:inline-block;padding:0 5px;background:rgba(0,0,0,0.5);line-height:18px;height:18px;overflow:hidden;position:absolute;left:5px;top:5px;border-radius:9px;color:#eee;font-size:10px;display:none;}
.m-uploadimg-one span.size-show{ display: inline-block;}
.m-uploadimg-one a.delete{display:inline-block;line-height:20px;height:20px;width:20px;border-radius:10px;text-align:center;overflow:hidden;position:absolute;right:5px;top:5px;text-decoration:none;background: rgba(0,0,0,0.5);font-size:14px;color:#fff; display:none; cursor: pointer;}
.m-uploadimg-one a.delete:hover{color:#fff;background: rgba(0,0,0,0.8);}
.m-uploadimg-one a.delete-show{display:block;}
</style>
<div id="upload-{{$input_id}}">

    <label id="{{$input_id}}_btn" class="u-dmuploader">
        <span role="button" class="btn btn-sm btn-primary w110"><span class="fa fa-upload"></span> 上传图片</span>
        <input type="file" name="files[]" class="hide">
    </label>


    <input type="hidden" data-o-md5="{{$md5 ?? ''}}" name="{{$input_name}}" value="{{$input_value}}">
    <input type="hidden" name="pic_not_use_id[]" value="">
    <input type="hidden" name="pic_use_id[]" value="">

    <div class="m-uploadimg-one">
        <img id="{{$input_id}}_img" src="{{$input_value == ''?'/resources/admin/images/nopicture.png':$input_value}}">
        <span class="size"></span>
        <a class="delete @if(isset($input_value)&&$input_value) delete-show @endif" data="/resources/admin/images/nopicture.png">×</a>
    </div>

</div>



<script>
(function(){
    var loading;
    //插件地址：https://github.com/danielm/uploader
    $('#{{$input_id}}_btn').dmUploader({
        url: '/admin/upload/ajax_upload_img',
        dataType: 'json',
        maxFileSize : '{{isset($size)&&$size?$size*1024:(int)ini_get('upload_max_filesize')*1024}}*1024',  //允许上传的大小，单位b
        allowedTypes: 'image/*',
        extFilter: ["jpg", "jpeg", "png", "gif"],
        multiple:false,
        extraData:{
            _token: $('meta[name="csrf-token"]').attr('content'),
            width : '{{$width ?? 0}}',
            height : '{{$height ?? 0}}',
        },
        onComplete: function(){
            //$.danidemo.addLog('#demo-debug', 'default', 'All pending tranfers completed');
            console.log('All pending tranfers completed');
        },
        onUploadProgress: function(id, percent){
            loading = $boot.loading({text:'图片上传中...'});
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
                reset_group{{ $input_id}}(res.data.md5,res.data.path)

                //显示图片
                $("#{{$input_id}}_img").attr('src',res.data.path);
                $("#upload-{{$input_id}}").find('.size').addClass('size-show').text(res.data.width+'×'+res.data.height);
                $("#upload-{{$input_id}}").find('.delete').addClass('delete-show');
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
    $("#upload-{{$input_id}} .m-uploadimg-one a.delete").click(function(){
        var nopicture = $(this).attr('data');
        $("#upload-{{$input_id}} .m-uploadimg-one").find('img').attr({'src':nopicture});
        $("#upload-{{$input_id}}").find('input[name="{{ $input_name }}"]').val('');
        $("#upload-{{$input_id}} .m-uploadimg-one span.size").removeClass('size-show');
        $("#upload-{{$input_id}}").find('.delete').removeClass('delete-show');
        //原md5值赋给not_use
        var $old_md5 = $("input[name='{{$input_name}}']").attr('data-o-md5');
        $("#upload-{{$input_id}}").find("input[name='pic_not_use_id[]']").val($old_md5);
    });

    //上传图片后重新赋值
    function reset_group{{ $input_id}}($new_md5,$path){
        //原md5值赋给not_use
        var $old_md5 = $("input[name='{{$input_name}}']").attr('data-o-md5');
        $("#upload-{{$input_id}}").find("input[name='pic_not_use_id[]']").val($old_md5);
        //新md5值赋给use
        $("#upload-{{$input_id}}").find("input[name='pic_use_id[]']").val($new_md5);
        //新md5值赋给input
        $("input[name='{{$input_name}}']").val($path);
    }
})();
</script>