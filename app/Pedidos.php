<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Pedidos extends Model
{
    /**
     * Define el nombre de la tabla del modelo.
     */
    protected $table = 'pedidos';

    /**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['nombre_cliente', 'correo_cliente', 'conekta_order_id', 'empresa_id', 'customer_id_conekta', 'num_seguimiento', 'costo_total', 'costo_envio',
    'telefono', 'recibidor', 'calle', 'num_ext', 'num_int', 'ciudad', 'estado', 'pais', 'codigo_postal', 'tipo_envio', 'status', 'num_referencia', 'tipo_orden'];

    /**
     * Obtiene todos los detalles de un pedido.
     *
     */
    public function pedido_detalles()
    {
        return $this->hasMany(PedidoDetalles::class, 'pedido_id');
    }

    /**
     * Obtiene todos los pedidos realizados
     *
     * @return $pedidos
     */
    public static function obtener_pedidos()
    {
    	return Pedidos::where('empresa_id', auth()->user()->empresa_id)
        ->get();
    }

    /**
     * Obtiene todos los pedidos realizados y pagados
     *
     * @return $pedidos
     */
    public static function obtener_pedidos_pagados()
    {
        return Pedidos::where('empresa_id', auth()->user()->empresa_id)
        ->where('status', 'paid')
        ->get();
    }

    /**
     * Actualiza el número de seguimiento (numero de guía) de un pedido
     *
     * @return DB
     */
    public static function asignar_numero_guia($request)
    {
        return DB::table('pedidos')
        ->where('conekta_order_id', $request->orden_id)
        ->update(['num_seguimiento' => $request->numero_guia]);
    }

    /**
     *
     * @return Regresa el total de pedidos filtrados por empresa
     */
    public static function total_orders()
    {
        return Pedidos::where('empresa_id', auth()->user()->empresa_id)->count();
    }

    /**
     *
     * @return Regresa el total de ventas filtrados por empresa
     */
    public static function total_sales()
    {
        return Pedidos::where('empresa_id', auth()->user()->empresa_id)->where('status', 'paid')
        ->sum(DB::raw('costo_total'));
    }

    /**
     *
     * @return Regresa el total de ventas semanales filtrados por empresa
     */
    public static function ventas_semanales()
    {
        return DB::table('pedidos')
        ->select(DB::raw('SUBSTRING_INDEX(created_at, " ", 1) as created_at, SUM(costo_total)/100 AS "Costo_total", 
            MONTH(`created_at`) AS Mes, DAY(`created_at`) AS Dia, COUNT(*) AS Total_Ventas'))
        ->where('created_at', '>=', DB::raw('SUBDATE(CURDATE(),INTERVAL 7 DAY)'))
        ->where('created_at', '<', DB::raw('CURDATE()'))
        ->where('empresa_id', auth()->user()->empresa_id)
        ->where('status', 'paid')
        ->groupBy(DB::raw('DAY(created_at)'))
        ->get();
    }
    
    /**
     *
     * @return Regresa el customer_id_conekta de un usuario
     */
    public static function obtener_id_conekta_usuario($usuario_id)
    {
        return DB::table('usuario')->where('id', $usuario_id)->pluck('customer_id_conekta');
    }

    /**
     *
     * @return Regresa los números de guía de los pedidos de un usuario
     */
    public static function obtener_num_guia_pedido($id_conekta)
    {
        return Pedidos::where('customer_id_conekta', $id_conekta)->get();
    }

    /**
     *
     * @return Regresa los números de guía de los pedidos de un usuario
     */
    public static function obtener_pedidos_usuario($customer_id_conekta)
    {
        return Pedidos::where('customer_id_conekta', $customer_id_conekta)->get();
    }
}
