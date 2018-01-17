/*Código para validar el formulario de datos del usuario*/
var inputs = [];
mb = 0;
fileExtension = ['jpg', 'jpeg', 'png'];
archivo = '';
var msgError = '';
var regExprNombre = /^[a-z ñ : , áéíóúäëïöüâêîôûàèìòùç\d_\s .]{2,50}$/i;
var regExprTel = /^[ \- + ( ) \d \s]{3,18}$/i;
var regExprNumReq = /^[ # \d \s]{3,10}$/i;
var regExprNum = /^[ # \d \s]{0,10}$/i;
var regExprTexto = /^[a-z ñ # , : ; ( ) áéíóúäëïöüâêîôûàèìòùç\d_\s .]{10,}$/i;
var btn_enviar_info = $("#guardar_info");
btn_enviar_info.on('click', function() {
    inputs = [];
    msgError = '';

    validarInput($('input#telefono'), regExprTel) == false ? inputs.push('Teléfono') : ''
    validarInput($('input#codigo_postal'), regExprNumReq) == false ? inputs.push('Código postal') : ''
    validarInput($('input#numeroExt'), regExprNum) == false ? inputs.push('Numero exterior') : ''
    validarInput($('input#numeroInt'), regExprNum) == false ? inputs.push('Numero interior') : ''
    validarInput($('textarea#direccion'), regExprTexto) == false ? inputs.push('Dirección') : ''
    validarArchivos($('input#logo')) == false ? inputs.push('Logo') : ''
    

    if (inputs.length == 0) {
        $('#guardar_info').hide();
        $("#guardar_info").submit();
    } else {
        $('#guardar_info').show();
        swal("Corrija los siguientes campos para continuar: ", msgError);
        return false;
    }
});

$( "input#telefono" ).blur(function() {
    validarInput($(this), regExprTel);
});
$( "input#codigo_postal" ).blur(function() {
    validarInput($(this), regExprNumReq);
});
$( "input#numeroExt" ).blur(function() {
    validarInput($(this), regExprNum);
});
$( "input#numeroInt" ).blur(function() {
    validarInput($(this), regExprNum);
});
$( "textarea#direccion" ).blur(function() {
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
    /*if (!$(campo).val() != '') {
        $(campo).parent().addClass("has-error");
        msgError = msgError + $(campo).parent().children('label').text() + '\n';
        return false;
    } else {
        $(campo).parent().removeClass("has-error");
        return true;
    }*/
}

/*function validarSelect (select) {
    if ($(select).val() == '0' || $(select).val() == '' || $(select).val() == null) {
        $(select).parent().addClass("has-error");
        msgError = msgError + $(select).parent().children('label').text() + '\n';
        return false;
    } else {
        $(select).parent().removeClass("has-error");
        return true;
    }
}*/

$('input#logo').bind('change', function() {
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

function validarArchivos(campo) {
    T = $(campo).val();
    extension = archivo.split('.').pop().toLowerCase();

    if($('form#form_info_empresa input#id').val() != '' && ($(campo).val() == '' || $(campo).val() == null)) {
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