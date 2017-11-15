<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class direccionesUsuarioModel extends Model
{
	/**
     * Define el nombre de la tabla del modelo.
     */
	protected $table = 'direccion_usuario';

	/**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['usuario_id', 'recibidor', 'calle', 'num_ext', 'num_int', 'estado', 'ciudad', 'pais', 'codigo_postal', 'residencial'];
}
