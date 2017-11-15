@extends('admin.main')

@section('content')
<style>
textarea {
	resize: none;
}
div.title{
    overflow:hidden;
    white-space:nowrap;
    text-overflow: ellipsis;
}
button.btn{
    margin-bottom: 3px;
}
</style>
<div class="" style="padding: 20px;">
	<div class="contactForm text-center">
		<h2 class='form-tittle'>Galería de productos</h2>
	</div>

	<div id="galeria" class="row text-center" style="margin-top: 20px;">
		<?php
		if (count($galeria)) {
            $folder = (auth()->user()->empresa_id == 1 ? 'bely/' : (auth()->user()->empresa_id == 2 ? 'gdlbox/' : (auth()->user()->empresa_id == 3 ? 'cosmeticos/' : 'error')));
			foreach ($galeria as $key => $foto) {
				echo "<div id=".$key." class='col-sm-3 text-center foto'>".
						"<a id=".$key." href=".asset('/img/img_productos/'.$folder.$foto)." data-lightbox='roadtrip' class='img-thumbnail' data-title='".$foto."'>".
							"<img class='img-responsive' src=".asset('/img/img_productos/'.$folder.$foto).">".
						"</a>".
						"<div class='title'>".$foto."</div>".
                        "<button class='btn btn-danger btn-sm borrar_foto_galeria'>Borrar</button>".
					"</div>";
			}
		} else {
			echo "<div class='alert alert-info'>".
                	"<button class='close' data-dismiss='alert'></button>".
                	"<a href='#' class='link'>Info:</a> No hay imágenes que mostrar, suba contenido para ver la galería de imágenes de sus productos".
                "</div>";
		}
			
		?>
	</div>
	
</div>

<script type="text/javascript">
$('body').delegate('.borrar_foto_galeria','click', function() {
    var id = $(this).parent().children('a').attr('id');
	var titulo = $(this).parent().children('a').attr('data-title');
	swal({
        title: "¿Realmente desea eliminar la imagen " + "<span style='color:#F8BB86'>" + titulo + "</span>?",
        text: "¡Se eliminará permanentemente del servidor!",
        html: true,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Si, borrar",
        showLoaderOnConfirm: true,
        allowEscapeKey: true,
        allowOutsideClick: true,
        closeOnConfirm: false
    },
    function() {
    	foto = titulo;
    	subdirectorio = '/img/img_productos';
    	token = $('#token').val();
    	borrarGaleria(id,foto,subdirectorio,token);
    });

});

function borrarGaleria(id,foto,subdirectorio,token) {
    $.ajax({
        method: "POST",
        url: "{{url('/galeria/eliminar')}}",
        data:{
            "foto":foto,
            "subdirectorio":subdirectorio,
            "_token":token
        },
        success: function(data) {
        	if (data.msg == 'Eliminado') {
        		$('div#'+id).remove();
        		swal({
	                title: "Foto eliminada",
	                type: "success",
	                timer: 1000,
	            });
        		if ($("#galeria div.foto").length == 0) {
        			$("div#galeria").append(
	                    "<div class='alert alert-info'>"+
		                	"<button class='close' data-dismiss='alert'></button>"+
		                	"<a href='#' class='link'>Info:</a> No hay imágenes que mostrar, suba contenido para ver la galería de imágenes de sus productos"+
		                "</div>"
	                );
        		}
        	} else if (data.msg == 'Fallo eliminando') {
        		swal({
	                title: "Error",
	                text: "Algo salió mal, trate nuevamente.",
	                type: "error",
	            });
        	} else {
        		console.warn("Algo salió mal");
        		$('div#'+id).remove();
        		swal.close();	
        	}
        },
        error: function(xhr, status, error) {
            swal({
                title: "<small>¡Error!</small>",
                text: "Ha ocurrido un error mientras se eliminaba la foto, porfavor, trate nuevamente.<br><span style='color:#F8BB86'>\nError: " + xhr.status + " (" + error + ") "+"</span>",
                html: true
            });
        }
    });
}
</script>
@endsection('content')