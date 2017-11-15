<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;

class usuariosModel extends Model
{
    /**
     * Define el nombre de la tabla del modelo.
     */
    protected $table = 'usuario';

    /**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['password', 'nombre', 'apellido', 'correo', 'tarjeta', 'fechaRegistro', 'foto_perfil', 'estado', 'genero_id', 'fechaNacimiento', 'telefono', 'celular', 'customer_id_conekta', 'status'];

    public static function buscar_usuario_por_correo($correo)
    {
    	return DB::table('usuario')
        ->where('correo', '=', $correo)
        ->get();
    }

    public static function buscar_id_conekta_usuario_app($correo)
    {
    	return DB::table('usuario')
        ->where('correo', '=', $correo)
        ->pluck('customer_id_conekta');
    }

    public static function actualizar_id_conekta_usuario_app($correo, $customer_id_conekta)
    {
    	return DB::table('usuario')
        ->where('correo', '=', $correo)
        ->update(['customer_id_conekta' => $customer_id_conekta]);
    }

    /**
     *
     * @return Regresa el total de usuarios registrados y activos en la aplicación filtrados por empresa
     */
    public static function total_app_users()
    {
        return usuariosModel::where('status', '!=', 2)->count();
    }

    /**
     *
     * @return Regresa el total de usuarios registrados y bloqueados en la aplicación filtrados por empresa
     */
    public static function count_banned_app_users()
    {
        return usuariosModel::where('status', 0)->count();
    }

    /**
     *
     * @return Regresa los datos de una de las direcciones del usuario
     */
    public static function direccion_usuario($id)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);

        return DB::table('direccion_usuario')->where('id', $id)->first();
    }
}
