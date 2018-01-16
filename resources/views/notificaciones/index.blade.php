@extends('admin.main')

@section('content')
<style type="text/css">
	textarea{
		resize: none;
		overflow: hidden;
	}
	ul.select-error{
		border: 1px #a94442 solid!important;
	}
</style>
<link rel="stylesheet" href="{{ asset('plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css')}}"  type="text/css" media="screen"/>
<div class="text-center" style="margin: 20px;">
	<h2>Notificaciones app</h2>
	<div class="row-fluid">
        <div class="span12">
            <div class="grid simple ">
                <div class="grid-title">
                    <div class="grid-body">
                    	<div class="row">
	                    	<div class="col-sm-12 col-xs-12">
					            <div class="alert alert-info alert-dismissible text-left" role="alert">
							        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
							        <strong>Nota: </strong><br>
							        - Complete los campos llamados fecha y hora para programar el momento en que debe enviarse la notificación.<br>
							        - No se pueden programar notificaciones para ser enviadas en fechas u horarios que ya pasaron.<br>
							        - En caso de querer envíar la notificación inmediatamente, deje los campos de fecha y hora vacíos.<br>
							        - Las notificaciones no pueden ser canceladas una vez sean programadas.<br>
							    </div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
						        <ul class="nav nav-tabs" id="tab-01">
						            <li class="active"><a href="#tabNotGen">Notificaciones generales</a></li>
						            <li><a href="#tabNotInd">Notificaciones individuales</a></li>
						        </ul>
						        <div class="tab-content">
						            <div class="tab-pane active" id="tabNotGen">
				                        <form id="form_notificaciones_generales" action="{{url('notificaciones_app/enviar/general')}}" onsubmit="return false" method="POST" autocomplete="off">
										    {{-- <input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}"> --}}
									        <div class="row">
									        	<div class="col-sm-12 col-xs-12 hide">
									                <div class="form-group">
									                    <label for="tipo_notificacion_general">Tipo</label>
									                    <input type="text" class="form-control" id="tipo_notificacion_general" value="general" name="tipo_notificacion">
									                </div>
									            </div>
									        	<div class="col-sm-6 col-xs-12 clockpicker">
				                                    <div class="form-group">
				                                        <label for="hora_general">Hora</label>
				                                        <input type="text" class="form-control timepicker" id="hora_general" name="hora" placeholder="Ej. 08:30">
				                                    </div>
				                                </div>
				                                <div class="col-sm-6 col-xs-12">
						                            <div class="form-group">
						                                <label for="">Fecha</label>
						                                <input type="text" name="fecha" class='form-control' id='fecha_general'>
						                            </div>
						                        </div>
									            <div class="col-sm-12 col-xs-12">
									                <div class="form-group">
									                    <label for="titulo_general">Título</label>
									                    <input type="text" class="form-control" id="titulo_general" maxlength="30" name="titulo" placeholder="Describa en menos de 30 caracteres el título de la notificación">
									                </div>
									            </div>
									            <div class="col-sm-12 col-xs-12">
									                <div class="form-group">
									                    <label for="mensaje_general">Contenido del mensaje</label>
	                   									<textarea class="form-control" id="mensaje_general" name="mensaje" maxlength="140" placeholder="Describa en menos de 140 caracteres el contenido de la notificación" rows="3">{{ isset($datos) ? $datos->direccion : "" }}</textarea>    
									                </div>
									            </div>
									        </div>
									        <button type="submit" class="btn btn-primary" id="enviar_notificacion_general">
									            <i class="fa fa-spinner fa-spin" style="display: none"></i>
									            Enviar
									        </button>
										</form>
						            </div>
						            <div class="tab-pane" id="tabNotInd">
				                        <form id="form_notificaciones_individuales" action="{{url('notificaciones_app/enviar/individual')}}" onsubmit="return false" method="POST" autocomplete="off">
										    {{-- <input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}"> --}}
			                    			<div class="row">
			                    				<div class="col-sm-12 col-xs-12 hide">
									                <div class="form-group">
									                    <label for="tipo_notificacion_individual">Tipo</label>
									                    <input type="text" class="form-control" id="tipo_notificacion_individual" value="individual" name="tipo_notificacion">
									                </div>
									            </div>
			                    				<div class="col-sm-6 col-xs-12 clockpicker">
				                                    <div class="form-group">
				                                        <label for="hora_individual">Hora</label>
				                                        <input type="text" class="form-control timepicker" id="hora_individual" name="hora" placeholder="Ej. 08:30">
				                                    </div>
				                                </div>
				                                <div class="col-sm-6 col-xs-12">
						                            <div class="form-group">
						                                <label for="">Fecha</label>
						                                <input type="text" name="fecha" class='form-control' id='fecha_individual'>
						                            </div>
						                        </div>
			                    				<div class="col-sm-12 col-xs-12">
									                <div class="form-group">
									                    <label for="titulo_individual">Título</label>
									                    <input type="text" class="form-control" id="titulo_individual" maxlength="30" name="titulo" placeholder="Describa en menos de 30 caracteres el título de la notificación">
									                </div>
									            </div>
									            <div class="col-sm-12 col-xs-12">
									                <div class="form-group">
									                    <label for="mensaje_individual">Contenido del mensaje</label>
	                   									<textarea class="form-control" id="mensaje_individual" name="mensaje" maxlength="140" placeholder="Describa en menos de 140 caracteres el contenido de la notificación" rows="3">{{ isset($datos) ? $datos->direccion : "" }}</textarea>    
									                </div>
									            </div>											            
						                        <div class="col-md-12">
									                <div class="form-group">
							                            <label for="usuarios_id">Usuarios</label>
														<select name="usuarios_id[]" id="usuarios_id" class="select2" multiple="multiple" style="width: 100%">
															<option value="0" disabled>Seleccionar usuarios</option>
															@foreach($usuarios as $usuario)
																<option value="{{$usuario->id}}">{{$usuario->nombre}} {{$usuario->apelido}}</option>
															@endforeach
														</select>
													</div>
						                        </div>
			                    			</div>
			                    			<button type="submit" class="btn btn-primary" id="enviar_notificacion_individual">
									            <i class="fa fa-spinner fa-spin" style="display: none"></i>
									            Enviar
									        </button>
				                    	</form>
						            </div>
						        </div>
						    </div>
						</div>
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="{{ asset('plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/tabs_accordian.js') }}"></script>
<script src="{{ asset('js/validacionesNotificaciones.js') }}"></script>
<script src="{{ asset('js/notificacionesAjax.js') }}"></script>
<script type="text/javascript">
	$(function(){
		$('#tab-01 a').click(function (e) {
			e.preventDefault();
        	$('.form-control').parent().removeClass("has-error");
        	$('.form-control').val('');
        	$('select').parent().children('div.select2').children('ul.select2-choices').removeClass("select-error");
			inputs = [];
    		msgError = '';
		})
		
		$(".select2").select2();

		$('.clockpicker ').clockpicker({
	        autoclose: true
	    });
	    
	    $( "#fecha_general, #fecha_individual" ).datepicker({
	        autoclose: true,
	        todayHighlight: true,
	        format: "yyyy-mm-dd",
	    });
	    $('#fecha_general, #fecha_individual').datepicker('setStartDate', "{{$start_date}}");
	})
</script>
@endsection