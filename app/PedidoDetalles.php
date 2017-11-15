<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoDetalles extends Model
{
	/**
     * Define el nombre de la tabla del modelo.
     */
	protected $table = 'pedido_detalles';

	/**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['pedido_id', 'nombre_producto', 'foto_producto', 'precio', 'cantidad', 'codigo', 'created_at'];

    /**
     * Obtiene la cotización a la que pertenece el detalle.
     */
    public function pedido()
    {
        return $this->belongsTo(Pedidos::class);
    }
}
