<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\usuariosModel;
use App\direccionesUsuarioModel;
use App\User;
use App\Producto;
use App\Favorito;
use App\quienes_somosModel;
use App\Pedidos;
use App\PedidoDetalles;
use App\Cotizaciones;
use App\CotizacionesDetalles;
use PDO;
use DB;
use Session;
use Auth;
use Mail;

require_once("conekta-php-master/lib/Conekta.php");
\Conekta\Conekta::setApiKey("key_wsnGdPKAe4pyTFhCs84qVw");
\Conekta\Conekta::setApiVersion("2.0.0");

class dataAppController extends Controller
{
    /**
     * Crea un nuevo usuario en caso de que el email proporcionado no se haya utilizado antes para un usuario.
     *
     * @param  Request $request
     * @return $usuario_app->id si es correcto el inicio de sesión o 0 si el email proporcionado se encuentra ya registrado.
     */
    public function registro_app(Request $request) 
    {
        if(count(usuariosModel::buscar_usuario_por_correo($request->correo))) {
            return 0;
        } else {
            $usuario_app = new usuariosModel;
            $usuario_app->password = md5($request->password);
            $usuario_app->correo = $request->correo;
            $usuario_app->nombre = $request->nombre;
            $usuario_app->apellido = $request->apellido;
            $usuario_app->telefono = $request->telefono;
            $usuario_app->celular = $request->celular;
            $usuario_app->fechaRegistro = date('Y-m-d H:i:s');
            $usuario_app->foto_perfil = "img/usuario_app/default.jpg";
            $usuario_app->tarjeta = $request->has('tarjeta') ? $request->tarjeta : '';
            $usuario_app->estado = $request->has('estado') ? $request->estado : '';
            $usuario_app->genero_id = $request->has('genero_id') ? $request->genero_id : 0;
            $usuario_app->fechaNacimiento = $request->fechaNacimiento;
            $usuario_app->status = 2;

            $usuario_app->save();

            DB::table('registro_logs')->insert([
                'user_id' => $usuario_app->id,
                'fechaLogin' => DB::raw('CURDATE()')
            ]);
        }

        return $usuario_app;
    }

    /**
     * Valida que los datos de un login sean correctos en la aplicación y registra un log
     *
     * @param  Request  $request
     * @return $usuario si es correcto el inicio de sesión o 0 si los datos son incorrectos.
     */
    public function login_app(Request $request) 
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $usuario = DB::table('usuario')
        ->select(DB::raw("usuario.id, nombre, apellido, correo, tarjeta, fechaRegistro, foto_perfil, estado, 
        genero_id, genero.nombreGenero, fechaNacimiento, telefono, celular, usuario.status"))
        ->leftJoin('genero', 'usuario.genero_id', '=', 'genero.id')
        ->where('usuario.correo', '=', $request->correo)
        ->where('usuario.password', '=', md5($request->password))
        //->where('usuario.status', '=', 1)
        ->first();

        if(count($usuario) > 0) {
            if ($usuario['status'] == 2) {
                return 1;//Esperando aprobación
            }
            $this->logs($usuario['id']);
            return $usuario;//Ya se aprobó
        } else {
            return 0;//No se encontró nada
        }
    }

    /**
     * Actualiza todos los datos de un usuario a excepción de la foto de perfil, contraseña y correo.
     *
     * @param  Request  $request
     * @return $usuario_app
     */
    public function actualizar_datos_usuario(Request $request) 
    {
        $usuario_app = usuariosModel::find($request->id);

        if (count($usuario_app)) {
            //$usuario_app->correo = $request->correo;
            $usuario_app->nombre = $request->nombre;
            $usuario_app->apellido = $request->apellido;
            $usuario_app->telefono = $request->telefono;
            $usuario_app->celular = $request->celular;
            $usuario_app->tarjeta = $request->tarjeta;
            $usuario_app->estado = $request->estado;
            $usuario_app->genero_id = $request->genero_id;
            $request->password ? $usuario_app->password = md5($request->password) : '';
            $usuario_app->fechaNacimiento = $request->fechaNacimiento;

            $usuario_app->save();

            return $usuario_app;
        }

        return ['msg'=>'Sin actualizar'];
    }

    /**
     * Guarda un producto como favorito
     *
     * @param  Request  $request
     * @return response
     */
    public function guardar_favorito(Request $req) 
    {
        $exist = Favorito::not_repeat($req->user_id, $req->producto_id);
        $user = usuariosModel::find($req->user_id);
        $producto = Producto::find($req->producto_id);

        if (!$user) { return response(['msg' => 'ID de usuario inválido', 'status' => 'error'], 200); }
        if (!$producto) { return response(['msg' => 'ID de producto inválido', 'status' => 'error'], 200); }
        if (count($exist)) { return response(['msg' => 'No se puede agregar dos veces el mismo producto', 'status' => 'error'], 200); }

        $fav = New Favorito;

        $fav->user_id = $req->user_id;
        $fav->producto_id = $req->producto_id;

        $fav->save();

        return response(['msg' => 'Se agregó un nuevo producto favorito', 'status' => 'ok'], 200);
    }

    /**
     * Elimina un producto de favoritos
     *
     * @param  Request  $request
     * @return response
     */
    public function eliminar_favorito(Request $req) 
    {
        $favs = Favorito::where('user_id', $req->user_id)
        ->where('producto_id', $req->producto_id)
        ->delete();

        return response(['msg' => 'Producto eliminado', 'status' => 'ok'], 200);
    }

    /**
     * Lista los productos favoritos de un usuario
     *
     * @param  Request  $request
     * @return response
     */
    public function listar_favoritos(Request $req) 
    {
        $timer = null;
        $opts = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', 1)->first();

        if ($opts->mostrar_timer) {
            $timer = $opts->dia_limite.' '.$opts->hora_limite;
        }
        
        //$select = $select.", ROUND((producto.precio) - ((producto.precio * $discount) /100), 2) AS precio_descuento";


        $favs = Favorito::where('user_id', $req->user_id)
        ->get();

        if (count($favs)) {
            foreach ($favs as $fav) {
                $fav->producto;
                if ($timer > $this->actual_datetime) {
                    $fav->producto->timer = $timer;
                }
                if ($opts->descuento_activo == 1) {
                    $fav->producto->precio_descuento = round(($fav->producto->precio) - (($fav->producto->precio * $opts->descuento_porcentaje) / 100), 2);
                }
            }
            return response(['msg' => 'Productos favoritos encontrados', 'data' => $favs, 'status' => 'ok'], 200);
        }
        return response(['msg' => 'El usuario no cuenta con productos favoritos', 'status' => 'ok'], 200);
    }

    /**
     * Agrega una dirección de envío para un usuario
     *
     * @param  Request  $request
     * @return $direccion
     */
    public function agregar_direccion_usuario_app(Request $request) 
    {
        $direccion = new direccionesUsuarioModel;

        $direccion->usuario_id = $request->usuario_id;
        $direccion->recibidor = $request->recibidor;
        $direccion->calle = $request->calle;
        $direccion->num_ext = $request->num_ext;
        $direccion->num_int = $request->num_int;
        $direccion->estado = $request->estado;
        $direccion->ciudad = $request->ciudad;
        $direccion->pais = 'MX';
        $direccion->codigo_postal = $request->codigo_postal;
        $direccion->residencial = $request->residencial;
        $direccion->is_main = 0;

        $direccion->save();

        return $direccion;
    }

    /**
     * Actualizar una dirección de envío para un usuario
     *
     * @param  Request  $request
     * @return $direccion
     */
    public function actualizar_direccion_usuario_app(Request $request) 
    {
        $direccion = direccionesUsuarioModel::find($request->id);

        if (count($direccion)) {
            $direccion->recibidor = $request->recibidor;
            $direccion->calle = $request->calle;
            $direccion->num_ext = $request->num_ext;
            $direccion->num_int = $request->num_int;
            $direccion->estado = $request->estado;
            $direccion->ciudad = $request->ciudad;
            $direccion->pais = 'MX';
            $direccion->codigo_postal = $request->codigo_postal;
            $direccion->residencial = $request->residencial;
            $direccion->is_main = 0;

            $direccion->save();

            return $direccion;
        }

        return ['msg' => 'Error actualizando la dirección']; 
    }

    /**
     * Elimina una dirección de envío para un usuario
     *
     * @param  Request  $request
     * @return $direccion
     */
    public function eliminar_direccion_usuario_app(Request $request) 
    {
        $direccion = direccionesUsuarioModel::find($request->id);

        if (count($direccion)) {

            $direccion->delete();

            return $direccion;
        }

        return ['msg' => 'Error eliminando la dirección']; 
    }

    /**
     * Muestra una lista de todas las direcciones del usuario de la aplicación
     *
     * @param  Request  $request
     * @return $direcciones
     */
    public function listar_direcciones(Request $request) 
    {
        $direcciones = direccionesUsuarioModel::where('usuario_id', $request->usuario_id)
        ->get();

        if (count($direcciones)) {
            return $direcciones;
        }

        return ['msg' => 'El usuario no cuenta con direcciones.'];
    }

    /**
     * Regresa todos los productos de una empresa.
     *
     * @param  int  $empresa_id
     * @return $productos
     */
    public function producto_por_empresa($empresa_id)
    {
        $select = "producto.id as producto_id, producto.codigo, producto.sku, producto.nombre, producto.precio, producto.stock,
            producto.descripcion, IF(producto.oferta=1,'si', 'no') AS oferta, producto.foto_producto,
            empresa.nombre AS 'empresa', categoria.categoria, subcategoria.subcategoria";

        $descuento = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $empresa_id)->first();
        if ($descuento) {//Si hay descuento aplicable en la empresa
            if ($descuento->descuento_activo == 1) {
                $discount = $descuento->descuento_porcentaje;
                $select = $select.", ROUND((producto.precio) - ((producto.precio * $discount) /100), 2) AS precio_descuento";
            }
        }

        $categorias = Producto::categorias_empresa($empresa_id);
        foreach ($categorias as $categoria) {
            $productos = DB::table('producto')
            ->select(DB::raw($select))
            ->leftJoin('empresa', 'producto.empresa_id', '=', 'empresa.id')
            ->leftJoin('categoria', 'producto.categoria_id', '=', 'categoria.id')
            ->leftJoin('subcategoria', 'producto.subcategoria_id', '=', 'subcategoria.id')
            ->where('producto.empresa_id', '=', $empresa_id)
            ->where('producto.categoria_id', '=', $categoria->id)
            ->where('producto.stock', '>', 0)
            ->where('producto.status', 1)
            ->orderBy('producto.nombre')
            ->get();

            foreach ($productos as $value) {
                if ($descuento->mostrar_timer) {
                    $time_ofert = $descuento->dia_limite.' '.$descuento->hora_limite;
                    if ($time_ofert > $this->actual_datetime) {
                        $value->fecha_limite = $time_ofert;
                    }
                }
            }
   
            $categoria->productos = $productos;
        }

        return $categorias;
    }

    /**
     * Regresa todos los productos que se encuentren en oferta.
     *
     * @param  Request $request
     * @return $productos 
     */
    public function productos_oferta($empresa_id)
    {
        $select = "producto.id as producto_id, producto.codigo, producto.sku, producto.nombre, producto.precio, 
            producto.stock, producto.descripcion, producto.oferta, producto.foto_producto, 
            empresa.nombre AS 'empresa', categoria.categoria, subcategoria.subcategoria";
            
        $descuento = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $empresa_id)->first();
        if ($descuento) {//Si hay descuento aplicable en la empresa
            if ($descuento->descuento_activo == 1) {
                $discount = $descuento->descuento_porcentaje;
                $select = $select.", ROUND((producto.precio) - ((producto.precio * $discount) /100), 2) AS precio_descuento";
            }
        }

        $categorias = DB::table('categoria')->where('empresa_id', $empresa_id)->get();
        foreach ($categorias as $categoria) {
            $productos = DB::table('producto')
            ->select(DB::raw($select))
            ->leftJoin('empresa', 'producto.empresa_id', '=', 'empresa.id')
            ->leftJoin('categoria', 'producto.categoria_id', '=', 'categoria.id')
            ->leftJoin('subcategoria', 'producto.subcategoria_id', '=', 'subcategoria.id')
            ->where('producto.empresa_id', '=', $empresa_id)
            ->where('producto.categoria_id', '=', $categoria->id)
            ->where('oferta', 1)
            ->where('producto.stock', '>', 0)
            ->where('producto.status', 1)
            ->orderBy('empresa')
            ->get();

            foreach ($productos as $value) {
                if ($descuento->mostrar_timer) {
                    $time_ofert = $descuento->dia_limite.' '.$descuento->hora_limite;
                    if ($time_ofert > $this->actual_datetime) {
                        $value->fecha_limite = $time_ofert;
                    }
                }
            }

            $categoria->productos = $productos;
        }

        return $categorias;
    }

    /**
     * Envía un correo con una nueva contraseña generada por el sistema al email proporcionado,
     * siempre y cuando este exista en la tabla de usuario.
     *
     * @param  string  $email
     * @return ['success'=>true] si el correo fue enviado exitosamente, ['success'=>false] si no se envió.
     */
    public function recuperar_contra(Request $request)
    {
        if (count(usuariosModel::buscar_usuario_por_correo($request->correo))) {
            $new_pass = str_random(7);
            DB::table('usuario')
            ->where('correo', $request->correo)
            ->update(['password' => md5($new_pass)]);

            $msg = "Se ha cambiado la contraseña para el acceso a la aplicación Bely.".
            "\nSu nueva contraseña es: ".$new_pass.
            "\nNo brinde a ninguna persona información confidencial sobre sus contraseñas o tarjetas.";
            $subject = "Restablecimiento de contraseña";
            $to = $request->correo;

            $enviado = Mail::raw($msg, function($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });

            if ($enviado) {
                return ['msg'=>'Enviado'];
            }
        }

        return ['msg'=>'Error al enviar correo'];
    }

    /**
     * Actualiza una foto de perfil de un usuario.
     *
     * @param  Request $request
     * @return $nombre_foto si la imagen fue subida exitosamente, 0 si hubo algún error subiendo la imagen.
     */
    public function actualizar_foto(Request $request)
    {
        $target_path = public_path()."/img/usuario_app/";
        $extension = explode('.', basename( $_FILES['file']['name']));
        $nombre_foto = time().'.'.$extension[1];
        $target_path = $target_path . $nombre_foto;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $usuario_app = usuariosModel::find($request->id);
            $usuario_app->foto_perfil = "img/usuario_app/".$nombre_foto;
            $usuario_app->save();
            return $usuario_app->foto_perfil;
        } else {
            echo $target_path;
            echo "There was an error uploading the file, please try again!";
            return 0;
        }
    }

    /**
     * Obtiene todas las órdenes hechas por los usuarios.
     *
     * @param  
     * @return $ordenes
     */
    public function obtener_ordenes()
    {
        return Pedidos::obtener_pedidos();
    }

    /**
     * Obtiene la información sobre quienes somos.
     *
     * @return 
     */
    public function obtener_quienes_somos()
    {
        return quienes_somosModel::obtener_quienes_somos() ? quienes_somosModel::obtener_quienes_somos() : 0;
    }

    /**
     * Obtiene la información de todas las empresas.
     *
     * @return 
     */
    public function info_empresas()
    {
        return DB::table('informacion_empresa')
        ->select(DB::raw('informacion_empresa.*, empresa.nombre as empresa'))
        ->leftJoin('empresa', 'informacion_empresa.empresa_id', '=', 'empresa.id')
        ->get();
    }

    /**
     * Registra un nuevo inicio de sesión de la aplicación.
     *
     * @param  $id_usuario
     */
    public function logs($id_usuario) {
        DB::table('registro_logs')->insert([
            'user_id' => $id_usuario,
            'fechaLogin' => DB::raw('CURDATE()'),
            'realTime' => DB::raw('NOW()')
        ]);
    }

    /**
     *===================================================================================================================================
     *=                                     Empiezan las funciones relacionadas a la api de conekta                                     =
     *===================================================================================================================================
     */

    /**
     * Busca si existe un usuario con un customer_id_conekta en la base de datos, si lo encuentra actualiza su método de pago
     * Caso contrario, se crea un cliente con la información del request.
     * Después, se crea la orden con los datos del request llamando la función procesar_orden()
     *
     * @param  Request $request
     * @return Retorna ['msg' => 'Cargo realizado'] en caso de que se haya aprobado el cargo
     *         Caso contrario, regresará errores de conekta
     */
    public function crear_cliente(Request $request)
    {
        if (!$this->verificar_compra_minima($request)) {
            return ['msg' => 'La primera compra debe ser mayor a $3000 MXN'];
        }
        $direccion = usuariosModel::direccion_usuario($request->direccion_id);
        if(!$direccion && $request->tipo_envio != 4) {//Si no hay una dirección de envío se cancela
            return ['msg' => 'No se agregó ninguna dirección de envío.'];
        }
        $direccion_num = $direccion['calle']. " No. Ext: ". $direccion['num_ext'];
        $direccion_num = $direccion['num_int'] ? $direccion_num. " No. Int: ". $direccion['num_int'] : $direccion_num;

        $invalidos = $this->verificar_stock_productos($request->productos);
        if ($invalidos) {//Si hay productos inválidos se cancela
            return $invalidos;
        }

        $customer_id_conekta = usuariosModel::buscar_id_conekta_usuario_app($request->correo);
        if ($customer_id_conekta) {//Se registrará una tarjeta nuevamente para el usuario
            $customer = \Conekta\Customer::find($customer_id_conekta);

            if (count($customer['payment_sources'])) {//Si tiene algún método de pago extra, entonces que se elimine y se crea uno nuevo
                $customer->payment_sources[0]->delete();
            }
            $customer = \Conekta\Customer::find($customer_id_conekta);//Se tiene que volver a buscar
            $source = $customer->createPaymentSource(array(
                'token_id' => $request->conektaTokenId,
                'type'     => 'card'
            ));
            
            $customer = \Conekta\Customer::find($customer_id_conekta);
            $response = $this->procesar_orden($request, $customer_id_conekta, $direccion);
            return $response;

        } else {
            try {
                $cliente = \Conekta\Customer::create(
                    array(
                        "name" => $request->nombre,
                        "email" => $request->correo,
                        "phone" => $request->telefono,
                        "payment_sources" => array(
                            array(
                                "type" => "card",
                                "token_id" => $request->conektaTokenId
                            )
                        ),//payment_sources
                        'shipping_contacts' => array(array(
                            'phone' => $request->telefono,
                            'receiver' => $request->tipo_envio != 4 ? $direccion['recibidor'] : "Belyapp Joyería",
                            'address' => array(
                                'street1' => $request->tipo_envio != 4 ? $direccion_num : "Paseo Hospicio #22 San Juan de Dios",
                                'city' => $request->tipo_envio != 4 ? $direccion['ciudad'] : "Guadalajara",
                                'state' => $request->tipo_envio != 4 ? $direccion['estado'] : "Jalisco",
                                'country' => $request->tipo_envio != 4 ? $direccion['pais'] : "Mx",
                                'postal_code' => $request->tipo_envio != 4 ? $direccion['codigo_postal'] : "44360",
                                'residential' => true
                            )
                        ))
                    )//customer
                );

                usuariosModel::actualizar_id_conekta_usuario_app($request->correo, $cliente['id']);
                $customer = \Conekta\Customer::find($cliente->id);
                $response = $this->procesar_orden($request, $cliente->id, $direccion);

                return $response;
                
            } catch (\Conekta\ErrorList $errorList) {
                $msg_errors = '';
                foreach ($errorList->details as &$errorDetail) {
                    $msg_errors .= $errorDetail->getMessage();
                    //echo $errorDetail->getMessage();
                }
                return ['msg' => 'Datos del cliente incorrectos: '.$msg_errors];
            }
        }
        
    }

    public function procesar_orden($request, $customer_id_conekta, $direccion, $oxxo = false)
    {
        $charge_ar = array();
        if ($oxxo) {
            date_default_timezone_set('America/Mexico_City');//Esto fue puesto para obtener corectamente la hora en local, remover si es necesario
            $hora = date("Y-m-d H:i:s");
            $hora = date('Y-m-d H:i:s', strtotime($hora. ' + 1 days'));
            //dd($hora);
            $time_number = strtotime($hora);
            //dd($time_number);
            $charge_ar["type"] = "oxxo_cash";
            $charge_ar["expires_at"] = $time_number;
        } else {
            $charge_ar["type"] = "default";
        }
        $direccion_num = $direccion['calle']. " No. Ext: ". $direccion['num_ext'];
        $direccion_num = $direccion['num_int'] ? $direccion_num. " No. Int: ". $direccion['num_int'] : $direccion_num;
        $costo = $this->validar_costo_envio($request->empresa_id, $request->productos, $request->tipo_envio);
        try {
            $order = \Conekta\Order::create(
                array(
                    "line_items" => $request->productos,
                    "shipping_lines" => array(
                        array(
                            "amount" => $costo,
                            "carrier" => "Belyapp Joyería"
                        )
                    ), //shipping_lines
                    "currency" => "MXN",
                    "customer_info" => array(
                        "customer_id" => $customer_id_conekta
                    ), //customer_info
                    "shipping_contact" => array(
                        "phone" => $request->telefono,
                        "receiver" => $request->tipo_envio != 4 ? $direccion['recibidor'] : "Belyapp Joyería",
                        "address" => array(
                            'street1' => $request->tipo_envio != 4 ? $direccion_num : "Paseo Hospicio #22 San Juan de Dios",
                            'city' => $request->tipo_envio != 4 ? $direccion['ciudad'] : "Guadalajara",
                            'state' => $request->tipo_envio != 4 ? $direccion['estado'] : "Jalisco",
                            'country' => $request->tipo_envio != 4 ? $direccion['pais'] : "Mx",
                            'postal_code' => $request->tipo_envio != 4 ? $direccion['codigo_postal'] : "44360",
                            'residential' => true
                        )//address
                    ), //shipping_contact
                    "charges" => array(
                        array(
                            "payment_method" => $charge_ar
                        ) //first charge
                    ) //charges
                )//order
            );

            date_default_timezone_set('America/Mexico_City');//Esto fue puesto para obtener corectamente la hora en local, remover si es necesario
            /*Se inserta un nuevo pedido en la base de datos*/
            $pedido = new Pedidos;
            $pedido->conekta_order_id = $order->id;
            $pedido->nombre_cliente = $request->nombre;
            $pedido->correo_cliente = $request->correo;
            $pedido->empresa_id = $request->empresa_id;
            $pedido->customer_id_conekta = $customer_id_conekta;
            $pedido->costo_total = $order->amount;
            $pedido->costo_envio = $costo;
            $pedido->telefono = $request->telefono;
            /*Atributos de oxxo*/
            $pedido->status = $oxxo ? 'pending_payment' : 'paid';
            $pedido->tipo_orden = $oxxo ? 'oxxo' : 'card';
            $pedido->num_referencia = $oxxo ? $order->charges[0]->payment_method->reference : '';
            $pedido->recibidor = $request->tipo_envio != 4 ? $direccion['recibidor'] : "Belyapp Joyería";
            $pedido->calle = $request->tipo_envio != 4 ? $direccion['calle'] : "Paseo Hospicio San Juan de Dios";
            $pedido->num_ext = $request->tipo_envio != 4 ? $direccion['num_ext'] : "22";
            $pedido->num_int = $request->tipo_envio != 4 ? $direccion['num_int'] : "";
            $pedido->ciudad = $request->tipo_envio != 4 ? $direccion['ciudad'] : "Guadalajara";
            $pedido->estado = $request->tipo_envio != 4 ? $direccion['estado'] : "Jalisco";
            $pedido->pais = 'MX';
            $pedido->codigo_postal = $request->tipo_envio != 4 ? $direccion['codigo_postal'] : "44360";
            $pedido->tipo_envio = $request->tipo_envio;
            $pedido->created_at = date("Y-m-d H:i:s");
            
            $pedido->save();

            $this->cambiar_stock_productos($request->productos);
            $this->enviar_correos_pedidos($request->empresa_id);
            $this->guardar_pedido($pedido->id, $request->productos);

            if ($oxxo) {
                $referencia = $order->charges[0]->payment_method->reference;
                $total = $order->amount/100;
                $moneda = $order->currency;
                $this->enviar_correo_referencia_oxxo($referencia, $total, $moneda, $pedido->correo_cliente, $pedido);
                return [
                    'msg' => 'Cargo oxxo solicitado', 
                    'num_referencia' => $order->charges[0]->payment_method->reference, 
                    'total_to_pay' => "$". $order->amount/100 . $order->currency
                ];

            } else {
                return ['msg' => 'Cargo realizado'];
            }
            
        } catch (\Conekta\ErrorList $errorList) {
            $msg_errors = '';
            
            foreach($errorList->details as &$errorDetail) {
                $msg_errors .= $errorDetail->getMessage();
                //echo $errorDetail->getMessage() . "\r\n";
            }
            return ['msg' => 'Cargo no realizado: '.$msg_errors];
        }
    }//End function

    /**
     * Valida que el costo total de la compra sea de al menos 3000 si se trata de la primera compra del usuario.
     *
     * @param  $req
     */
    public function verificar_compra_minima($req)
    {
        $num_pedidos = Pedidos::where('correo_cliente', $req->correo)->count();
        $total_productos = 0;
        foreach ($req->productos as $producto) {
            $total_productos += ($producto['unit_price'] * $producto['quantity']);
        }

        if ($num_pedidos == 0 && $total_productos < 300000) {
            return false;
        }
        return true;
    }

    /**
     * Valida el costo de envío, si está activado o si el total de compra supera la tarífa mínima de envío.
     *
     * @param  int $empresa_id
     */
    public function validar_costo_envio($empresa_id, $productos, $tipo_envio)
    {
        $costo = 0;
        if ($tipo_envio == 3 || $tipo_envio == 4) {
            $costo = 0;
            return $costo;
        }
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $envio = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $empresa_id)->first();

        if (!$envio) {//Verifica que si haya información de envío de la empresa, si no la hay regresa un costo de 0 pesos
            return $costo;
        } else {
            if ($envio['envio_gratuito'] == 0) {//No hay envio gratuito, pero se debe verificar el monto para saber si cobrar o no el envío
                $total_productos = 0;
                foreach ($productos as $key => $producto) {
                    $total_productos += ($producto['unit_price'] * $producto['quantity']);
                }
                if ($total_productos >= ($envio['monto_minimo_envio'] * 100)) {//Aquí iría 0, ya que el monto mínimo para el envío gratuito es mayor o igual
                    $costo = 0;            
                } else {//Aquí va la tarifa de envio.
                    $costo = $envio['tarifa_envio'] * 100;
                }
            } else {//Si hay envío gratuito, por lo que no es necesario verificar el monto mínimo.
                $costo = 0;
            }
        }
        return $costo;
    }

    /**
     * Cambia el número de stock de los productos
     * 
     * @param  json $productos
     */
    public function cambiar_stock_productos($productos)
    {
        foreach ($productos as $key => $producto) {
            DB::table('producto')->where('codigo', $producto['sku'])->decrement('stock', $producto['quantity']);
        }
    }

    /**
     * Verifica que haya suficiente stock para comprar los productos.
     *
     * @param  json $productos
     */
    public function verificar_stock_productos($productos)
    {
        $items_invalidos = array();
        foreach ($productos as $key => $producto) {
            $check = DB::table('producto')->where('codigo', $producto['sku'])->pluck('stock');
            if ($check < $producto['quantity']) {
                array_push($items_invalidos, ['name' => $producto['name'], 'quantity' => $check, 'sku' => $producto['sku']]);   
            }
        }
        return $items_invalidos;
    }

    /**
     * Obtiene todas las preguntas frecuentes de la aplicación.
     * 
     */
    public function obtener_preguntas_frecuentes()
    {
        return DB::table('preguntas_frecuentes')->get();
    }

    /**
     * Regresa todos los pedidos de un usuario.
     *
     * @return $pedidos
     */
    public function obtener_pedidos_usuario(Request $request)
    {
        $customer_id_conekta = Pedidos::obtener_id_conekta_usuario($request->usuario_id);
        $pedidos = Pedidos::obtener_pedidos_usuario($customer_id_conekta);
        foreach ($pedidos as $pedido) {
            $pedido->pedido_detalles;
        }
        return $pedidos;
    }

    /**
     * Regresa todas las cotizaciones solicitadas de un usuario.
     *
     * @return $pedidos
     */
    public function obtener_cotizaciones_usuario(Request $request)
    {
        $cotizaciones = Cotizaciones::obtener_cotizaciones_usuario($request->usuario_id);
        foreach ($cotizaciones as $cotizacion) {
            $cotizacion->cotizaciones_detalles;
            $cotizacion->direccion = DB::table('direccion_usuario')->where('id', $cotizacion->direccion_id)->get();
        }
        return $cotizaciones;
    }

    /**
     * Obtiene la información de envío de la empresa.
     * 
     */
    public function informacion_envio()
    {
        $empresas = DB::table('empresa')->get();
        foreach ($empresas as $empresa) {
            $envio = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $empresa->id)->first();
            $empresa->info_envio = $envio;
        }
        return $empresas;
    }

    /**
     * Envía correos que notifican de una compra exitosa a la empresa que se dio el pedido.
     * 
     */
    public function enviar_correos_pedidos($empresa_id)
    {
        $enviado = false;
        $msg = "Se ha realizado una nueva compra, porfavor, vaya al panel de administración de conekta".
        "\no al módulo de pedidos en su panel administrativo de la aplicación para ver los detalles de la compra";
        $subject = "Nueva compra realizada.";
        $to = "";
        $cc = "";

        if ($empresa_id == 1) {
            $to = "marcosalfaro@gmail.com";
            $enviado = Mail::raw($msg, function($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
        } else if ($empresa_id == 2) {
            $subject = "Nueva cotización solicitada.";
            $msg = "Se ha solicitado una nueva cotización, porfavor, vaya al módulo de cotizaciones en el panel ".
            "\n administrativo de la aplicación para ver los detalles de la cotización.";
            $to = "gdlboxcel@gmail.com";
            $cc = "marcosalfaro@gmail.com";
            $enviado = Mail::raw($msg, function($message) use ($to, $subject, $cc) {
                $message->to($to)->cc($cc)->subject($subject);
            });
        } else if ($empresa_id == 3) {
            $to = "palomaarroyo999@gmail.com";
            $cc = "marcosalfaro@gmail.com";
            $enviado = Mail::raw($msg, function($message) use ($to, $subject, $cc) {
                $message->to($to)->cc($cc)->subject($subject);
            });
        }

        if ($enviado) {
            return ['msg'=>'Enviado'];
        }
        return ['msg' => 'Error enviando el mensaje'];
    }

    /**
     * Guarda los detalles de una orden.
     * 
     */
    public function guardar_pedido($pedido_id, $productos)
    {
        date_default_timezone_set('America/Mexico_City');//Esto fue puesto para obtener corectamente la hora en local, remover si es necesario
        foreach ($productos as $producto) {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $producto_detalle = DB::table('producto')->where('nombre', $producto['name'])->where('codigo', $producto['sku'])->first();
            $item = New PedidoDetalles;
            $item->pedido_id = $pedido_id;
            $item->nombre_producto = $producto['name'];
            $item->medida = $producto['medida'];
            $item->foto_producto = $producto_detalle['foto_producto'];
            $item->precio = $producto['unit_price'];
            $item->cantidad = $producto['quantity'];
            $item->codigo = $producto['sku'];
            $item->created_at = date('Y-m-d H:i:s');
            $item->save();
        }
    }

    /**
     * Busca si existe un usuario con un customer_id_conekta en la base de datos, si lo encuentra actualiza su método de pago
     * Caso contrario, se crea un cliente con la información del request.
     * Después, se crea la orden con los datos del request llamando la función procesar_orden()
     *
     * @param  Request $request
     * @return Retorna ['msg' => 'Cargo realizado'] en caso de que se haya aprobado el cargo
     *         Caso contrario, regresará errores de conekta
     */
    public function crear_cliente_oxxo(Request $request)
    {
        $direccion = usuariosModel::direccion_usuario($request->direccion_id);
        if(!$direccion && $request->tipo_envio != 4) {//Si no hay una dirección de envío se cancela
            return ['msg' => 'No se agregó ninguna dirección de envío.'];
        }
        $direccion_num = $direccion['calle']. " No. Ext: ". $direccion['num_ext'];
        $direccion_num = $direccion['num_int'] ? $direccion_num. " No. Int: ". $direccion['num_int'] : $direccion_num;

        $invalidos = $this->verificar_stock_productos($request->productos);
        if ($invalidos) {//Si hay productos inválidos se cancela
            return $invalidos;
        }

        $customer_id_conekta = usuariosModel::buscar_id_conekta_usuario_app($request->correo);
        if ($customer_id_conekta) {//Se registrará una tarjeta nuevamente para el usuario
           /* $customer = \Conekta\Customer::find($customer_id_conekta);

            if (count($customer['payment_sources'])) {//Si tiene algún método de pago extra, entonces que se elimine y se crea uno nuevo
                $customer->payment_sources[0]->delete();
            }
            $customer = \Conekta\Customer::find($customer_id_conekta);//Se tiene que volver a buscar
            $source = $customer->createPaymentSource(array(
                'token_id' => $request->conektaTokenId,
                'type'     => 'card'
            ));
            
            $customer = \Conekta\Customer::find($customer_id_conekta);*/
            $response = $this->procesar_orden($request, $customer_id_conekta, $direccion, true);
            return $response;

        } else {
            try {
                $cliente = \Conekta\Customer::create(
                    array(
                        "name" => $request->nombre,
                        "email" => $request->correo,
                        "phone" => $request->telefono,
                        'shipping_contacts' => array(array(
                            'phone' => $request->telefono,
                            'receiver' => $request->tipo_envio != 4 ? $direccion['recibidor'] : "Belyapp Joyería",  
                            'address' => array(
                                'street1' => $request->tipo_envio != 4 ? $direccion_num : "Paseo Hospicio #22 San Juan de Dios",
                                'city' => $request->tipo_envio != 4 ? $direccion['ciudad'] : "Guadalajara",
                                'state' => $request->tipo_envio != 4 ? $direccion['estado'] : "Jalisco",
                                'country' => $request->tipo_envio != 4 ? $direccion['pais'] : "Mx",
                                'postal_code' => $request->tipo_envio != 4 ? $direccion['codigo_postal'] : "44360",
                                'residential' => true
                            )
                        ))
                    )//customer
                );

                usuariosModel::actualizar_id_conekta_usuario_app($request->correo, $cliente['id']);
                $customer = \Conekta\Customer::find($cliente->id);
                $response = $this->procesar_orden($request, $cliente->id, $direccion, true);

                return $response;
                
            } catch (\Conekta\ErrorList $errorList) {
                $msg_errors = '';
                foreach ($errorList->details as &$errorDetail) {
                    $msg_errors .= $errorDetail->getMessage();
                    //echo $errorDetail->getMessage();
                }
                return ['msg' => 'Datos del cliente incorrectos: '.$msg_errors];
            }
        }
        
    }

    /**
     *==================================================================================================================================
     *=                                    Finalizan las funciones relacionadas a la api de conekta                                    =
     *==================================================================================================================================
     */

    /**
     *==================================================================================================================================
     *=                                     Empiezan las funciones relacionadas a las cotizaciones                                     =
     *==================================================================================================================================
     */

    /**
     * Guarda una cotización
     * 
     */
    public function guardar_cotizacion(Request $request)
    {
        $total_productos = 0;
        foreach ($request->productos as $producto) {
            $total_productos += ($producto['unit_price'] * $producto['quantity']);
        }
        $cotizacion = New Cotizaciones;
        $cotizacion->usuario_id = $request->usuario_id;
        $cotizacion->direccion_id = $request->direccion_id;
        $cotizacion->status = 0;
        $cotizacion->costo_cotizacion = $total_productos;
        $cotizacion->created_at = date('Y-m-d H:i:s');
        $cotizacion->save();

        foreach ($request->productos as $producto) {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $producto_detalle = DB::table('producto')->where('nombre', $producto['name'])->where('codigo', $producto['sku'])->first();
            $item = New CotizacionesDetalles;
            $item->cotizaciones_id = $cotizacion->id;
            $item->nombre_producto = $producto['name'];
            $item->foto_producto = $producto_detalle['foto_producto'];
            $item->precio = ($producto['unit_price']/100);
            $item->cantidad = $producto['quantity'];
            $item->codigo = $producto['sku'];
            $item->created_at = date('Y-m-d H:i:s');
            $item->save();
        }

        return 1;
    }

    /**
     * Envía correos con los detalles de un pedido al correo de un usuario.
     * 
     */
    public function enviar_correo_detalle_orden(Request $req)
    {
        $id = DB::table('pedidos')->where('id', $req->pedido_id)->pluck('conekta_order_id');
        $orden = DB::table('pedidos')->where('id', $req->pedido_id)->first();
        $productos = DB::table('pedido_detalles')->where('pedido_id', $req->pedido_id)->get();
        $orden_conekta = \Conekta\Order::find($id);
        $total = 0;

        $nombre_cliente = $orden_conekta->customer_info['name'];
        $email_cliente = $orden_conekta->customer_info['email'];
        $telefono_cliente = $orden_conekta->customer_info['phone'];
        $enviado = false;
        $subject = "Detalles de su orden";
        $to = $req->email;
        $msg = "<h3>A continuación se muestran los detalles de su orden</h3>";

        $msg .= "<div><p style='font-weight: bold;'>Nombre cliente: <span style='font-weight: normal'>$nombre_cliente</span></p></div>".
                "<div><p style='font-weight: bold;'>Email cliente: <span style='font-weight: normal'>$email_cliente</span></p></div>".
                "<div><p style='font-weight: bold;'>Teléfono cliente: <span style='font-weight: normal'>$telefono_cliente</span></p></div>";

        $recibidor = $orden->recibidor;
        $guia = $orden->num_seguimiento;
        $calle = $orden->calle;
        $estado = $orden->estado;
        $ciudad = $orden->ciudad;
        $cp = $orden->codigo_postal;
        $costo_envio = $orden->costo_envio/100;
        $costo_total = $orden->costo_total/100;
        $msg .= "<br><h3>Información de envío: </h3>".
                "<div><p style='font-weight: bold;'>Persona que recibirá el pedido: <span style='font-weight: normal'>$recibidor</span></p></div>".
                "<div><p style='font-weight: bold;'>Número de guía: <span style='font-weight: normal'>$guia</span></p></div>".
                "<div><p style='font-weight: bold;'>Costo envío: <span style='font-weight: normal'>$$costo_envio</span></p></div>".
                "<div><p style='font-weight: bold;'>Dirección: <span style='font-weight: normal'>$calle</span></p></div>".
                "<div><p style='font-weight: bold;'>Código postal: <span style='font-weight: normal'>$cp</span></p></div>".
                "<div><p style='font-weight: bold;'>País: <span style='font-weight: normal'>México</span></p></div>".
                "<div><p style='font-weight: bold;'>Estado: <span style='font-weight: normal'>$estado</span></p></div>".
                "<div><p style='font-weight: bold;'>Ciudad: <span style='font-weight: normal'>$ciudad</span></p></div>";

        $msg .= "<br><h3>Productos encargados: </h3>";
        foreach ($productos as $producto) {
            $src = 'https://belyapp.com/'.$producto->foto_producto;
            $nombre_producto = $producto->nombre_producto;
            $cantidad = $producto->cantidad;
            $precio = $producto->precio/100;
            $msg .= "<div>$nombre_producto $$precio (x$cantidad)</div>".
                    "<br><div><img width='150px;' height='150px;' src=$src></div>";
        }

        $msg .= "<br><div>Costo total: $$costo_total</div>";

        $enviado = Mail::send([], [], function ($message) use($to, $subject, $msg) {
            $message->to($to)
            ->subject($subject)
            ->setBody($msg, 'text/html'); // for HTML rich messages
        });

        if ($enviado) {
            return ['msg'=>'Enviado'];
        }
        return ['msg' => 'Error enviando el mensaje'];
    }

    /**
     * Envía correos con los detalles de una cotización al correo de un usuario.
     * 
     */
    public function enviar_correo_detalle_cotizacion(Request $req)
    {
        $cotizacion = DB::table('cotizaciones')->where('id', $req->cotizacion_id)->first();
        $usuario = DB::table('usuario')->where('id', $cotizacion->usuario_id)->first();
        $direccion = DB::table('direccion_usuario')->where('id', $cotizacion->direccion_id)->first();
        $productos = DB::table('cotizaciones_detalles')->where('cotizaciones_id', $req->cotizacion_id)->get();
        $total = 0;
        $enviado = false;
        $subject = "Detalles de su cotizacion";
        $to = $req->email;
        $msg = "<h3>A continuación se muestran los detalles de su cotizacion</h3>";
        $nombre_cliente = $usuario->nombre;
        $apellido_cliente = $usuario->apellido;
        $email_cliente = $usuario->correo;
        $telefono_cliente = $usuario->telefono;

        $msg .= "<div><p style='font-weight: bold;'>Nombre cliente: <span style='font-weight: normal'>$nombre_cliente </span></p></div>".
                "<div><p style='font-weight: bold;'>Apellido cliente: <span style='font-weight: normal'>$apellido_cliente</span></p></div>".
                "<div><p style='font-weight: bold;'>Email cliente: <span style='font-weight: normal'>$email_cliente</span></p></div>".
                "<div><p style='font-weight: bold;'>Teléfono cliente: <span style='font-weight: normal'>$telefono_cliente</span></p></div>";

        $recibidor = $direccion->recibidor;
        $direccion_num = $direccion->calle. " No. Ext: ". $direccion->num_ext;
        $direccion_num = $direccion->num_int ? $direccion_num. " No. Int: ". $direccion->num_int : $direccion_num;
        $recibidor = $direccion->recibidor;
        $pais = "México";
        $estado = $direccion->estado;
        $ciudad = $direccion->ciudad;
        $codigo_postal = $direccion->codigo_postal;

        $msg .= "<br><h3>Información de envío: </h3>".
                "<div><p style='font-weight: bold;'>Persona que recibirá el pedido: <span style='font-weight: normal'>$recibidor</span></p></div>".
                "<div><p style='font-weight: bold;'>Direccion: <span style='font-weight: normal'>$direccion_num</span></p></div>".
                "<div><p style='font-weight: bold;'>País: <span style='font-weight: normal'>$pais</span></p></div>".
                "<div><p style='font-weight: bold;'>Estado: <span style='font-weight: normal'>$estado</span></p></div>".
                "<div><p style='font-weight: bold;'>Ciudad: <span style='font-weight: normal'>$ciudad</span></p></div>";
                "<div><p style='font-weight: bold;'>Código postal: <span style='font-weight: normal'>$codigo_postal</span></p></div>";

        $msg .= "<br><h3>Productos encargados: </h3>";
        foreach ($productos as $producto) {
            $src = 'https://belyapp.com/'.$producto->foto_producto;
            $nombre_producto = $producto->nombre_producto;
            $cantidad = $producto->cantidad;
            $precio = $producto->precio;
            $msg .= "<div>$nombre_producto $$precio (x$cantidad)</div>".
                    "<br><div><img width='150px;' height='150px;' src=$src></div>";
        }

        $costo_total = $cotizacion->costo_cotizacion/100;
        $msg .= "<br><div>Costo total: $$costo_total</div>";

        $enviado = Mail::send([], [], function ($message) use($to, $subject, $msg) {
            $message->to($to)
            ->subject($subject)
            ->setBody($msg, 'text/html'); // for HTML rich messages
        });

        if ($enviado) {
            return ['msg'=>'Enviado'];
        }
        return ['msg' => 'Error enviando el mensaje'];
    }

    /**
     * Envía un correo con el número de referencia
     * 
     */
    public function enviar_correo_referencia_oxxo($referencia, $total, $moneda, $correo_cliente, $pedido)
    {
        Mail::send('emails.oxxo', ['total' => $total, 'referencia' => $referencia, 'pedido' => $pedido], function ($message)  use ($correo_cliente)
        {
            $message->to($correo_cliente);
            $message->subject('Bely | Número de referencia OXXO');
        });
    }

    /**
     * Envía correos con los detalles de una cotización al correo de un usuario.
     * 
     */
    public function enviar_num_referencia_correo($num_referencia, $monto)
    {
        $msg .= "<div><p style='font-weight: bold;'>Nombre cliente: <span style='font-weight: normal'>$nombre_cliente </span></p></div>".
                "<div><p style='font-weight: bold;'>Apellido cliente: <span style='font-weight: normal'>$apellido_cliente</span></p></div>".
                "<div><p style='font-weight: bold;'>Email cliente: <span style='font-weight: normal'>$email_cliente</span></p></div>".
                "<div><p style='font-weight: bold;'>Teléfono cliente: <span style='font-weight: normal'>$telefono_cliente</span></p></div>";

        $recibidor = $direccion->recibidor;
        $direccion_num = $direccion->calle. " No. Ext: ". $direccion->num_ext;
        $direccion_num = $direccion->num_int ? $direccion_num. " No. Int: ". $direccion->num_int : $direccion_num;
        $recibidor = $direccion->recibidor;
        $pais = "México";
        $estado = $direccion->estado;
        $ciudad = $direccion->ciudad;
        $codigo_postal = $direccion->codigo_postal;

        $msg .= "<br><h3>Información de envío: </h3>".
                "<div><p style='font-weight: bold;'>Persona que recibirá el pedido: <span style='font-weight: normal'>$recibidor</span></p></div>".
                "<div><p style='font-weight: bold;'>Direccion: <span style='font-weight: normal'>$direccion_num</span></p></div>".
                "<div><p style='font-weight: bold;'>País: <span style='font-weight: normal'>$pais</span></p></div>".
                "<div><p style='font-weight: bold;'>Estado: <span style='font-weight: normal'>$estado</span></p></div>".
                "<div><p style='font-weight: bold;'>Ciudad: <span style='font-weight: normal'>$ciudad</span></p></div>";
                "<div><p style='font-weight: bold;'>Código postal: <span style='font-weight: normal'>$codigo_postal</span></p></div>";

        $msg .= "<br><h3>Productos encargados: </h3>";
        foreach ($productos as $producto) {
            $src = 'https://belyapp.com/'.$producto->foto_producto;
            $nombre_producto = $producto->nombre_producto;
            $cantidad = $producto->cantidad;
            $precio = $producto->precio;
            $msg .= "<div>$nombre_producto $$precio (x$cantidad)</div>".
                    "<br><div><img width='150px;' height='150px;' src=$src></div>";
        }

        $costo_total = $cotizacion->costo_cotizacion/100;
        $msg .= "<br><div>Costo total: $$costo_total</div>";

        $enviado = Mail::send([], [], function ($message) use($to, $subject, $msg) {
            $message->to($to)
            ->subject($subject)
            ->setBody($msg, 'text/html'); // for HTML rich messages
        });
    }

    /**
     * Actualiza el player_id de un usuario
     * 
     * @return json
     */
    public function actualizar_player_id(Request $req)
    {
        $user = usuariosModel::find($req->usuario_id);
        $user->player_id = $req->player_id;
        $user->save();

        return response(['msg' => 'Player ID modificado con éxito'], 200);
    }

    /**
    * Envía una notificación individual a un usuario que puede ser repartidor o cliente
    * @return $response
    */
    public function enviar_notificacion_individual($title, $mensaje, $data, $player_ids)
    {
        $content = array(
            "en" => $mensaje
        );
        $header = array(
            "en" => $title
        );
        
        $fields = array(
            'app_id' => $this->app_id,
            'include_player_ids' => $player_ids,
            'data' => $data,
            'headings' => $header,
            'contents' => $content,
            'small_icon' => $this->small_icon,
            'large_icon' => $this->regular_icon
        );
        
        
        $fields = json_encode($fields);
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   "Authorization: Basic $this->app_key"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
    }

    public function webhook_conekta()
    {
        $body = @file_get_contents('php://input');
        $data = json_decode($body);
        http_response_code(200); // Return 200 OK

        /*DB::table('rows')->insert([
            'content' => json_encode($data),
        ]);*/

        $payment = $data->data->object->object;
        $type = $data->type;

        if ($data->data->object->object == "charge") {//Se verifica que sea un cargo.

            if ($type == 'charge.paid') {//Se verifica que sea un cargo pagado
                $order_id = $data->data->object->order_id;
                Pedidos::where('conekta_order_id', $order_id)->update(['status' => 'paid']);
                $servicio = Pedidos::where('conekta_order_id', $order_id)->first();
                
                if ($servicio) {
                    if ($servicio->tipo_orden == 'oxxo') {//Se manda la notificación por onesignal
                        $player_id [] = usuariosModel::where('correo', $servicio->correo_cliente)->first()->player_id;
                        $titulo = '¡Pago por oxxo exitoso!';
                        $mensaje = "Gracias por pagar en tiempo y forma su pedido solicitado por OXXO pay. Pronto se le asignará un número de guía para que pueda recibir satisfactoriamente su pedido.";
                        $data = array('msg' => 'Pedido por OXXO pay pagado');
                        
                        //app('App\Http\Controllers\dataAppController')->enviar_notificacion_individual($titulo, $mensaje, $data, $player_id);
                    }
                    
                    $to = $servicio->correo_cliente;
                    $monto = $servicio->costo_total / 100;
                    $nombre_cliente = $servicio->nombre_cliente;

                    $subject = "Bely | Confirmación de pago";

                    Mail::send('emails.pago_confirmado', ['nombre_cliente' => $nombre_cliente, 'monto' => $monto, 'pedido' => $servicio], function ($message)  use ($to, $subject)
                    {
                        $message->to($to);
                        $message->subject($subject);
                    });

                    return response(['msg' => 'Notificado'], 200);
                }//If para verificar que exista una orden con dicho order_id
                return response(['msg' => 'Cargo pagado recibido'], 200);
            }//If para verificar status del pago
        }//If que verifica tipo de pago
    }
}
