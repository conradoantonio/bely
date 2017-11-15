/*Código para validar el formulario de datos del usuario*/
var inputs = [];
mb = 0;
fileExtension = ['jpg', 'jpeg', 'png'];
var msgError = '';
var regExprTexto = /^[a-z ñ # , : ; ¿ ? ! ¡ ' " _ @ ( ) áéíóúäëïöüâêîôûàèìòùç\d_\s \-.]{2,}$/i;
var btn_enviar_pregunta = $("#guardar_pregunta");
btn_enviar_pregunta.on('click', function() {
    inputs = [];
    msgError = '';

    validarInput($('input#pregunta'), regExprTexto) == false ? inputs.push('Pregunta') : ''
    validarInput($('textarea#respuesta'), regExprTexto) == false ? inputs.push('Descripción') : ''
    validarArchivo($('input#imagen_pregunta'), $('div#imagen_pregunta')) == false ? inputs.push('Imagen Pregunta') : ''

    if (inputs.length == 0) {
        $('#guardar_pregunta').hide();
        $('#guardar_pregunta').submit();
    } else {
        $('#guardar_pregunta').show();
        swal("Corrija los siguientes campos para continuar: ", msgError);
        return false;
    }
});

$( "input#pregunta" ).blur(function() {
    validarInput($(this), regExprTexto);
});

$( "textarea#respuesta" ).blur(function() {
    validarInput($(this), regExprTexto);
});

function validarInput (campo,regExpr) {
    if (!$(campo).val().match(regExpr)) {
        $(campo).parent().addClass("has-error");
        msgError = msgError + $(campo).parent().children('label').text() + '\n';
        return false;
    } else {
        $(campo).parent().removeClass("has-error");
        return true;
    }
}

$('input#imagen_pregunta').bind('change', function() {
    if ($(this).val() != '') {

        kilobyte = (this.files[0].size / 1024);
        mb = kilobyte / 1024;

        archivo = $(this).val();
        extension = archivo.split('.').pop().toLowerCase();

        if ($.inArray(extension, fileExtension) == -1 || mb >= 5) {
            swal({
                title: "Archivo no válido",
                text: "Debe seleccionar una imágen con formato jpg, jpeg o png, y debe pesar menos de 5MB",
                type: "error",
                closeOnConfirm: false
            });
        }
    }
});

function validarArchivo(campo, div) {
    archivo = $(campo).val();
    extension = archivo.split('.').pop().toLowerCase();

    if ($(div).is(":visible") && ($(campo).val() == '' || $(campo).val() == null)) {
        return true;
    } else if ($.inArray(extension, fileExtension) == -1 || mb >= 5) {
        $(campo).parent().addClass("has-error");
        msgError = msgError + $(campo).parent().children('label').text() + '\n';
        return false;
    } else {
        $(campo).parent().removeClass("has-error");
        return true;
    }
}
/*Fin de código para validar el formulario de datos del usuario*/
