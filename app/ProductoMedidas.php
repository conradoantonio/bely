<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductoMedidas extends Model
{
	/**
     * Define el nombre de la tabla del modelo.
     */
    protected $table = 'producto_medidas';

    /**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['producto_id', 'medida'];
}
