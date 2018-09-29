@extends("admin.include.mother")

@section("content")
    @component('admin.component.breadcrumb')
        @slot('name')
            留言
        @endslot
    @endcomponent
    <div class="h20"></div>

    <table class="table table-sm table-hover table-bb">

            <tr>
                <th>ID</th>
                <th>留言者称呼</th>
                <th>电话</th>
                <th>email</th>
                <th>留言内容</th>
                <th>留言时间</th>
            </tr>

        @foreach($list as $vo)
            <tr>
                <td>{{$vo->id}}</td>
                <td>
                    {{$vo->name}}
                </td>
                <td>
                    {{$vo->phone}}
                </td>
                <td>
                    {{$vo->email}}
                </td>
                <td>
                    {{$vo->body}}
                </td>
                <td>
                    {{$vo->created_at->format('Y-m-d H:i')}}

                </td>
            </tr>
        @endforeach

    </table>
    <div class="pagination-warp mt10">
        {{$list->links()}}
    </div>
@endsection