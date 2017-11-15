@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-select2/select2.css')}}"  type="text/css" media="screen"/>
<link rel="stylesheet" href="{{ asset('plugins/jquery-datatable/css/jquery.dataTables.css')}}"  type="text/css" media="screen"/>
<style>
th {
    text-align: center!important;
}
table td.table-bordered{
    border-bottom: 1px solid gray!important;
    border-top: 1px solid gray!important;
}
</style>
<div class="text-center" style="margin: 20px;">

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo_detalles_cotizacion" id="detalles_cotizacion">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" id="titulo_detalles_cotizacion">Detalles de cotización</h2>
                </div>
                <div class="modal-body">
                    <div class="row" id="campos_detalles">

                        <div id="datos_contacto" class="row">
                            <h3 class="">Datos de contacto</h3>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <label class="control-label">Nombre(s)</label>
                                <div class="">
                                    <p class="form-control-static" id="nombre"></p>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <label class="control-label">Apellido(s)</label>
                                <div class="">
                                    <p class="form-control-static" id="apellido"></p>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <div class="">
                                        <p class="form-control-static" id="email"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Teléfono</label>
                                    <div class="">
                                        <p class="form-control-static" id="telefono"></p>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- Fin datos de contacto --}}
                        <div class="progress progress-small">
                            <div class="progress-bar progress-bar-success animate-progress-bar" data-percentage="100%"></div>
                        </div> 

                        <div id="datos_envio" class="row">
                            <h3 class="">Dirección del cliente</h3>
                            <div class="row">
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">Código Postal</label>
                                        <div class="">
                                            <p class="form-control-static" id="codigo_postal"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <label class="control-label">Ciudad</label>
                                    <div class="">
                                        <p class="form-control-static" id="ciudad"></p>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">Estado</label>
                                        <div class="">
                                            <p class="form-control-static" id="estado"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">País</label>
                                        <div class="">
                                            <p class="form-control-static" id="pais"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label">Calle</label>
                                    <div class="">
                                        <p class="form-control-static" id="calle"></p>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- Fin de información de envío --}}
                        <div class="progress progress-small">
                            <div class="progress-bar progress-bar-success animate-progress-bar" data-percentage="100%"></div>
                        </div> 

                        <div id="detalles_cotizacion" class="row">
                            <h3 class="">Productos</h3>
                            <div class="col-sm-12 col-md-12 col-xs-12">
                                <table class="table table-responsive" id="detalle_cotizacion">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Aquí va el contenido de los productos --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>{{-- Fin datos generales --}}

                    </div>
                    <div class="row hide" id="load_bar">
                        <span><i class="fa fa-cloud-download fa-7x" aria-hidden="true"></i></span><br>
                        <h3>Cargando detalles de la cotización, espere...</h3>
                        <div class="col-xs-12 col-sm-8 col-sm-push-2 col-sm-pull-2 col col-md-6 col-md-push-3 col-md-pull-3">
                            <div class="progress transparent progress-large progress-striped active no-radius no-margin">
                                <div data-percentage="100%" class="progress-bar progress-bar-success animate-progress-bar"></div>       
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <h2>Lista de cotizaciones</h2>

    <div class="row-fluid">
        <div class="span12">
            <div class="grid simple ">
                <div class="grid-title">
                    <div class="grid-body">
                        <div class="table-responsive">
                            <table class="table" id="example3">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Status</th>
                                        <th>Fecha cotización</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($cotizaciones) > 0)
                                        @foreach($cotizaciones as $cotizacion)
                                            <tr class="" id="{{$cotizacion->id}}">
                                                <td>{{$cotizacion->id}}</td>
                                                <td>{{$cotizacion->nombre}} {{$cotizacion->apellido}}</td>
                                                <td><?php echo $cotizacion->status == '0' ? '<span class="label label-important">Pendiente</span>' : '<span class="label label-success">Atendido</span>';?></td>
                                                <td>{{$cotizacion->created_at}}</td>
                                                <td>
                                                    <button type="button" class="btn btn-info ver_cotizacion"><i class="fa fa-info" aria-hidden="true"></i> Detalles</button>
                                                    @if($cotizacion->status == '0')
                                                        <button type="button" class="btn btn-success finalizar_cotizacion"><i class="fa fa-check" aria-hidden="true"></i> Finalizar</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <td colspan="7">No hay cotizaciones disponibles</td>
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
<script src="{{ asset('js/cotizacionesAjax.js') }}"></script>
<script type="text/javascript">

$('body').delegate('.ver_cotizacion','click', function() {
    var cotizacion_id = $(this).parent().siblings("td:nth-child(1)").text();
    var token = $("#token").val();

    $('div#campos_detalles').addClass('hide');
    $('div#load_bar').removeClass('hide');
    $('#detalles_cotizacion').modal();
    obtenerDetalleCotizacion(cotizacion_id,token);
});

$('body').delegate('.finalizar_cotizacion','click', function() {
    var cotizacion_id = $(this).parent().siblings("td:nth-child(1)").text();
    var td_status = $(this).parent().siblings("td:nth-child(3)");
    var token = $("#token").val();

    swal({
        title: "¿Realmente desea marcar como atendida la cotización con el id <span style='color:#8CD4F5'>" + cotizacion_id + "</span>?",
        text: "¡No podrá deshacer esta acción!",
        html: true,
        type: "info",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Si, continuar",
        showLoaderOnConfirm: true,
        allowEscapeKey: true,
        allowOutsideClick: true,
        closeOnConfirm: false
    },
    function() {
        finalizarCotizacion(cotizacion_id,td_status,token);
    });
});

</script>
@endsection