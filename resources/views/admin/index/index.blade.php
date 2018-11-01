@extends("admin.include.mother")

@section('page')
    <script src="/resources/plugs/chartjs/Chart.min.js"></script>


    <div class="page-index">
        <div class="row">
            <div class="col-4 pl10 pr10">
                <div class="card d-block">
                    <div class="card-body">
                        <h5 class="card-title mt15">站点名称</h5>
                        <div class="card-text">{{$config['sitename']}}</div>
                        <h5 class="card-title mt15">当前管理员</h5>
                        <div class="card-text">{{$admin->name}}</div>
                        <h5 class="card-title mt15">上次登录时间</h5>
                        <div class="card-text">{{date('Y-m-d H:i:s',$admin->last_login_at)}}</div>
                    </div>
                </div>
                <div class="card d-block mt15">
                    <div class="card-body">
                        <h5 class="card-title">系统类型及版本号</h5>
                        <div class="card-text">{{php_uname()}}</div>
                        <h5 class="card-title mt15">PHP版本号</h5>
                        <div class="card-text">{{PHP_VERSION}}</div>
                        <h5 class="card-title mt15">域名</h5>
                        <div class="card-text">{{$_SERVER["HTTP_HOST"]}}</div>
                        <h5 class="card-title mt15">主机IP</h5>
                        <div class="card-text">{{GetHostByName($_SERVER['SERVER_NAME'])}}</div>
                        <h5 class="card-title mt15">运行环境</h5>
                        <div class="card-text">{{$_SERVER['SERVER_SOFTWARE']}}</div>
                    </div>
                </div>
            </div>
            <div class="col-8 pl10 pr10">
                <div class="card d-block">
                    <div class="card-body">
                        <div class="m-index-card flex">
                            <div class="item flex-1">
                                <span class="icon"><i class="fa fa-file-text-o"></i></span>
                                <div class="title">文章数</div>
                                <div class="number">{{$article_count}}</div>
                                <a href="#" onclick="return window.parent.alert_iframe('文章列表','/admin/article','{{md5("/admin/article")}}')">查看</a>
                            </div>
                            <div class="item flex-1">
                                <span class="icon"><i class="fa fa-link"></i></span>
                                <div class="title">链接数</div>
                                <div class="number">{{$link_count}}</div>
                                <a href="#" onclick="return window.parent.alert_iframe('链接列表','/admin/link','{{md5("/admin/link")}}')">查看</a>
                            </div>
                            <div class="item flex-1">
                                <span class="icon"><i class="fa fa-commenting-o"></i></span>
                                <div class="title">留言数</div>
                                <div class="number">{{$guestbook_count}}</div>
                                <a href="#" onclick="return window.parent.alert_iframe('留言内容','/admin/guestbook','{{md5("/admin/guestbook")}}')">查看</a>
                            </div>
                            <div class="item flex-1">
                                <span class="icon"><i class="fa fa-map-o"></i></span>
                                <div class="title">地图数</div>
                                <div class="number">{{$map_count}}</div>
                                <a href="#" onclick="return window.parent.alert_iframe('地图列表','/admin/map','{{md5("/admin/map")}}')">查看</a>
                            </div>
                            <div class="item flex-1">
                                <span class="icon"><i class="fa fa-user-circle-o"></i></span>
                                <div class="title">管理员</div>
                                <div class="number">{{$manager_count}}</div>
                                <a href="#" onclick="return window.parent.alert_iframe('管理员','/admin/manager_user','{{md5("/admin/manager_user")}}')">查看</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card d-block mt15">
                    <div class="card-body">
                        <div style="">
                            <canvas id="myChart" width="800" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <script>
                    //图标宽度
                    (function(){
                        var w = $('#myChart').parent().width();
                        $('#myChart').css({width:w});
                    })();
                    //图标绘制
                    var ctx = document.getElementById("myChart");
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($labels) !!},
                            datasets: [{
                                label: '近一周文章发布情况',
                                data: {!! json_encode($data) !!},
                                fill:false,
                                borderWidth:10,
                                lineTension:0,
                                pointBorderColor:'#fa5c7c',
                                borderColor:'#fa5c7c',
                                backgroundColor:'rgba(250,92,124,0.1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero:true
                                    }
                                }]
                            }
                        }
                    });
                </script>

                <div class="card d-block mt15">
                    <div class="card-body">
                        <h5 class="card-title">最近发布</h5>
                        <table class="table table-hover">
                            <tr>
                                <th width="40%">标题</th>
                                <th>缩略图</th>
                                <th>发布时间</th>
                                <th>操作</th>
                            </tr>
                            @foreach($article as $vo)
                                <tr>
                                    <td>{{$vo->title}}</td>
                                    <td>
                                        @if(!empty($vo->thumb))
                                            <img src="{{$vo->thumb}}" height="40" />
                                        @else
                                            <span class="badge badge-secondary-lighten badge-pill">无</span>
                                        @endif
                                    </td>
                                    <td>{{$vo->created_at->format('Y-m-d H:i')}}</td>
                                    <td>
                                        <a class="btn btn-sm btn-light" href="{{url('/admin/article/edit?id='.$vo['id'])}}" role="button" title="编辑"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>
                </div>



            </div>
        </div>
    </div>
@stop