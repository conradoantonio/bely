base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista
console.info(base_url);
function eliminarPregunta(id,token) {
    url = base_url.concat('/preguntas_frecuentes/eliminar_pregunta');
    $.ajax({
        method: "POST",
        url: url,
        data:{
            "id":id,
            "_token":token
        },
        success: function() {
            swal({
                title: "Pregunta eliminada correctamente, esta página se recargará automáticamente ahora.",
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
                text: "Se encontró un problema eliminando la pregunta, por favor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}