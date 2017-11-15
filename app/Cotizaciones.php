<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Cotizaciones extends Model
{
    /**
     * Define el nombre de la tabla del modelo.
     */
	protected $table = 'cotizaciones';

    /**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['usuario_id', 'direccion_id','status', 'costo_cotizacion','created_at'];

    /**
     * Obtiene los productos de una cotización.
     */
    public function cotizaciones_detalles()
    {
        return $this->hasMany(CotizacionesDetalles::class);
    }

    /**
     * Obtiene todas las cotizaciones.
     */
    public static function obtener_cotizaciones()
    {
    	return Cotizaciones::select(DB::raw('cotizaciones.id, cotizaciones.status, cotizaciones.created_at, usuario.nombre, usuario.apellido'))
		->leftJoin('usuario', 'cotizaciones.usuario_id', '=', 'usuario.id')
		->get();
    }

    /**
     * Obtiene los detalles de una cotización.
     */
    public static function ver_cotizacion_detalles($cotizacion_id)
    {
    	$cotizaciones = Cotizaciones::select(DB::raw('cotizaciones.id, cotizaciones.status, 
        cotizaciones.created_at, usuario.id as usuario_id, usuario.nombre, usuario.apellido, usuario.correo, usuario.telefono'))
		->leftJoin('usuario', 'cotizaciones.usuario_id', '=', 'usuario.id')
		->where('cotizaciones.id', $cotizacion_id)
		->first();

    	$cotizaciones->direccion_user = DB::table('direccion_usuario')
    	->where('usuario_id', $cotizaciones->usuario_id)
    	->get();

        $cotizaciones->direccion_user = DB::table('direccion_usuario')
        ->where('usuario_id', $cotizaciones->usuario_id)
        ->get();

    	return $cotizaciones;
    }

    /**
     * Finaliza una cotización.
     */
    public static function finalizar_cotizacion($cotizacion_id)
    {
        return DB::table('cotizaciones')
        ->where('id', $cotizacion_id)
        ->update(['status' => 1]);
    }

    /**
     *
     * @return Regresa el total de cotizaciones
     */
    public static function total_cotizaciones()
    {
        return Cotizaciones::count();
    }

    /**
     *
     * @return Regresa el total de cotizaciones atentidas
     */
    public static function total_cotizaciones_atendidas()
    {
        return Cotizaciones::where('status', 1)->count();
    }

    /**
     *
     * @return Regresa las cotizaciones solicitadas de un usuario
     */
    public static function obtener_cotizaciones_usuario($usuario_id)
    {
        return Cotizaciones::where('usuario_id', $usuario_id)->get();
    }
}
