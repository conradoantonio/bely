@extends('admin.main')

@section('content')
<style>
th {
    text-align: center!important;
}
textarea {
    resize: none;
}
.table td.text {
    max-width: 177px;
}
.table td.text span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    max-width: 100%;
}
</style>
<link rel="stylesheet" href="{{ asset('plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css')}}"  type="text/css" media="screen"/>
<div class="text-center" style="margin-left: 10px;">

    <h2>Preferencias de envío</h2>

    <input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}" base-url="<?php echo url();?>">
    <div class="col-md-12">
        <ul class="nav nav-tabs" id="tab-01">
            <li class="active"><a href="#tab1hellowWorld">Activar o desactivar envío gratuito</a></li>
            <li><a href="#tab1FollowUs">Monto mínimo para envío gratuito</a></li>
            <li><a href="#tab1Inspire">Tarifa de envío</a></li>
            <li><a href="#tabPorcentajeDescuento">Porcentajes de descuento</a></li>
            <li><a href="#tabFechaLimite">Fecha límite de precios</a></li>
        </ul>
        <div class="tools"> <a href="javascript:;" class="collapse"></a> <a href="#grid-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1hellowWorld">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Actualmente esta característica se encuentra: <span id="envio" class="semi-bold">Desactivada</span></h3>
                        <p>Pulse el switch de abajo para cambiar el estado de esta característica.</p>
                        <br>
                        <div class="row-fluid">
                            <div class="slide-success">
                                <input type="checkbox" id="activar_envio" name="switch" class="iosblue" {{$preferencias->envio_gratuito == 1 ? 'checked' : ''}}/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab1FollowUs">
                <div class="row">
                    <div class="col-md-12">
                        <h3>El monto actual mínimo para poder realizar envíos gratuitos es: <span id="monto" class="semi-bold"> ${{$preferencias->monto_minimo_envio}}</span></h3>
                        <p>Pulse el botón de abajo para cambiar el monto, solo son admitidos números enteros y decimales (2 decimales como máximo).</p>
                        <br>
                        <p class="">
                            <button id="monto_minimo_envio" type="button" class="btn btn-success btn-cons">Modificar</button>
                        </p>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab1Inspire">
                <div class="row">
                    <div class="col-md-12">
                        <h3>La tarifa de envío actual es: <span id="tarifa" class="semi-bold"> ${{$preferencias->tarifa_envio}}</span></h3>
                        <p>Pulse el botón de abajo para cambiar la tarifa, solo son admitidos números enteros y decimales (2 decimales como máximo).</p>
                        <br>
                        <p class="">
                            <button id="tarifa_envio" type="button" class="btn btn-success btn-cons">Modificar</button>
                        </p>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tabPorcentajeDescuento">
                <p>Marque la casilla de abajo para activar el porcentaje de descuento, y procure poner un número entre el 1 y 100.</p>
                <div class="row">
                    <div class="col-sm-6 col-xs-12" style="padding-bottom: 20px;">
                        <label for="activar_descuento">Activar</label>
                        <div class="row-fluid">
                            <div class="checkbox check-primary">
                                <input id="activar_descuento" name="activar_descuento" {{$preferencias->descuento_activo == 1 ? 'checked' : ''}} type="checkbox">
                                <label for="activar_descuento" style="padding-left:0px;"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label for="porcentaje_descuento">Porcentaje de descuento</label>
                            <input type="text" class="form-control" id="porcentaje_descuento" value="{{$preferencias->descuento_porcentaje}}" maxlength="2" name="porcentaje_descuento" placeholder="Ej: 40">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="guardar_info_descuento">
                    <i class="fa fa-spinner fa-spin" style="display: none"></i>
                    Guardar
                </button>
            </div>
            <div class="tab-pane" id="tabFechaLimite">
                <p>Marque la casilla de abajo para mostrar el timer del precio del producto en la aplicación.</p>
                <div class="row">
                    <div class="col-sm-4 col-xs-12" style="padding-bottom: 20px;">
                        <label for="mostrar_timer">Mostrar timer en la aplicación</label>
                        <div class="row-fluid">
                            <div class="checkbox check-primary">
                                <input id="mostrar_timer" name="mostrar_timer" {{$preferencias->mostrar_timer == 1 ? 'checked' : ''}} type="checkbox">
                                <label for="mostrar_timer" style="padding-left:0px;"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <div class="form-group">
                            <label for="">Fecha</label>
                            <input type="text" name="dia_limite" class='form-control' value="{{$preferencias->dia_limite}}" id='dia_limite'>
                        </div>
                    </div>
                    <div class="col-sm-4 col-xs-12 clockpicker">
                        <div class="form-group">
                            <label for="hora_limite">Hora</label>
                            <input type="text" class="form-control timepicker" value="{{$preferencias->hora_limite}}" id="hora_limite" name="hora" placeholder="Ej. 08:30">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="guardar_info_fecha_limite">
                    <i class="fa fa-spinner fa-spin" style="display: none"></i>
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/ios7-switch.js') }}"></script>
<script src="{{ asset('js/form_elements.js') }}"></script>
<script src="{{ asset('js/tabs_accordian.js') }}"></script>
<script src="{{ asset('js/preferenciasEnvioAjax.js') }}"></script>
<script type="text/javascript">

    $(function(){
        $('.clockpicker ').clockpicker({
            autoclose: true
        });
        $( "#dia_limite, #fecha_individual" ).datepicker({
            autoclose: true,
            language: 'es',
            todayHighlight: true,
            format: "yyyy-mm-dd",
        });
        $('#dia_limite').datepicker('setStartDate', "{{date('Y-m-d')}}");
    })

    $('body').delegate('div.slide-success','click', function() {
        envio = $('#activar_envio').prop('checked') == true ? '1' : '0';
        empresa = $('#token').attr('empresa-id');
        token = $('#token').val();
        activarDesactivarEnvio(envio, empresa, token);
    });

    $('body').delegate('button#monto_minimo_envio','click', function() {
        swal({
            title: "Monto mínimo de envío",
            text: "Ingrese un valor entero o decimal (NO ingrese el signo de peso).",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            animation: "slide-from-top",
            inputPlaceholder: "Ej. 1500.00"
        },
        function(inputValue) {
            regExpr = /^\d+(\.\d{1,2})?$/i;
            if (inputValue === false) return false;

            if (inputValue === "") {
                swal.showInputError("Ingrese una cantidad válida.");
                return false
            }

            if (!inputValue.match(regExpr)) {
                swal.showInputError("Ingrese una cantidad válida.");
                return false;
            } else {
                empresa = $('#token').attr('empresa-id');
                token = $('#token').val();
                cambiarMontoMinimoEnvio(inputValue, empresa, token);
                return true;
            }
        });
    });

    $('body').delegate('button#tarifa_envio','click', function() {
        swal({
            title: "Nueva tarifa de envío",
            text: "Ingrese un valor entero o decimal (NO ingrese el signo de peso).",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            animation: "slide-from-top",
            inputPlaceholder: "Ej. 100.00"
        },
        function(inputValue) {
            regExpr = /^\d+(\.\d{1,2})?$/i;
            if (inputValue === false) return false;

            if (inputValue === "") {
                swal.showInputError("Ingrese una cantidad válida.");
                return false
            }

            if (!inputValue.match(regExpr)) {
                swal.showInputError("Ingrese una cantidad válida.");
                return false;
            } else {
                empresa = $('#token').attr('empresa-id');
                token = $('#token').val();
                cambiarTarifaEnvio(inputValue, empresa, token);
                return true;
            }
        });
    });

    $('body').delegate('button#guardar_info_descuento','click', function() {
        porcentaje = $('#porcentaje_descuento').val();

        if (!isNaN(porcentaje) && porcentaje != "") {//Es número
            if (porcentaje < 100 && porcentaje > 0) {
                activo = $('#activar_descuento').prop('checked') == true ? 1 : 0;
                empresa = $('#token').attr('empresa-id');
                token = $('#token').val();
                console.log('debio mandarse');
                cambiarPorcentajeDescuento(activo,empresa,porcentaje,token)        
            } else {
                swal("El porcentaje debe ser mayor a 0 y menor a 100.", 'Porfavor ingrese otra cantidad', 'error');
            }
        } else {
            swal("Favor de ingresar sólo números.", 'Sólo ingrese un valor menor a 100 y mayor a 0', 'error');
        }
    });

    $('body').delegate('button#guardar_info_fecha_limite','click', function() {
        mostrar_timer = $('#mostrar_timer').prop('checked') == true ? 1 : 0;
        dia = $('#dia_limite').val();
        hora = $('#hora_limite').val();

        if (dia && hora) {
            empresa = $('#token').attr('empresa-id');
            token = $('#token').val();
            configurarHoraLimite(dia,hora,mostrar_timer,empresa,token);
        } else {
            swal("Favor de completar los campos de hora y fecha para continuar", '', 'error');
        }
    });

</script>
@endsection