base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista
function obtenerDetalleCotizacion(cotizacion_id,token) {
    url = base_url.concat('/cotizaciones/ver_cotizacion_detalles');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "cotizacion_id":cotizacion_id,
            "_token":token
        },
        success: function(data) {
            console.info(data);
            $("table#detalle_cotizacion tbody").children().remove();
            var items = data.cotizaciones_detalles;
            
            /*Datos generales*/
            $('p#apellido').text(data.apellido);
            $('p#nombre').text(data.nombre);
            $('p#email').text(data.correo);
            $('p#telefono').text(data.telefono);
            
            /*Datos de envío*/
            $('p#ciudad').text(data.direccion_user[0].ciudad);
            $('p#codigo_postal').text(data.direccion_user[0].codigo_postal);
            $('p#pais').text(data.direccion_user[0].pais);
            $('p#estado').text(data.direccion_user[0].estado);
            var direccion = data.direccion_user[0].calle;
            direccion = direccion.concat(data.direccion_user[0].num_ext ? ' No. ext: ' + data.direccion_user[0].num_ext : ' ')
            direccion = direccion.concat(data.direccion_user[0].num_int ? ' No. int: ' + data.direccion_user[0].num_int : ' ')
            $('p#calle').text(direccion);

            /*Detalles de cotizacion (Productos)*/
            for (var key in items) {
                if (items.hasOwnProperty(key)) {
                    $("table#detalle_cotizacion tbody").append(
                        '<tr class="" id="">'+
                            '<td class="table-bordered">'+items[key].codigo+'</td>'+
                            '<td class="table-bordered">'+items[key].nombre_producto+'</td>'+
                            '<td class="table-bordered">$'+(items[key].precio ? items[key].precio : 'No disponible')+'</td>'+
                            '<td class="table-bordered">'+(items[key].cantidad)+'</td>'+
                        '</tr>'
                    );
                }
            }

            $('div#campos_detalles').removeClass('hide');
            $('div#load_bar').addClass('hide');
        },
        error: function(xhr, status, error) {
            $('#detalles_cotizacion').modal('hide');
            swal({
                title: "<small>¡Error!</small>",
                text: "Se encontró un problema obteniendo los detalles de esta cotización, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}

function finalizarCotizacion(cotizacion_id,td_status,token) {
    url = base_url.concat('/cotizaciones/finalizar_cotizacion');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "cotizacion_id":cotizacion_id,
            "_token":token
        },
        success: function() {
            swal({
                title: "¡Hecho!",
                text: "La cotización con el id " + cotizacion_id + " ha sido marcada como atendida.",
                type: "success",
                showLoaderOnConfirm: false,
                timer: 3000
            },function() {
                    location.reload();
                });
            setTimeout("location.reload()",1200);

            td_status.children().remove();
            td_status.append('<span class="label label-success">Atendido</span>');
        },
        error: function(xhr, status, error) {
            swal({
                title: "<small>¡Error!</small>",
                text: "Ocurrió un problema finalizando esta cotización, por favor trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}
