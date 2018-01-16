base_url = $('#token').attr('base-url');//Extrae la base url del input token de la vista

/*Carga una foto a una categoría*/
function enviarNotificacion(form,btn) {
    var formData = new FormData($(form)[0]);
    $.ajax({
        method: "POST",
        url: $(form).attr('action'),
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: function(data) {
            obj = JSON.parse(data);
            console.log(obj);
            if (obj.hasOwnProperty('errors')) {
                swal({
                    title: "Notificación no enviada, hay datos inválidos.",
                    text: "Porfavor, revise que la información introducida en el formulario sea correcta.<br><span style='color:#F8BB86'>\nMensaje de error: " + obj.errors +"</span>",
                    type: "error",
                    html: true,
                    showConfirmButton: true,
                });
            } else {
                swal({
                    title: "Notificacion enviada.",
                    type: "success",
                    showConfirmButton: true,
                });
            }
            
            btn.children('i').hide();
            btn.attr('disabled', false);
        },
        error: function(xhr, status, error) {
            btn.children('i').hide();
            btn.attr('disabled', false);
            swal({
                title: "<small>¡Error!</small>",
                type: "error",
                text: "Se encontró un problema de parte de nuestro servidor al momento de enviar la notificación, porfavor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}
