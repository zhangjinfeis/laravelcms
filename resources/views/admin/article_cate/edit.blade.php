@extends("admin.include.mother")

@section("content")
    @component('admin.component.breadcrumb',['is_back'=>true])
        @slot('name')
            编辑分类
        @endslot
    @endcomponent
    <div class="h30"></div>

    <div class="nav nav-tabs" role="tablist">
        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-1" role="tab" aria-selected="true">基本信息</a>
        @if($menu->is_able == 1)
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-2" role="tab" aria-selected="false">附加字段</a>
        @endif
    </div>
    <div class="h15"></div>


    <form id="form2">
        <input type="hidden" name="id" value="{{$menu->id}}" />

        <div class="tab-content">
            <div class="tab-pane fade show active" id="nav-1" role="tabpanel">
                <div class="form-group">
                    <label for="name">父级</label>
                    <span class="text-muted pl-2 js-pid">{{$parent->name_cn ?? '根菜单'}}</span>
                </div>
                <input name="parent_id" type="hidden" value="">
                <div class="form-group">
                    <label for="name"><span class="text-danger">* </span>分类名称</label>
                    <input type="text" class="form-control" id="name" name="name_cn" placeholder="菜单名称" style="width:400px;" value="{{$menu->name_cn}}" />
                    <small class="form-text text-muted">1-20个字符</small>
                </div>
                <div class="form-group">
                    <label for="name_cn">英文名称</label>
                    <input type="text" class="form-control" id="name_cn" name="name_en" placeholder="英文名称" style="width:400px;" value="{{$menu->name_en}}" />
                    <small class="form-text text-muted">1-20个字符</small>
                </div>
                <div class="form-group">
                    <label>状态</label>
                    <div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="is_show1" name="is_show" class="custom-control-input" value="1"
                                   @if($menu->is_show == 1)
                                   checked
                                    @endif
                            >
                            <label class="custom-control-label" for="is_show1">开启</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="is_show2" name="is_show" class="custom-control-input" value="9"
                                   @if($menu->is_show == 9)
                                   checked
                                    @endif
                            >
                            <label class="custom-control-label" for="is_show2">关闭</label>
                        </div>
                    </div>
                </div>
                <div class="h10"></div>
                <button type="submit" class="btn btn-primary" onclick="return post_edit();">保存</button>

            </div>
            @if($menu->is_able == 1)
            <div class="tab-pane fade show" id="nav-2" role="tabpanel">
                <div class="mod_cate_edit_001">
                    <a role="button" class="btn btn-sm btn-primary" href="#" onclick="return alert_win();"><i class="fa fa-plus"></i> 新增字段</a>
                    <div class="h10"></div>
                    <table class="table table-bb">
                        <tr>
                            <th>ID</th>
                            <th>字段名</th>
                            <th>键</th>
                            <th>组件类型</th>
                            <th>排序</th>
                            <th>小贴士</th>
                            <th width="140">操作</th>
                        </tr>
                        @foreach($exattr as $vo)
                            <tr class="tr_{{$vo->id}}">
                                <td>{{$vo->id}}</td>
                                <td>{{$vo->name}}</td>
                                <td>{{$vo->key}}</td>
                                <td>
                                    @foreach(config('config.config_type') as $k=>$v)
                                        @if($k == $vo->type)
                                            {{$v}}
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{$vo->sort}}</td>
                                <td>{{$vo->tips}}</td>
                                <td>
                                    <a class="blue" href="{{url('/admin/article_exattr/edit?id='.$vo->id)}}">编辑</a>&nbsp;&nbsp;&nbsp;
                                    <a class="blue" href="#" onclick="delete_attr({{$vo->id}});">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <script>



                    //提交基本信息编辑
                    function post_edit(){
                        $.ajax({
                            type:'post',
                            url:'/admin/article_cate/edit',
                            data:$('#form2').serialize(),
                            success:function(res){
                                if(res.status == 0){
                                    $boot.warn({text:res.msg},function(){
                                        $('input[name='+res.field+']').focus();
                                    });
                                }else{
                                    $boot.success({text:res.msg},function(){
                                        window.location = "{{ url()->previous() }}";
                                    });
                                }
                            }
                        })
                        return false;
                    }

                    function delete_attr(id){
                        $boot.confirm({text:"确定删除？"},function(){
                            $.ajax({
                                type:"post",
                                url:"/admin/article_exattr/ajax_del",
                                data:{id:id},
                                success:function(res){
                                    if(res.status == 1){
                                        $boot.success({text:res.msg},function(){
                                            window.location = "/admin/article_cate/edit?id={{$menu->id}}&on=1";
                                        });
                                    }else{
                                        $boot.error({text:res.msg});
                                    }
                                }
                            });
                        });
                    }

                </script>
            </div>
            @endif
        </div>

    </form>


    <div id="as" style="display: none;">
        <form id="form1">
            <input type="hidden" name="cate_id" value="{{$menu->id}}" />
            <div class="form-group">
                <label><span class="text-danger">* </span>字段名称</label>
                <input type="text" class="form-control" name="name" placeholder="字段名称">
                <small class="form-text text-muted">1-20个字符</small>
            </div>
            <div class="form-group">
                <label><span class="text-danger">* </span>组件类型</label>
                <select class="form-control" name="type" lay-filter="type">
                    @foreach(config('config.config_type') as $k=>$v)
                        <option value="{{$k}}">{{$v}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="key"><span class="text-danger">* </span>键</label>
                <input type="text" class="form-control" id="key" name="key" placeholder="键" value="">
                <small class="form-text text-muted">取值的字段名</small>
            </div>
            <div class="form-group hide js-radio-checkbox">
                <label>单选/复选选项</label>
                <textarea class="form-control" rows="4" name="radio_checkbox_json" placeholder="选项值"></textarea>
                <small class="form-text text-muted">例如，颜色:1-红色，多个选项换行区分</small>
            </div>
            <div class="form-group hide js-width">
                <label>宽度</label>
                <input type="text" class="form-control" name="width" placeholder="宽度" value="">
                <small class="form-text text-muted">图片或编辑器的宽度，0或空值表示不限制</small>
            </div>
            <div class="form-group hide js-height">
                <label>高度</label>
                <input type="text" class="form-control" name="height" placeholder="高度" value="">
                <small class="form-text text-muted">图片或编辑器的高度，0或空值表示不限制</small>
            </div>
            <div class="form-group hide js-size">
                <label>图片允许大小</label>
                <input type="text" class="form-control" name="size" placeholder="图片允许大小" value="">
                <small class="form-text text-muted">单位：M，0或空值表示不限制</small>
            </div>
            <div class="form-group hide js-custom">
                <label>编辑器类型</label>
                <select class="form-control w400" name="custom">
                    @foreach(config('config.ckeditor_custom') as $k=>$v)
                        <option value="{{$k}}">{{$v}}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted"></small>
            </div>
            <div class="form-group">
                <label for="tips">小贴士</label>
                <input type="text" class="form-control" id="tips" name="tips" placeholder="小贴士" value="">
                <small class="form-text text-muted">字段的说明</small>
            </div>
            <div class="form-group">
                <label for="sort"><span class="text-danger">* </span>排序</label>
                <input type="text" class="form-control" id="sort" name="sort" placeholder="排序" value="500">
                <small class="form-text text-muted">默认500,数值越小排名越靠前</small>
            </div>
            <div class="h10"></div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary" onclick="return create_exattr();">新增</button>
            </div>
        </form>
    </div>

    <script>

        //弹出创建菜单窗口
        function alert_win(){
            $boot.win({id:'#as','title':'新增字段'});
            return false;
        }

        //类型切换
        $('select[name=type]').change(function(){
            if($(this).val() == 3 || $(this).val() == 4){
                $('.js-custom').hide();
                $('.js-width').show();
                $('.js-height').show();
                $('.js-size').show();
            }else if($(this).val() == 5){
                $('.js-custom').show();
                $('.js-width').show();
                $('.js-height').show();
                $('.js-size').hide();
            }else if($(this).val() == 6 || $(this).val() == 7){
                $('.js-custom').hide();
                $('.js-radio-checkbox').show();
                $('.js-width').hide().find('input[name=width]').val('');
                $('.js-height').hide().find('input[name=height]').val('');
                $('.js-size').hide();
            }else{
                $('.js-custom').hide();
                $('.js-width').hide().find('input[name=width]').val('');
                $('.js-height').hide().find('input[name=height]').val('');
                $('.js-size').hide().find('input[name=size]').val('');
            }
        });

        //新增附加字段提交
        function create_exattr(){
            var data = $('#form1').serialize();
            var url = "/admin/article_cate/edit?id={{$menu->id}}&on=1";
            $.ajax({
                type:"post",
                url:"/admin/article_exattr/create",
                data:data,
                success:function(res){
                    if(res.status == 1){
                        window.location = url;
                    }else{
                        $boot.error({text:res.msg});
                    }
                }
            });
            return false;
        }
    </script>

    @isset($_GET['on'])
        <script>
            var on_tag = '{{$_GET['on']}}';
            $(".nav-item").removeClass('active').eq(on_tag).addClass('active');
            $(".tab-pane").removeClass('active').eq(on_tag).addClass('active');
        </script>
    @endisset

@endsection