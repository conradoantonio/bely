<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Pedidos;
use App\PedidoDetalles;
use DB;
use Auth;
use PDO;
use Redirect;
use Mail;

require_once("conekta-php-master/lib/Conekta.php");
\Conekta\Conekta::setApiKey("key_wsnGdPKAe4pyTFhCs84qVw");
\Conekta\Conekta::setApiVersion("2.0.0");

class pedidosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            if (auth()->user()->empresa_id != 2) {//Si está logueado como usuario bely o cosmeticos & co
                $title = "Pedidos";
                $menu = "Pedidos";
                $pedidos = Pedidos::obtener_pedidos_pagados();//Se vuelven a solicitar los pedidos en caso de que se hayan actualizado el status de los pedidos de oxxo
                return view('pedidos.pedidos', ['pedidos' => $pedidos, 'menu' => $menu, 'title' => $title]);
            } else {
                return Redirect::to('/dashboard');
            }   
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Revisa el status de un pedido solicitado a pagar por oxxo pay
     *
     * @return $pedido
     */
    /*public function checar_status_pedido($id, $id_orden_conekta)
    {
        $orden_conekta = \Conekta\Order::find($id_orden_conekta);
        if ($orden_conekta->payment_status == 'paid') {//Se va a actualizar el pedido
            $pedido = Pedidos::find($id);
            
            $pedido->status = 'paid';

            $num_referencia = $orden_conekta->charges[0]->payment_method->reference;
            $monto = $orden_conekta->amount/100 . $orden_conekta->currency;
            $to = $pedido->correo_cliente;
            $subject = "Confirmación de pago de oxxo";
            $msg = "<br><h3>Confirmación de pago de oxxo. </h3>".
                "<div><p>Se le notifica que su pago con el número de referencia $num_referencia por el monto $$monto ha sido registrado en nuestro sistema de forma exitosa</p></div>";
            $enviado = Mail::send([], [], function ($message) use($to, $subject, $msg) {
                $message->to($to)
                ->subject($subject)
                ->setBody($msg, 'text/html'); // for HTML rich messages
            });

            $pedido->save();
        }
    }*/

    /**
     * Obtiene la información de un pedido en específico y su número de guía en caso de que tenga uno.
     *
     * @return $pedidos
     */
    public function obtener_pedido_por_id(Request $request)
    {
        $pedido = Pedidos::where('conekta_order_id', $request->orden_id)->first();
        $pedido->detalles = PedidoDetalles::where('pedido_id', $pedido->id)->get();
        return $pedido;
    }

    /**
     * Actualiza el número de seguimiento (numero de guía) de un pedido.
     *
     * @return Pedidos::asignar_numero_guia($request)
     */
    public function asignar_numero_guia(Request $request)
    {
        Pedidos::asignar_numero_guia($request);
        return $this->enviar_correo_num_guia($request);
    }

    /**
     * Envía un correo con el número de guía asignado recientemente.
     *
     * @return msg
     */
    public function enviar_correo_num_guia($request)
    {
        $numero_guia = $request->numero_guia;
        $pedido_id = $request->orden_id;
        $pedido = DB::table('pedidos')->where('conekta_order_id', $request->orden_id)->first();
        $usuario = DB::table('usuario')->where('customer_id_conekta', $pedido->customer_id_conekta)->first();
        $to = $usuario->correo;
        $enviado = false;
        $subject = "¡Su pedido ya está en camino!";
        $msg = "Se ha asignado un número de guía para su pedido con el número $pedido_id realizado desde la aplicación belyapp".
        "\nNúmero de guía: $numero_guia";
        $enviado = Mail::raw($msg, function($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
        if ($enviado) {
            return ['msg'=>'Enviado'];
        }
        return ['msg' => 'Error enviando el mensaje'];
    }
}
