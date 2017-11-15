base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista
function activarDesactivarEnvio(envio,empresa,token) {
    url = base_url.concat('/configuracion/preferencias/activar_envio_gratuito');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "envio":envio,
            "empresa_id":empresa,
            "_token":token
        },
        success: function() {
            $('span#envio').text(envio == 1 ? 'Activada' : 'Desactivada');
        },
        error: function(xhr, status, error) {
            cambio = envio == 1 ? 'Activando' : 'Desactivando';
            swal({
                title: "<small>¡Error!</small>",
                text: "Ocurrió un problema " + cambio + " la opción de envio, por favor trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}

function cambiarMontoMinimoEnvio(monto,empresa,token) {
    url = base_url.concat('/configuracion/preferencias/cambiar_monto_minimo');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "monto":monto,
            "empresa_id":empresa,
            "_token":token
        },
        success: function() {
            swal({
                title: "Bien",
                text: "Éxito cambiando el monto mínimo de envío a: $" + monto,
                type: "success",
                showLoaderOnConfirm: false,
                timer: 2000
            });
            $('span#monto').text('$'+monto);
        },
        error: function(xhr, status, error) {
            swal({
                title: "<small>¡Error!</small>",
                text: "Ocurrió un problema cambiando el monto mínimo de envío, por favor trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}

function cambiarTarifaEnvio(tarifa,empresa,token) {
    url = base_url.concat('/configuracion/preferencias/cambiar_tarifa_envio');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "tarifa":tarifa,
            "empresa_id":empresa,
            "_token":token
        },
        success: function() {
            swal({
                title: "Bien",
                text: "Éxito cambiando la tarifa de envío a: $" + tarifa,
                type: "success",
                showLoaderOnConfirm: false,
                timer: 2000
            });
            $('span#tarifa').text('$'+tarifa);
        },
        error: function(xhr, status, error) {
            swal({
                title: "<small>¡Error!</small>",
                text: "Ocurrió un problema cambiando la tarifa de envío, por favor trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}