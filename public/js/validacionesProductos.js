/*Código para validar el formulario de datos del usuario*/
var inputs = [];
mb = 0;
fileExtension = ['jpg', 'jpeg', 'png'];
var msgError = '';
var regExprTexto = /^[a-z ñ # , : ; ¿ ? ! ¡ ' " _ @ ( ) áéíóúäëïöüâêîôûàèìòùç\d_\s \-.]{2,}$/i;
var regExprNum = /^[\d .]{1,}$/i;
var btn_enviar_producto = $("#guardar_producto");
btn_enviar_producto.on('click', function() {
    inputs = [];
    msgError = '';

    validarInput($('input#codigo'), regExprTexto) == false ? inputs.push('Código') : ''
    validarInput($('input#sku'), regExprTexto) == false ? inputs.push('SKU') : ''
    validarInput($('input#nombre'), regExprTexto) == false ? inputs.push('Nombre') : ''
    validarInput($('input#precio'), regExprNum) == false ? inputs.push('Precio') : ''
    validarInput($('input#stock'), regExprNum) == false ? inputs.push('Stock') : ''
    validarSelect($('select#categoria_id')) == false ? inputs.push('Categoría') : ''
    validarInput($('textarea#descripcion'), regExprTexto) == false ? inputs.push('Descripción') : ''
    validarArchivo($('input#foto_producto'), $('div#foto_producto')) == false ? inputs.push('Imagen') : ''

    if (inputs.length == 0) {
        $('#guardar_producto').hide();
        $('#guardar_producto').submit();
    } else {
        $('#guardar_producto').show();
        swal("Corrija los siguientes campos para continuar: ", msgError);
        return false;
    }
});

$( "input#codigo" ).blur(function() {
    validarInput($(this), regExprTexto);
});
$( "input#sku" ).blur(function() {
    validarInput($(this), regExprTexto);
});
$( "input#nombre" ).blur(function() {
    validarInput($(this), regExprTexto);
});
$( "input#precio" ).blur(function() {
    validarInput($(this), regExprNum);
});
$( "input#stock" ).blur(function() {
    validarInput($(this), regExprNum);
});
$( "select#categoria_id" ).change(function() {
    validarSelect($(this));
});
$( "textarea#descripcion" ).blur(function() {
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

function validarSelect (select) {
    if ($(select).val() == '0' || $(select).val() == '' || $(select).val() == null) {
        $(select).parent().addClass("has-error");
        msgError = msgError + $(select).parent().children('label').text() + '\n';
        return false;
    } else {
        $(select).parent().removeClass("has-error");
        return true;
    }
}

$('input#foto_producto').bind('change', function() {
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

/*Código para validar el archivo que importa datos desde excel*/
var btn_enviar_excel = $("#enviar-excel");
btn_enviar_excel.on('click', function() {
    fileExtension = ['xls', 'xlsx'];
    archivo = $("#archivo-excel").val();
    extension = archivo.split('.').pop().toLowerCase();

    if ($.inArray(extension, fileExtension) == -1) {
        swal({
            title: "Error",
            text: "<span>Solo son admitidos archivos con extensión <strong>xls y xlsx</strong><br>Extensión de archivo seleccionado: <strong>"+ extension +" </strong></span>",
            type: "error",
            html: true,
            confirmButtonColor: "#286090",
            confirmButtonText: "Aceptar",
            closeOnConfirm: false,
        });
        return false;
    } else {
        $('#enviar-excel').hide();
        $('#enviar-excel').submit();
    }
});
/*Fin del código para validar el archivo que importa datos desde excel*/