base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista
function obtenerInfoPedido(orden_id,token) {
    url = base_url.concat('/pedidos/obtener_info_pedido');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "orden_id":orden_id,
            "_token":token
        },
        success: function(data) {
            console.info(data);

            $("table#detalle_pedido tbody").children().remove();
            var items = data.detalles;
            /*Datos generales*/
            $('p#order_id').text(data.conekta_order_id);
            $('p#payment_status').text(data.status);
            $('p#currency').text('MXN');
            $('p#total').text('$' + data.costo_total/100);
            $('p#num_referencia').text(data.num_referencia ? data.num_referencia : 'No aplica');

            
            /*Datos de contacto*/
            $('p#customer_id').text(data.customer_id_conekta);
            $('p#name').text(data.nombre_cliente);
            $('p#phone').text(data.telefono);
            $('p#email').text(data.correo_cliente);
            
            /*Datos de envío*/
            $('p#shpping_c_receiver').text(data.recibidor);
            $('p#shpping_c_phone').text(data.telefono);
            $('p#num_guia').children().remove();
            $('p#num_guia').append(data.num_seguimiento ? '<span class="label label-success">'+data.num_seguimiento+'</span>' : '<span class="label label-important">Sin asignar </span>');
            $('p#costo_envio').text('$'+(data.costo_envio/100));
            $('p#shpping_c_city').text(data.ciudad);
            $('p#shpping_c_state').text(data.estado);
            $('p#shpping_c_country').text(data.pais);
            $('p#shpping_c_postal_code').text(data.codigo_postal);
            $('p#shipping_c_street1').text(data.calle);
            
            /*Detalles de pedido (Productos)*/
            for (var key in items) {
                if (items.hasOwnProperty(key)) {
                    $("table#detalle_pedido tbody").append(
                        '<tr class="" id="">'+
                            '<td class="table-bordered">'+items[key].codigo+'</td>'+
                            '<td class="table-bordered">'+items[key].nombre_producto+'</td>'+
                            '<td class="table-bordered">'+(items[key].medida ? items[key].medida : 'No aplica')+'</td>'+
                            '<td class="table-bordered">$'+(items[key].precio / 100)+'</td>'+
                            '<td class="table-bordered">'+(items[key].cantidad)+'</td>'+
                            '<td class="table-bordered">$'+((items[key].precio * items[key].cantidad) / 100)+'</td>'+
                        '</tr>'
                    );
                }
            }
            $("table#detalle_pedido tbody").append(
                '<tr class="" id="">'+
                    '<td></td>'+
                    '<td></td>'+
                    '<td></td>'+
                    '<td></td>'+
                    '<td class="bold">Costo de envío</td>'+
                    '<td>$'+(data.costo_envio/100)+'</td>'+
                '</tr>'+
                '<tr class="" id="">'+
                    '<td></td>'+
                    '<td></td>'+
                    '<td></td>'+
                    '<td></td>'+
                    '<td class="bold">Costo total</td>'+
                    '<td>$'+(data.costo_total/100)+'</td>'+
                '</tr>'
            );

            $('div#campos_detalles').removeClass('hide');
            $('div#load_bar').addClass('hide');
        },
        error: function(xhr, status, error) {
            $('#detalles_pedido').modal('hide');
            swal({
                title: "<small>¡Error!</small>",
                text: "Se encontró un problema obteniendo los detalles de este pedido, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}

function asignarNumeroGuia(numero_guia,orden_id,td_guia,token) {
    url = base_url.concat('/pedidos/asignar_guia');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "numero_guia":numero_guia,
            "orden_id":orden_id,
            "_token":token
        },
        success: function() {
            swal({
                title: "Bien",
                text: "Número de guía "+ numero_guia +" asignado correctamente",
                type: "success",
                showLoaderOnConfirm: false,
                timer: 2000
            });
            td_guia.children().remove();
            td_guia.append('<span class="label label-success">'+numero_guia+'</span>');
        },
        error: function(xhr, status, error) {
            swal({
                title: "<small>¡Error!</small>",
                text: "Ocurrió un problema asignando el número de guía a este pedido, por favor trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}
