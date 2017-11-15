base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista
function eliminarProducto(id,token) {
    url = base_url.concat('/productos/eliminar');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "id":id,
            "_token":token
        },
        success: function() {
            swal({
                title: "Producto eliminado correctamente, esta página se recargará automáticamente ahora.",
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
                title: "<small>¡Error!</small>",
                text: "Se encontró un problema eliminando este producto, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}

function cargarSubcategorías(categoria_id,token) {
    url = base_url.concat('/productos/cargar_subcategorias');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "categoria_id":categoria_id,
            "_token":token
        },
        success: function(data) {
            $('select#subcategoria_id option').remove();
            $('select#subcategoria_id').append('<option value="0" selected="selected">Elija una opción</option>');
            
            data.forEach(function (option) {
                $('select#subcategoria_id').append('<option value="'+option.id+'">'+option.subcategoria+'</option>');
            });
        },
        error: function(xhr, status, error) {
            console.warn('Error cargando las subcategorías');
        }
    });
}
