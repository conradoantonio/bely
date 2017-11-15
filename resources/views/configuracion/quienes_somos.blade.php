@extends('admin.main')

@section('content')
<div class="text-center">
    <h2>¿Quienes somos?</h2>
    <form id="cargar_quienes_somos" action="<?php echo url();?>/cargar/quienes_somos" enctype="multipart/form-data" method="POST" autocomplete="off">
        {{ csrf_field() }}
        <div class="col-sm-6 col-xs-12 hidden">
            <div class="form-group">
                <label for="id">ID</label>
                <input type="text" class="" id="id" name="id">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-sm-push-2 col-sm-pull-2 col col-md-6 col-md-push-3 col-md-pull-3">
                <div class="form-group" style="text-align: center;">
                    <label>Cargar PDF</label>
                    <input type="file" class="" style="margin-left: 110px;" name="quienes_somos_pdf" id="quienes_somos_pdf">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-sm-push-2 col-sm-pull-2 col col-md-6 col-md-push-3 col-md-pull-3">
                <div class="form-group" style="text-align: center;">
                    <label>Cargar Imágen</label>
                    <input type="file" class="" style="margin-left: 110px;" name="quienes_somos_img" id="quienes_somos_img">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group" style="text-align: center;">
                <label>Link de video</label>
                <input type="text" class="" style="width: 400px;" name="link_video" id="link_video" placeholder="">
            </div>
        </div>
        @if(count($pdf) == 1 && $pdf->nombrePDF != null && file_exists(public_path().'/'.$pdf->nombrePDF))
            <div class="row hide" id="">
                <div class="col-xs-12 col-sm-8 col-sm-push-2 col-sm-pull-2 col col-md-6 col-md-push-3 col-md-pull-3">
                    <div class="form-group">
                        <label>Nombre pdf actual</label>
                        <input type="" name="pdf_actual" id="pdf_actual" value="{{$pdf->nombrePDF}}">
                    </div>
                </div>
            </div>
        @endif
        @if(count($pdf) == 1 && $pdf->imagen != null && file_exists(public_path().'/'.$pdf->imagen))
            <div class="row hide" id="">
                <div class="col-xs-12 col-sm-8 col-sm-push-2 col-sm-pull-2 col col-md-6 col-md-push-3 col-md-pull-3">
                    <div class="form-group">
                        <label>Nombre pdf actual</label>
                        <input type="" name="img_actual" id="img_actual" value="{{$pdf->imagen}}">
                    </div>
                </div>
            </div>
        @endif
        <button type="submit" class="btn btn-primary" id="guardar-pdf">Guardar</button>
    </form>
    @if(count($pdf) == 1 && $pdf->nombrePDF != null && file_exists(public_path().'/'.$pdf->nombrePDF))
        <div class="row" id="quienes_somos" style="margin-top: 20px;">
            <div class="col-xs-12 col-sm-8 col-sm-push-2 col-sm-pull-2 col col-md-6 col-md-push-3 col-md-pull-3">
                <div class="form-group">
                    <label>Descargar PDF actual</label>
                    <button type="button" class="btn btn-default" id="descargar_ap_pdf" pdf_name="{{$pdf->nombrePDF}}">Descargar</button>
                </div>
            </div>
        </div>
    @endif
</div>
<script type="text/javascript">
/*Código para validar el archivo pdf*/
var inputs = [];
var vacios = [];
var regExprTexto = /^[a-z ñ # , : ; ¿ ? ! ¡ ' " _ @ ( ) áéíóúäëïöüâêîôûàèìòùç\d_\s \-.]{2,}$/i;
var btn_cargar_quienes_somos = $("button#guardar-pdf");
btn_cargar_quienes_somos.on('click', function() {
    inputs = [];
    vacios = [];
    msgError = '';
    fileExtensionPDF = ['pdf', 'pdf'];
    fileExtensionIMG = ['jpg', 'jpg'];
    archivo = $("#quienes_somos_pdf").val();
    extension = archivo.split('.').pop().toLowerCase();

    validarInput($('input#link_video'), regExprTexto) == false ? inputs.push('Link') : ''
    validarArchivo($('input#quienes_somos_pdf'), fileExtensionPDF) == false ? inputs.push('PDF') : ''
    validarArchivo($('input#quienes_somos_img'), fileExtensionIMG) == false ? inputs.push('Imágen') : ''

    if (vacios.length == 3) {
        $('button#guardar-pdf').show();
        swal("Debe completar al menos uno de los campos");
        return false;
    } else if(inputs.length == 0) {
        $('button#guardar-pdf').hide();
        console.info(vacios.length);
        $('button#guardar-pdf').submit();
    } else {
        $('button#guardar-pdf').show();
        swal("Corrija los siguientes campos para continuar: ", msgError);
        return false;
    }
});

function validarArchivo(campo, extensiones) {
    archivo = $(campo).val();
    extension = archivo.split('.').pop().toLowerCase();

    if ($(campo).val() == '' || $(campo).val() == null) {
        vacios.push('1');
        return true;
    } else if ($.inArray(extension, extensiones) == -1 || mb >= 5) {
        //$(campo).parent().addClass("has-error");
        msgError = msgError + $(campo).parent().children('label').text() + '\n';
        return false;
    } else {
        //$(campo).parent().removeClass("has-error");
        return true;
    }
}

function validarInput (campo,regExpr) {
    if ($(campo).val() == "") {
        vacios.push('1');
        //$(campo).parent().removeClass("has-error");
        return true;
    } else {
        //$(campo).parent().removeClass("has-error");
        return true;
    }
}
/*Fin del código para validar el archivo pdf*/

$('body').delegate('button#descargar_ap_pdf','click', function() {
    var urlChunks = $(this).attr('pdf_name').split('/');
    window.location.href = "<?php echo url();?>/descargar/quienes_somos/" + urlChunks[urlChunks.length - 1];
});
</script>
@endsection