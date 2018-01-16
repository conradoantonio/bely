<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
	if (Auth::check()) {
		return redirect()->action('LogController@index');
	} else {
    	return view('welcome');//login
    }
});

/*-- Rutas para el login --*/
Route::resource('log', 'LogController');
Route::post('login', 'LogController@store');
Route::get('logout', 'LogController@logout');

/*-- Rutas para el dashboard --*/
Route::get('/dashboard','LogController@index');//Carga solo el panel administrativo
Route::post('/grafica', 'LogController@get_userSesions');//Carga los datos de la gráfica

/*-- Rutas para la pestaña de usuariosSistema --*/
Route::get('/usuarios/sistema','UsuariosController@index');//Carga la tabla de usuarios del sistema
Route::post('/usuarios/sistema/validar_usuario', 'UsuariosController@validar_usuario');//Checa si un usuario del sistema existe
Route::post('/usuarios/sistema/guardar_usuario', 'UsuariosController@guardar_usuario');//Guarda un usuario del sistema
Route::post('/usuarios/sistema/guardar_foto_usuario_sistema', 'UsuariosController@guardar_foto_usuario_sistema');//Guarda la foto de perfil de un usuario del sistema
Route::post('/usuarios/sistema/eliminar_usuario', 'UsuariosController@eliminar_usuario');//Elimina un usuario del sistema
Route::post('/usuarios/sistema/change_password', 'UsuariosController@change_password');//Elimina un usuario del sistema

/*-- Rutas para la pestaña usuariosApp--*/
Route::get('/usuarios/app','UsuariosController@usuariosApp');//Carga la tabla de usuarios de la aplicación
Route::get('/usuarios/app/exportar_usuarios_app','ExcelController@exportar_usuarios_app');//Exporta todos los usuarios de la aplicación a excel
Route::post('/usuarios/app/guardar_usuario_app', 'UsuariosController@guardar_usuario_app');//Guarda un nuevo usuario de la aplicación
Route::post('/usuarios/app/editar_usuario_app', 'UsuariosController@editar_usuario_app');//Edita un usuario de la aplicación
Route::post('/usuario/cambiarStatus', 'UsuariosController@destroy');//Da de baja un usuario

/*-- Ruta para la pestaña de productos --*/
Route::get('/productos','productoController@index');//Carga la tabla de productos del sistema
Route::post('/productos/guardar', 'productoController@guardar_producto');//Guarda un producto
Route::post('/productos/editar', 'productoController@editar_producto');//Edita un producto
Route::post('/productos/eliminar', 'productoController@eliminar_producto');//Elimina un producto
Route::post('/productos/importar_productos', ['as' => '/productos/importar_productos', 'uses' => 'ExcelController@importar_productos']);//Carga los productos a excel
Route::get('/productos/exportar_productos/{empresa_id}/{fecha_inicio}/{fecha_fin}', 'ExcelController@exportar_productos');//Exporta ciertos productos a excel
Route::post('/productos/cargar_subcategorias', 'productoController@cargar_subcategorias');//Carga las subcategorías de una categoría.

/*-- Rutas para la pestaña de configuración --*/
Route::get('/configuracion/preguntas_frecuentes','configuracionController@preguntas_frecuentes');//Carga la tabla de preguntas frecuentes.
Route::post('/preguntas_frecuentes/guardar_pregunta', 'configuracionController@guardar_pregunta');//Guarda una pregunta
Route::post('/preguntas_frecuentes/editar_pregunta', 'configuracionController@editar_pregunta');//Edita una pregunta
Route::post('/preguntas_frecuentes/eliminar_pregunta', 'configuracionController@eliminar_pregunta');//Elimina una pregunta
Route::get('/configuracion/quienes_somos','configuracionController@quienes_somos');//Carga el formulario para cargar el pdf de quienes somos.
Route::get('/descargar/quienes_somos/{pdf}', 'archivosController@descargar_quienes_somos');//Descarga el archivo de quienes somos.
Route::post('/cargar/quienes_somos', 'archivosController@cargar_quienes_somos');//Carga el archivo de quienes somos.

/*-- Rutas para la pestaña de pedidos --*/
Route::get('/pedidos','pedidosController@index');//Carga la vista para los pedidos realizados del panel
Route::post('/pedidos/agregar_num_seguimiento','pedidosController@agregar_num_seguimiento');//Agrega un número de seguimiento a un pedido
Route::post('/pedidos/obtener_info_pedido','pedidosController@obtener_pedido_por_id');//Obtiene la información de un pedido por su id.
Route::post('/pedidos/asignar_guia','pedidosController@asignar_numero_guia');//Actualiza el número de seguimiento (numero de guía) de un pedido.

/*-- Rutas para la pestaña de cotización (Solo disponible para el tipo de usuario GDLBOX) --*/
Route::get('/cotizaciones','cotizacionesController@index');//Carga la vista para las cotizaciones realizadas.
Route::post('/cotizaciones/ver_cotizacion_detalles','cotizacionesController@ver_cotizacion_detalles');//Muestra los detalles de una cotización en específico.
Route::post('/cotizaciones/finalizar_cotizacion','cotizacionesController@finalizar_cotizacion');//Marca como finalizada la cotización.

/*-- Rutas para la subpestaña de preferencias de envíos --*/
Route::get('/configuracion/preferencias','configuracionController@preferencias_envio');//Carga la vista para las preferencias de envío.
Route::post('/configuracion/preferencias/activar_envio_gratuito','configuracionController@activar_envio_gratuito');//Activa o desactiva el envío gratuito.
Route::post('/configuracion/preferencias/cambiar_monto_minimo','configuracionController@cambiar_monto_minimo');//Cambia el monto mínimo para el envío gratuito.
Route::post('/configuracion/preferencias/cambiar_tarifa_envio','configuracionController@cambiar_tarifa_envio');//Cambia el monto de envío.

/*-- Rutas para la subpestaña de información empresa --*/
Route::get('/configuracion/info_empresa','configuracionController@info_empresa');//Carga la vista para la información de la empresa.
Route::post('/configuracion/info_empresa/guardar','configuracionController@guardar_info_empresa');//Guarda la información de la empresa.
Route::post('/configuracion/info_empresa/editar','configuracionController@editar_info_empresa');//Edita la información de la empresa.

/*-- Rutas para la pestaña cargar imagenes --*/
Route::get('/cargar_imagenes','imagenController@index');//Carga el formulario de dropzone para cargar imagenes
Route::post('/subir_imagenes','imagenController@subir_imagenes');//Carga las imágenes al servidor

/*-- Rutas para la pestaña de galería --*/
Route::get('/galeria','imagenController@cargar_galeria');//Carga el login de ionic
Route::post('/galeria/eliminar', 'imagenController@eliminar_galeria');//Da de baja un usuario

/*-- google analytics --*/
Route::get('/data','estadosController@analytics');//Devuelve los datos de google analytics

/**
 *=======================================================================================================================
 *=                           Empiezan las funciones relacionadas a la api para la aplicación                           =
 *=======================================================================================================================
 */
Route::get('/form','dataAppController@cargar_form_conekta');//Carga el formulario de prueba de conekta

Route::post('/generar_token','dataAppController@generar_token');//Genera un token de prueba
Route::post('/post_send','dataAppController@post_send');//Procesa los datos después de generar el token
Route::post('app/validar_cargo','dataAppController@crear_cliente');//Crea un cliente
Route::post('app/validar_cargo_oxxo','dataAppController@crear_cliente_oxxo');//Crea un cliente
//Route::post('/crear_cliente','dataAppController@crear_cliente');//Crea un cliente
Route::post('/procesar_orden','dataAppController@procesar_orden');//Procesa una orden
Route::post('/app/orden_empresa','dataAppController@obtener_ordenes');//Obtiene las pedidos de las empresas

Route::post('/app/registro_usuario','dataAppController@registro_app');//Registra un usuario en la aplicación.
Route::post('/app/login','dataAppController@login_app');//Valida el inicio de sesión de un usuario en la aplicación.
Route::post('/app/actualizar_usuario','dataAppController@actualizar_datos_usuario');//Actualiza los datos del usuario a excepción de la contraseña, email y foto.
Route::post('/app/recuperar_contra','dataAppController@recuperar_contra');//Envía una contraseña nueva generada automáticamente al correo del usuario.
Route::post('/app/actualizar_foto','dataAppController@actualizar_foto');//Actualiza la foto de perfil de un usuario.
Route::post('/app/agregar_direccion','dataAppController@agregar_direccion_usuario_app');//Agrega una dirección para el usuario.
Route::post('/app/actualizar_direccion','dataAppController@actualizar_direccion_usuario_app');//Actualiza una dirección del usuario.
Route::post('/app/listar_direcciones','dataAppController@listar_direcciones');//Muestra una lista de todas las direcciones del usuario de la aplicación.
Route::post('/app/eliminar_direccion','dataAppController@eliminar_direccion_usuario_app');//Elimina una dirección del usuario de la aplicación.
Route::get('/app/productos_empresa/{empresa_id}','dataAppController@producto_por_empresa');//Regresa todos los productos de una empresa.
Route::get('/app/productos_oferta/{empresa_id}','dataAppController@productos_oferta');//Regresa todos los productos que se encuentran en oferta.
Route::get('/app/quienes_somos','dataAppController@obtener_quienes_somos');//Regresa la información relacionada sobre el contenido de quienes somos
Route::post('/app/info_empresas','dataAppController@info_empresas');//Muestra la información de las empresas de la plataforma.
Route::post('/app/info_empresas/costo_envios','dataAppController@informacion_envio');//Muestra la información de envío de las empresas.
Route::get('/app/preguntas_frecuentes','dataAppController@obtener_preguntas_frecuentes');//Regresa todas las preguntas frecuentes de la aplicación.
Route::post('/app/obtener_pedidos_usuario','dataAppController@obtener_pedidos_usuario');//Devuelve los pedidos del usuario hechas desde la aplicación.
Route::post('/app/generar_cotizacion','dataAppController@guardar_cotizacion');//Guarda una nueva cotización para GDLBOX.
Route::post('/app/obtener_cotizaciones_usuario','dataAppController@obtener_cotizaciones_usuario');//Obtiene las cotizaciones de un usuario.
Route::post('/app/enviar_correo_detalle_orden','dataAppController@enviar_correo_detalle_orden');//Envía un correo electrónico con los detalles de la orden.
Route::post('/app/enviar_correo_detalle_cotizacion','dataAppController@enviar_correo_detalle_cotizacion');//Envía un correo electrónico con los detalles de la cotización.
Route::post('/app/actualizar_player_id','dataAppController@actualizar_player_id');//Actualiza el player id de un usuario de la aplicación

/*-- Rutas para las notificaciones --*/
Route::group(['prefix' => 'notificaciones_app', 'middleware' => 'auth'], function () {
	Route::get('/','NotificacionesController@index');//Carga el panel para mandar notificaciones a la aplicación.
	Route::post('/enviar/general','NotificacionesController@enviar_notificacion_general');//Manda una notificación a todos los usuarios suscritos de la aplicación.
	Route::post('/enviar/individual','NotificacionesController@enviar_notificacion_individual');//Manda una notificación a los usuarios seleccionados de la áplicación.
});

Route::post('/app/webhook', function()
{
	header('HTTP/1.1 200 OK');
	$body = @file_get_contents('php://input');
	$event = json_decode($body);
	$charge = $event->data->object;
	
	if ($data->type == 'charge.paid') {
		$msg = "Tu pago ha sido comprobado.";
		mail("anton_con@hotmail.com","Pago confirmado",$msg);
	}
});