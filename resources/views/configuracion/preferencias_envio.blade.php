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
<div class="text-center" style="margin-left: 10px;">

    <h2>Preferencias de envío</h2>

    <input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}" base-url="<?php echo url();?>">
    <div class="col-md-12">
        <ul class="nav nav-tabs" id="tab-01">
            <li class="active"><a href="#tab1hellowWorld">Activar o desactivar envío gratuito</a></li>
            <li><a href="#tab1FollowUs">Monto mínimo para envío gratuito</a></li>
            <li><a href="#tab1Inspire">Tarifa de envío</a></li>
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
        </div>
    </div>
</div>
<script src="{{ asset('js/ios7-switch.js') }}"></script>
<script src="{{ asset('js/form_elements.js') }}"></script>
<script src="{{ asset('js/tabs_accordian.js') }}"></script>
<script src="{{ asset('js/preferenciasEnvioAjax.js') }}"></script>
<script type="text/javascript">

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
</script>
@endsection