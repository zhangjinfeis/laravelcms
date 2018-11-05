@foreach($exattr as $vo)
    <div class="form-group">
        <label>{{$vo['name']}}</label>
        @switch($vo['type'])
            @case(1)
                <input type="text" class="form-control w400" name="exattr[{{$vo['key']}}]" placeholder="{{$vo['name']}}" value="{{$vo['value'] ?? ''}}" />
            @break
            @case(2)
                <textarea class="form-control w600" rows="3" name="exattr[{{$vo['key']}}]" placeholder="{{$vo['name']}}">{{$vo['value'] ?? ''}}</textarea>
            @break

            @case(3)
                @component('admin.component.upload_img',array("input_id"=>md5($vo['key']),"input_name"=>"exattr[".$vo['key']."]",'width'=>$vo['width'],'height'=>$vo['height'],'input_value'=>$vo['value']??'','size'=>$vo['size']??''))@endcomponent
            @break

            @case(4)
            @component('admin.component.upload_imgs',array("input_id"=>md5($vo['key']),"input_name"=>"exattr[".$vo['key']."]",'width'=>$vo['width'],'height'=>$vo['height'],'input_value'=>$vo['value']??'','size'=>$vo['size']??''))@endcomponent
            @break

            @case(5)
            @component('admin.component.ckeditor',array("input_id"=>md5($vo['key']),"input_name"=>"exattr[".$vo['key']."]",'width'=>$vo['width'],'height'=>$vo['height'],'input_value'=>$vo['value']??'','custom'=>$vo['custom']??''))@endcomponent
            @break

            @case(6)
            <div>
                @foreach($vo['radio_checkbox_json'] as $item)
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="{{$vo['key']}}{{$item[0]??''}}" name="exattr[{{$vo['key']}}]" value="{{$item[0] ?? ''}}" @if(isset($vo['value'])&&$vo['value']==$item[0]) checked @endif>
                        <label class="custom-control-label" for="{{$vo['key']}}{{$item[0]??''}}">{{$item[1]??''}}</label>
                    </div>
                @endforeach
            </div>
            @break
            @case(7)
            <div>
                @foreach($vo['radio_checkbox_json'] as $item)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="exattr[{{$vo['key']}}][]"  value="{{$item[0]??''}}" id="{{$vo['key']}}{{$item[0]}}" @if(isset($vo['value'])&&in_array($item[0],$vo['value'])) checked @endif>
                        <label class="custom-control-label" for="{{$vo['key']}}{{$item[0]??''}}">{{$item[1]??''}}</label>
                    </div>
                @endforeach
            </div>
            @break
        @endswitch
        <small class="form-text text-muted">
            @if($vo['tips'])
                {{$vo['tips']}}&nbsp;&nbsp;&nbsp;
            @endif
        </small>
    </div>
@endforeach