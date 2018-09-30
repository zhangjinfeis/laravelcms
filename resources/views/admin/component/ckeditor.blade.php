{{--
编辑器参数：
必选：input_id,input_name
可选：width,height,custom,pic_max_width(图片最大宽度，默认1000)
值：input_value

**custom:编辑器类型,对应ckeditor/custom下的js名称
--}}
<textarea id="{{$input_id}}" name="{{$input_name}}" class="ckeditor">{{$input_value or ""}}</textarea>
<input id="editor_old_{{$input_id}}" type="hidden" value="{{$input_value or ""}}" />
<input id="editor_no_{{$input_id}}" name="editor_not_use[]" type="hidden" value="" />
<input id="editor_{{$input_id}}" name="editor_use[]" type="hidden" value="" />
<script>
    var editor_{{$input_id}} = CKEDITOR.replace( '{{$input_id}}', {
        language: 'zh-cn'
        ,filebrowserImageUploadUrl:'{{url('admin/upload/ajax_ckeditor_img')}}?_token={{csrf_token()}}&width={{$pic_max_width or '1000'}}'
        @if(isset($height)&&$height)
        ,height:'{{$height}}px'
        @else
        ,height:'400px'
        @endif
        @if(isset($width)&&$width)
        ,width:'{{$width}}px'
        @else
        ,width:'100%'
        @endif
        @if(isset($custom))
        ,customConfig : 'custom/ckeditor_{{$custom}}.js'
        @endif
    });
    //$('.cke_1.cke_chrome').css({'border-color':'#dee2e6'});
    //$('.cke_1 .cke_top').css({'border-bottom-color':'#dee2e6'})
    editor_{{$input_id}}.on('change',function(){
        var bd = editor_{{$input_id}}.getData();
        $("#{{$input_id}}").val(bd);
        $("#editor_no_{{$input_id}}").val($("#editor_old_{{$input_id}}").val());
        $("#editor_{{$input_id}}").val(bd);
    });
</script>
