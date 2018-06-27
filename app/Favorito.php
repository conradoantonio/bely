<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'favoritos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'producto_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Check the rol of the current user.
     *
     */
    static function not_repeat($user_id, $producto_id)
    {
        $query = Favorito::where('user_id', $user_id)
        ->where('producto_id', $producto_id)
        ->get();

        return $query;  
    }

    /**
     * Obtiene los productos favoritos del cliente.
     *
     */
    public function producto()
    {
        return $this->hasOne('App\Producto', 'id', 'producto_id');
    }
}
