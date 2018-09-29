@extends("admin.include.mother")

@section('page')

    <div class="page-index">
        <div class="row">
            <div class="col-4">
                <div class="card d-block">
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
                        <h5 class="card-title mt15">上次登录时间</h5>
                        <div class="card-text">{{date('Y-m-d H:i:s',$admin->last_login_at)}}</div>


                    </div>
                </div>
            </div>
            <div class="col-8">
                One of three columns
            </div>
        </div>
    </div>
@stop