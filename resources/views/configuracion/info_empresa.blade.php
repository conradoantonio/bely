@extends('admin.main')

@section('content')
<style>
th {
    text-align: center!important;
}
textarea {
    resize: none;
}
</style>
<div class="" style="padding: 20px;">
	<div class="row">
		<div class="contactForm text-center">
			<h2 class='form-tittle'>{{isset($header) ? $header : 'Guardar '}} Información</h2>
		</div>
		<div class="col-sm-12 formulariodatos">
			<form id="form_info_empresa" action="<?php echo url();?>/configuracion/info_empresa/{{isset($datos) ? 'editar' : 'guardar'}}" enctype="multipart/form-data" method="POST" autocomplete="off">
				<label class='hide'>Id</label>
				<input type="text" class='hide' value="{{ isset($datos) ? $datos->id : "" }}" name="id" class='form-controlID' id='id' placeholder=""></br>

				<div class="row">					
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Teléfono</label>
							<input type="" class="form-control" name="telefono" id="telefono" placeholder="Ej. 9-80-10-10" value="{{{ isset($datos) ? $datos->telefono : "" }}}">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Código postal</label>
							<input type="" class="form-control" name="codigo_postal" id="codigo_postal" placeholder="Ej. 82195" value="{{{ isset($datos) ? $datos->codigo_postal : "" }}}">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Número Exterior</label>
							<input type="" class="form-control" name="numeroExt" id="numeroExt" placeholder="Ej. #5225" value="{{{ isset($datos) ? $datos->numeroExt : "" }}}">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Número Interior</label>
							<input type="" class="form-control" name="numeroInt" id="numeroInt" placeholder="Ej. 21" value="{{{ isset($datos) ? $datos->numeroInt : "" }}}">
						</div>
					</div>
				</div>		

	            <div class="row">
					<div class="col-sm-12 col-xs-12">
	                    <div class="form-group">
	                        <label>Dirección</label>
	                        <textarea class="form-control" id="direccion" name="direccion" placeholder="Ej. Av. Sávalo #4210" rows="3">{{ isset($datos) ? $datos->direccion : "" }}</textarea>    
	                    </div>
	                </div>
	            </div>
	            @if(isset($datos))
	            	@if($datos->logo != null || $datos->logo != "")
			            <div class="row text-center" id="logo_contenedor">
			                <div class="col-sm-12 col-xs-12">
			                    <div class="form-group">
			                        <label>Foto actual</label>
			                        <img style="width: 40%;" src='<?php echo asset('');?>{{$datos->logo}}' class='img-responsive img-thumbnail' alt='Responsive image' id='logo'>
			                    </div>
			                </div>
			            </div>
		        	@endif
		        @endif
				
				<div class="alert alert-info alert-dismissible" role="alert">
			        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
			        <strong>Nota: </strong>El logo de la empresa debe de ser formato jpg, jpeg o png y pesar menos de 5mb. <br>
			        Favor de subir una imagen de 460x384 px o su equivalente a escala, ya que el sistema redimensionará automáticamente a esas medidas las imagenes subidas.
			    </div>
	            <div class="row">
	            	<div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label>Cargar logo</label>
                            <input type="file" class="form-control" name="logo" id="logo">
                        </div>
                    </div>
                </div>

				<input type="hidden" id="token" name="_token" value="{{csrf_token()}}">

				<button class="btn btn-primary" type="submit" name="guardar_info" value="submit" id="guardar_info">Guardar</button>
			</form>
		</div>
	</div>
</div>

<script src="{{ asset('js/validacionesInfoEmpresa.js') }}" type="text/javascript"></script> 
@endsection