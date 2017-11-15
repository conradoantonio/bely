<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class usuariosSistemaModel extends Model
{
	/**
     * Define el nombre de la tabla del modelo.
     */
    protected $table = 'users';

    /**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['user', 'password', 'nombre', 'apellido', 'email', 'foto_usuario', 'empresa_id'];
}
