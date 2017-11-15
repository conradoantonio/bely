@extends('admin.main')

@section('content')
{{-- <link rel="stylesheet" href="{{ asset('plugins/bootstrap-select2/select2.css')}}"  type="text/css" media="screen"/> --}}
<link rel="stylesheet" href="{{ asset('plugins/jquery-datatable/css/jquery.dataTables.css')}}"  type="text/css" media="screen"/>
<style>
th {
    text-align: center!important;
}
/* Cambia el color de fondo de los input con autofill */
input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0px 1000px white inset !important;
}
</style>
<div class="text-center" style="margin: 20px;">

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo-form-usuario-sistema" id="formulario-usuario-sistema">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titulo-form-usuario-sistema">Nuevo usuario (sistema)</h4>
                </div>
                <form id="form_usuario_sistema" action="<?php echo url();?>/usuarios/sistema/guardar_usuario" enctype="multipart/form-data" method="POST" autocomplete="off">
                    <input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}" base-url="<?php echo url();?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12 hidden">
                                <div class="form-group">
                                    <label for="id">ID</label>
                                    <input type="text" class="form-control" id="id" name="id">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12 hidden">
                                <div class="form-group">
                                    <label for="user">Nombre usuario</label>
                                    <input type="text" class="form-control" id="user_name_old" name="user_name_old" placeholder="Usuario">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="user">Nombre usuario</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Usuario">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="password">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="user">Nombre (s)</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="user">Apellido (s)</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="user">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select class="form-control" id="empresa" name="empresa">
                                        <option value="0">Elija una opción</option>
                                        @foreach($empresas as $empresa)
                                            <option value="{{$empresa->id}}">{{$empresa->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="input_foto_usuario">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="foto-usuario">Foto usuario</label>
                                    <input type="file" class="form-control" id="foto_usuario" name="foto_usuario">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="foto_usuario">
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label>Foto actual</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="guardar-usuario-sistema">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    @if(isset($_GET['valido']) && $_GET['valido'] == md5('false'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
            <strong>Error guardando el usuario</strong> El nombre de usuario especificado no es válido ya que se encuentra ocupado, trate con uno distinto.
        </div>
    @endif
    <h2>Lista de usuarios del panel</h2>

    <div class="row-fluid">
        <div class="span12">
            <div class="grid simple ">
                <div class="grid-title">
                    <h4>Opciones <span class="semi-bold">adicionales</span></h4>
                    <div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#formulario-usuario-sistema" id="nuevo-usuario"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo usuario (sistema)</button>
                    </div>
                    <div class="grid-body ">
                        <div class="table-responsive">
                            <table class="table table-hover" id="example3">
                                <thead class="centered">    
                                    <th class="hide">ID</th>
                                    <th>User</th>
                                    <th class="hide">Nombre</th>
                                    <th class="hide">Apellido</th>
                                    <th>Email</th>
                                    <th class="hide">Foto usuario</th>
                                    <th>Fecha registro</th>
                                    <th>Empresa</th>
                                    <th class="hide">Empresa_id</th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody id="tabla-usuarios-sistema" class="">
                                    @if(count($usuarios) > 0)                           
                                        @foreach($usuarios as $usuario)
                                            <tr class="" id="{{$usuario->id}}">
                                                <td class="hide">{{$usuario->id}}</td>
                                                <td>{{$usuario->user}}</td>
                                                <td class="hide">{{$usuario->nombre}}</td>
                                                <td class="hide">{{$usuario->apellido}}</td>
                                                <td>{{$usuario->email}}</td>
                                                <td class="hide">{{$usuario->foto_usuario}}</td>
                                                <td>{{$usuario->created_at}}</td>
                                                <td><?php echo ($usuario->empresa_id == '1' ? '<span class="label">BELY</span>' : ($usuario->empresa_id == '2' ? '<span class="label label-warning">GDLBOX</span>' : '<span class="label label-success">Cosmeticos & Co</span>')) ?></td>
                                                <td class="hide">{{$usuario->empresa_id}}</td>
                                                <td>
                                                    <button type="button" class="btn btn-info editar-usuario">Editar</button>
                                                    <button type="button" class="btn btn-danger eliminar-usuario-sistema">Borrar</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <td colspan="5">No hay usuarios disponibles</td>
                                    @endif  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('plugins/jquery-datatable/js/jquery.dataTables.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/datatables-responsive/js/datatables.responsive.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/datatables-responsive/js/lodash.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/usuariosSistemaAjax.js') }}"></script>
<script src="{{ asset('js/validacionesUsuariosSistema.js') }}"></script>
<script type="text/javascript">

$('#formulario-usuario-sistema').on('hidden.bs.modal', function (e) {
    $('#formulario-usuario-sistema div.form-group').removeClass('has-error');
    $('input#foto_usuario').val('');
    $('input.form-control').val('');
    $('select.form-control').val(0);
    $('#formulario-usuario-sistema div#usuario_caracteristicas').show();
});

$('body').delegate('button#nuevo-usuario','click', function() {
    $('input.form-control').val('');
    $('div#foto_usuario').hide();
    $("div#input_foto_usuario").hide();
    $("h4#titulo-form-usuario-sistema").text('Nuevo usuario (sistema)');
});

$('body').delegate('.editar-usuario','click', function() {

    $('input.form-control').val('');
    id = $(this).parent().siblings("td:nth-child(1)").text(),
    user = $(this).parent().siblings("td:nth-child(2)").text(),
    nombre = $(this).parent().siblings("td:nth-child(3)").text(),
    apellido = $(this).parent().siblings("td:nth-child(4)").text(),
    email = $(this).parent().siblings("td:nth-child(5)").text(),
    imagen = $(this).parent().siblings("td:nth-child(6)").text(),
    empresa = $(this).parent().siblings("td:nth-child(9)").text();

    $("h4#titulo-form-usuario-sistema").text('Editar usuario');
    $("div#input_foto_usuario").show();
    $("#formulario-usuario-sistema input#id").val(id);
    $("#formulario-usuario-sistema input#user_name").val(user);
    $("#formulario-usuario-sistema input#user_name_old").val(user);
    $("#formulario-usuario-sistema input#nombre").val(nombre);
    $("#formulario-usuario-sistema input#apellido").val(apellido);
    $("#formulario-usuario-sistema input#email").val(email);
    $("#formulario-usuario-sistema select#empresa").val(empresa);

    $('#formulario-usuario-sistema div#usuario_caracteristicas').hide();

    $('div#foto_usuario').children().children().children().remove('img#foto_usuario');
    $('div#foto_usuario').children().children().append(
        "<img src='<?php echo asset('');?>/"+imagen+"' class='img-responsive img-thumbnail' alt='Responsive image' id='foto_usuario'>"
    );
    $("div#foto_usuario").show();

    $('#formulario-usuario-sistema').modal();
});

$('body').delegate('.eliminar-usuario-sistema','click', function() {
    var usuario = $(this).parent().siblings("td:nth-child(2)").text();
    var token = $("#token").val();
    var id = $(this).parent().parent().attr('id');

    swal({
        title: "¿Realmente desea eliminar al usuario <span style='color:#F8BB86'>" + usuario + "</span>?",
        text: "¡Cuidado!",
        html: true,
        type: "warning",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Si, continuar",
        showLoaderOnConfirm: true,
        allowEscapeKey: true,
        allowOutsideClick: true,
        closeOnConfirm: false
    },
    function() {
        eliminarUsuarioSistema(id,token);
    });
});

</script>
@endsection