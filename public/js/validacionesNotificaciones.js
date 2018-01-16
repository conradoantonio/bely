/*Código para validar el formulario de datos del usuario*/
var inputs = [];
var msgError = '';
var regExprTitulo = /^[\* a-z A-Z ñ # , : ; ¿ ? ! ¡ ' " _ @ ( ) áéíóúäëïöüâêîôûàèìòùç\d_\s \-.]{2,30}$/i;
var regExprContenido = /^[\* a-z A-Z ñ # , : ; ¿ ? ! ¡ ' " _ @ ( ) áéíóúäëïöüâêîôûàèìòùç\d_\s \-.]{2,140}$/i;
var regExprNum = /^[\d .]{1,}$/i;
$("#enviar_notificacion_general").on('click', function() {
    inputs = [];
    msgError = '';

    validarInput($('input#titulo_general'), regExprTitulo) == false ? inputs.push('Título') : ''
    validarInput($('textarea#mensaje_general'), regExprContenido) == false ? inputs.push('Mensaje') : ''

    if (inputs.length == 0) {
        $(this).children('i').show();
        $(this).attr('disabled', true);
        enviarNotificacion($('form#form_notificaciones_generales'), $(this));
    } else {
        swal("Corrija los siguientes campos para continuar: ", msgError);
        return false;
    }
});

$( "input#titulo_general" ).blur(function() {
    validarInput($(this), regExprTitulo);
});
$( "textarea#mensaje_general" ).blur(function() {
    validarInput($(this), regExprContenido);
});


$("#enviar_notificacion_individual").on('click', function() {
    inputs = [];
    msgError = '';

    validarInput($('input#titulo_individual'), regExprTitulo) == false ? inputs.push('Título') : ''
    validarInput($('textarea#mensaje_individual'), regExprContenido) == false ? inputs.push('Mensaje') : ''
    validarSelect($('select#usuarios_id')) == false ? inputs.push('Usuarios') : ''

    if (inputs.length == 0) {
        $(this).children('i').show();
        $(this).attr('disabled', true);
        enviarNotificacion($('form#form_notificaciones_individuales'), $(this));
    } else {
        swal("Corrija los siguientes campos para continuar: ", msgError);
        return false;
    }
});

$( "input#titulo_individual" ).blur(function() {
    validarInput($(this), regExprTitulo);
});
$( "textarea#mensaje_individual" ).blur(function() {
    validarInput($(this), regExprContenido);
});
$( "select#usuarios_id" ).change(function() {
    validarSelect($(this));
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

function validarSelect (select) {
    if ($(select).val() == '0' || $(select).val() == '' || $(select).val() == null) {
        $(select).parent().children('div.select2').children('ul.select2-choices').addClass("select-error");
        msgError = msgError + $(select).parent().children('label').text() + '\n';
        return false;
    } else {
        $(select).parent().children('div.select2').children('ul.select2-choices').removeClass("select-error");
        return true;
    }
}

/*Fin del código para validar el archivo que importa datos desde excel*/