<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;

class quienes_somosModel extends Model
{
    /**
     * Define el nombre de la tabla del modelo.
     */
    protected $table = 'quienes_somos';

    /**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['nombrePDF', 'linkVideo', 'imagen'];

    /**
     * Obtiene la información sobre quienes somos.
     *
     * @return 
     */
    public static function obtener_quienes_somos()
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
    	return DB::table('quienes_somos')
        ->first();
    }
}
