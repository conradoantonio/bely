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
			<h2 class='form-tittle'>{{isset($header) ? $header : 'Realizar '}} pago</h2>
		</div>
		<div class="col-sm-12 form_prueba">

			<form action="<?php echo url();?>/generar_token" method="POST" id="card-form" autocomplete="off">
				{{ csrf_field() }}
				<div class="row" style="border-style: solid;">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Nombre del usuario</label>
							<input type="text" class="form-control" size="20" id="nombre" name="nombre">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Correo del usuario</label>
							<input type="text" class="form-control" size="100" id="correo" name="correo">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Teléfono del usuario</label>
							<input type="text" class="form-control" size="18" id="telefono" name="telefono">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Empresa id</label>
							<input type="text" class="form-control" size="18" id="empresa_id" value="{{Auth::user()->empresa_id}}" name="empresa_id">
						</div>
					</div>
				</div>


				<span class="card-errors"></span>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Nombre del tarjetahabiente</label>
							<input type="text" class="form-control" size="20" data-conekta="card[name]" id="name_card">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="">Número de tarjeta de crédito</label>
							<input class="form-control" type="text" size="20" data-conekta="card[number]" id="number_card">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="">CVC</label>
							<input type="text" size="4" class="form-control" data-conekta="card[cvc]" id="cvc">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="">Fecha de expiración (Mes MM)</label>
							<input type="text" size="2" class="form-control" data-conekta="card[exp_month]" id="mm">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="">Fecha de expiración (Año AAAA)</label>
							<input type="text" size="4" class="form-control" data-conekta="card[exp_year]" id="yyyy">
						</div>
					</div>
				</div>
				
				<button type="submit" class="btn btn-primary">Crear token</button>
			</form>
		</div>
	</div>
</div>

<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> -->
<script type="text/javascript" src="https://conektaapi.s3.amazonaws.com/v0.3.2/js/conekta.js"></script>
<script>
$( document ).ready(function() {
    $('#nombre').val('Bridge Studio Test');
    $('#correo').val('bridgestudiotest@gmail.com');
    $('#telefono').val('6619840556');

    /*$('#name_card').val('Omar Mendoza');
    $('#number_card').val('5579100148178039');*/
    $('#name_card').val('Bridge Studio Test');
    $('#number_card').val('4152313255720414');
    $('#cvc').val('263');
    $('#mm').val('02');
    $('#yyyy').val('2021');

    
});
Conekta.setPublishableKey('key_IxEHzx7fypYtFjqQXmHfrew');

	var conektaSuccessResponseHandler = function(token) {
		var $form = $("#card-form");
		//Inserta el token_id en la forma para que se envíe al servidor
		$form.append($("<input type='hidden' name='conektaTokenId' id='conektaTokenId'>").val(token.id));
		$form.get(0).submit(); //Hace submit
	};
	var conektaErrorResponseHandler = function(response) {
		var $form = $("#card-form");
		$form.find(".card-errors").text(response.message_to_purchaser);
		$form.find("button").prop("disabled", false);
	};

	//jQuery para que genere el token después de dar click en submit
	$(function () {
		$("#card-form").submit(function(event) {
			var $form = $(this);
			// Previene hacer submit más de una vez
			$form.find("button").prop("disabled", true);
			Conekta.token.create($form, conektaSuccessResponseHandler, conektaErrorResponseHandler);
			return false;
		});
	});
</script>
@endsection