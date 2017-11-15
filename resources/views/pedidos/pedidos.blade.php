@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-select2/select2.css')}}"  type="text/css" media="screen"/>
<link rel="stylesheet" href="{{ asset('plugins/jquery-datatable/css/jquery.dataTables.css')}}"  type="text/css" media="screen"/>
<style>
textarea {
    resize: none;
}
th {
    text-align: center!important;
}
label.control-label{
    font-weight: bold;
}
table td.table-bordered{
    border-bottom: 1px solid gray!important;
    border-top: 1px solid gray!important;
}
</style>
<div class="text-center" style="margin: 20px;">

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo_detalles_pedido" id="detalles_pedido">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" id="titulo_detalles_pedido">Detalles de pedido</h2>
                </div>
                <div class="modal-body">
                    <div class="row" id="campos_detalles">
                        {{-- <ul class="list-group">
                            <li class="list-group-item active">Datos generales de la orden</li>
                            <li class="list-group-item">
                                <span class="label_show">Número de orden: <span id="order-number"></span></span>
                            </li>
                        </ul> --}}

                        <div id="datos_generales" class="row">
                            <h3 class="">Datos generales de la orden</h3>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <label class="control-label">Id orden</label>
                                <div class="">
                                    <p class="form-control-static" id="order_id"></p>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Status</label>
                                    <div class="">
                                        <p class="form-control-static" id="payment_status"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Moneda</label>
                                    <div class="">
                                        <p class="form-control-static" id="currency"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Total</label>
                                    <div class="">
                                        <p class="form-control-static" id="total"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Número de referencia</label>
                                    <div class="">
                                        <p class="form-control-static" id="num_referencia"></p>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- Fin datos generales --}}
                        <div class="progress progress-small">
                            <div class="progress-bar progress-bar-success animate-progress-bar" data-percentage="100%"></div>
                        </div> 

                        <div id="datos_contacto" class="row">
                            <h3 class="">Datos de contacto</h3>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <div class="form-group">
                                    <label class="control-label">Customer Id</label>
                                    <div class="">
                                        <p class="form-control-static" id="customer_id"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-xs-6">
                                <label class="control-label">Nombre</label>
                                <div class="">
                                    <p class="form-control-static" id="name"></p>
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
                                        <p class="form-control-static" id="phone"></p>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- Fin datos de contacto --}}
                        <div class="progress progress-small">
                            <div class="progress-bar progress-bar-success animate-progress-bar" data-percentage="100%"></div>
                        </div> 

                        <div id="datos_envio" class="row">
                            <h3 class="">Información de envío</h3>
                            <div class="row">
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <label class="control-label">Receptor</label>
                                    <div class="">
                                        <p class="form-control-static" id="shpping_c_receiver"></p>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">Teléfono</label>
                                        <div class="">
                                            <p class="form-control-static" id="shpping_c_phone"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">Guía</label>
                                        <div class="">
                                            <p class="form-control-static" id="num_guia"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">Costo de envío</label>
                                        <div class="">
                                            <p class="form-control-static" id="costo_envio"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <label class="control-label">Ciudad</label>
                                    <div class="">
                                        <p class="form-control-static" id="shpping_c_city"></p>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">Estado</label>
                                        <div class="">
                                            <p class="form-control-static" id="shpping_c_state"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">País</label>
                                        <div class="">
                                            <p class="form-control-static" id="shpping_c_country"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-md-3 col-xs-6">
                                    <div class="form-group">
                                        <label class="control-label">Código Postal</label>
                                        <div class="">
                                            <p class="form-control-static" id="shpping_c_postal_code"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label">Calle 1</label>
                                    <div class="">
                                        <p class="form-control-static" id="shipping_c_street1"></p>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- Fin de información de envío --}}
                        <div class="progress progress-small">
                            <div class="progress-bar progress-bar-success animate-progress-bar" data-percentage="100%"></div>
                        </div> 

                        <div id="detalles_pedido" class="row">
                            <h3 class="">Productos</h3>
                            <div class="col-sm-12 col-md-12 col-xs-12">
                                <table class="table table-responsive" id="detalle_pedido">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th>Medida</th>
                                            <th>Precio Unitario</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal Producto</th>
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
                        <h3>Cargando información desde conekta, espere.</h3>
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


    <h2>Listado de pedidos</h2>

    <div class="row-fluid">
        <div class="span12">
            <div class="grid simple ">
                <div class="grid-title">
                    <div class="grid-body">
                        <div class="table-responsive">
                            <table class="table" id="example3">
                                <thead>
                                    <tr>
                                        <th>ID orden</th>
                                        <th>Fecha</th>
                                        <th>Costo total</th>
                                        <th>Tipo envío</th>
                                        <th>Guía</th>
                                        <th>Método pago</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($pedidos))
                                        @foreach($pedidos as $pedido)
                                            <tr class="" id="{{$pedido->id}}">
                                                <td>{{$pedido->conekta_order_id}}</td>
                                                <td>{{$pedido->created_at}}</td>
                                                <td>{{'$'.$pedido->costo_total/100}}</td>
                                                <td>
                                                    {!! ($pedido->tipo_envio == 1 ? "<span class='label label-warning'>Con envío</span>" : 
                                                    ($pedido->tipo_envio == 2 ? "<span class='label label-success'>Gratuito</span>" : 
                                                    ($pedido->tipo_envio == 3 ? "<span class='label label-info'>Metropolitano</span>" : 
                                                    ($pedido->tipo_envio == 4 ? "<span class='label'>Recoger en tienda</span>" :
                                                     "<span class='label label-important'>¡Desconocido!</span>")))) !!}
                                                </td>
                                                <td>{!! $pedido->num_seguimiento ? "<span class='label label-success'>$pedido->num_seguimiento</span>" : "<span class='label label-important'>Sin asignar</span>" !!}</td>  
                                                <td>{{$pedido->tipo_orden}}</td>
                                                <td>
                                                    {!! $pedido->tipo_envio == 1 || $pedido->tipo_envio == 2 ? "<button type='button' class='btn btn-success asignar_guia'><i class='fa fa-truck' aria-hidden='true'></i> Guía</button>" : ""!!}
                                                    <button type="button" class="btn btn-info detalle_producto"><i class="fa fa-info-circle" aria-hidden="true"></i> Detalles</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <td colspan="6">No hay pedidos disponibles</td>
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
<script src="{{ asset('js/pedidosAjax.js') }}"></script>
<script type="text/javascript">
$('body').delegate('.detalle_producto','click', function() {
    var orden_id = $(this).parent().siblings("td:nth-child(1)").text();
    var token = $("#token").val();

    $('div#campos_detalles').addClass('hide');
    $('div#load_bar').removeClass('hide');
    $('#detalles_pedido').modal();
    obtenerInfoPedido(orden_id,token);
});

$('body').delegate('.asignar_guia','click', function() {
    var orden_id_conekta = $(this).parent().siblings("td:nth-child(1)").text();
    var td_guia = $(this).parent().siblings("td:nth-child(5)");
    var token = $('#token').val();
    swal({
        title: "Asignar guía",
        text: "Ingrese el nuevo número de guía para este pedido (se enviará un correo electrónico al usuario mostrando el número de guía asignado al pedido).",
        type: "input",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
        animation: "slide-from-top",
        inputPlaceholder: "Ej. 2345678901"
    },
    function(inputValue) {
        if (inputValue === false) return false;

        if (inputValue === "") {
            swal.showInputError("No se permite dejar este campo vacío");
            return false
        } else {
            asignarNumeroGuia(inputValue, orden_id_conekta, td_guia, token);
            return true;
        }
    });
});

</script>
@endsection