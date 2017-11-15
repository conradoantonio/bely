base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista
function eliminarQuehacer(id,token) {
    url = base_url.concat('/admin/pueblos_magicos/quehacer/eliminar');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "id":id,
            "_token":token
        },
        success: function() {
            swal({
                title: "Registro eliminado correctamente, esta página se recargará automáticamente ahora.",
                type: "success",
                showConfirmButton: false,
            }, 
                function() {
                    location.reload();
                });
                setTimeout("location.reload()",1200);
        },
        error: function(xhr, status, error) {
            swal({
                title: "<small>Error!</small>",
                text: "Se encontró un problema eliminando este registro, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}

function guardarDetalleQuehacer(id_empresa,id_quehacer,descripcion,token) {
    url = base_url.concat('/admin/pueblos_magicos/quehacer/guardarDetalle');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "id_empresa":id_empresa,
            "id_quehacer":id_quehacer,
            "descripcion":descripcion,
            "_token":token
        },
        success: function(data) {
            $('#formulario-detalle-quehacer select#empresa').val('0');
            $('#descripcionDetalleEmpresa').val('');
            if (data.length > 0) {
                $("#detallesQuehacerContenido").children().remove();
                data.forEach(function(res) {
                    if (res != "") {
                        $("#detallesQuehacerContenido").append(
                            "<tr class='' id="+res.idDetalle+">"+
                                "<td class='hide'>"+res.idDetalle+"</td>"+
                                "<td>"+res.nombreEmpresa+"</td>"+
                                "<td class='text'><span>"+res.descripcion+"</span></td>"+
                                "<td>"+
                                    "<button type='button' class='btn btn-danger borrar-detalle-quehacer' status-pueblo='0'>Borrar</button>"+
                                "</td>"+
                            "</tr>"
                        );
                    }
                })
            }
            swal({
                title: "Registro guardado correctamente.",
                type: "success",
                showConfirmButton: true,
                timer: 1200,
            });
            $('#guardar-detalle-quehacer').show();
        },
        error: function(xhr, status, error) {
            swal({
                title: "<small>Error!</small>",
                text: "No se ha podido guardar el nuevo registro, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
            $('#guardar-detalle-quehacer').show();
        }
    });
}

function borrarQuehacerDetalles(idDetalle,token) {
    url = base_url.concat('/admin/pueblos_magicos/quehacer/borrarDetalle');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "idDetalle":idDetalle,
            "_token":token
        },
        success: function() {
            swal({
                title: "Registro eliminado correctamente.",
                type: "success",
                showConfirmButton: true,
                timer: 1200,
            });
            $('#guardar-detalle-quehacer').show();
            $('tr#'+idDetalle).remove();
            if ($('table#detallesQuehacer >tbody#detallesQuehacerContenido >tr').length == 0){
                $("#detallesQuehacerContenido").append(
                    '<td colspan="4">No hay registros disponibles</td>'
                );
            }
        },
        error: function(xhr, status, error) {
        $('#guardar-detalle-quehacer').show();
            swal({
                title: "<small>Error!</small>",
                text: "Se encontró un problema eliminando este registro, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}