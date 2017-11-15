base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista
function eliminarBloquearUsuario(id,status,correo,token) {
    url = base_url.concat('/usuario/cambiarStatus');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "id":id,
            "status":status,
            "correo":correo,
            "_token":token
        },
        success: function() {
            status_usuario = status == '0' ? 'bloqueado' : status == '1' ? 'reactivado' : status == '2' ? 'aceptado' : status == '3' ? 'borrado' : 'rechazado'
            swal({
                title: "Usuario " + status_usuario + " correctamente, esta página se recargará automáticamente ahora",
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
                text: "Se encontró un problema cambiando el status del usuario, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}

function guardarUsuarioApp(id,nombre,correo,estado_id,estado,edad,genero_id,genero,token) {
    url = base_url.concat('/usuario/guardarUsuario');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "id":id,
            "nombre":nombre,
            "correo":correo,
            "estado_id":estado_id,
            "edad":edad,
            "genero_id":genero_id,
            "_token":token
        },
        success: function() {
            $('#guardar-datos-usuario').show();
            if (id != "") {
                var tr = $('tr[idUsuario='+id+']');
                $(tr).children('td').siblings("td:nth-child(2)").text(nombre),
                $(tr).children('td').siblings("td:nth-child(3)").text(correo),
                $(tr).children('td').siblings("td:nth-child(5)").text(estado_id),
                $(tr).children('td').siblings("td:nth-child(6)").text(estado),
                $(tr).children('td').siblings("td:nth-child(7)").text(edad),
                $(tr).children('td').siblings("td:nth-child(8)").text(genero_id),
                $(tr).children('td').siblings("td:nth-child(9)").text(genero);
                swal({
                    title: "¡Usuario actualizado correctamente!",
                    type: "success",
                    allowOutsideClick: true,
                    showConfirmButton: true,
                });
                save = true;
            } else {
                swal({ 
                    title: "Usuario agregado correctamente, esta página se recargará automáticamente ahora.",
                    type: "success",
                    showConfirmButton: false,
                }, 
                    function() {
                        location.reload();
                    });
                setTimeout("location.reload()",1200);
            }
        },
        error: function(xhr, status, error) {
            $('#guardar-datos-usuario').show();
            swal({
                title: "<small>Error!</small>",
                text: "Ha ocurrido un error mientras se actualizaba el usuario, por favor, trate nuevamente o recargue la página.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
            save = true;
        }
    });
}