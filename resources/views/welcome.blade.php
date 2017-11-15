<!DOCTYPE html>
<html>
    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Login</title>

        <link rel="stylesheet" href="{{ asset('plugins/pace/pace-theme-flash.css')}}"  type="text/css" media="screen"/>
        <link rel="stylesheet" href="{{ asset('plugins/boostrapv3/css/bootstrap.min.css')}}"  type="text/css"/>
        <link rel="stylesheet" href="{{ asset('css/style.css')}}" type="text/css"/>
        <style type="text/css">
        /* Change the white to any color ;) */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0px 1000px white inset !important;
        }
        </style>
    </head>
    <body>
        <div class="container centered">
            <div class="col-lg-12 text-center">
                <div class="row tiles-container m-b-10">
                    <div class="col-xs-12 col-sm-8 col-sm-push-2 col-sm-pull-2 col col-md-6 col-md-push-3 col-md-pull-3">
                        <div class="tiles white p-t-20 p-l-15 p-r-15 p-b-30">
                            <h2 class="text-center">Inicio de sesión <span class="semi-bold text-success">Bely</span></h2>
                            <form class="m-t-30 m-l-15 m-r-15" method="POST" action="login" autocomplete="off">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label class="form-label">Usuario</label>
                                    <div class="controls">
                                        <input type="text" class="form-control" id="user" name="user" placeholder="Escribe tu usuario">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Contraseña</label>
                                    <div class="controls">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Escribe tu contraseña">
                                    </div>
                                </div>
                                <button class="btn btn-block btn-primary m-t-10" type="submit"><i class="icon-cloud-download"></i>Entrar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <script src="{{ asset('js/jquery.js') }}"></script>
        <script src="{{ asset('js/sweetalert.min.js') }}"></script>
        <script src="{{ asset('plugins/boostrapv3/js/bootstrap.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('plugins/pace/pace.min.js') }}" type="text/javascript"></script>
    </body>
</html>
