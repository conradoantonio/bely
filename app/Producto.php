<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class producto extends Model
{
	/**
     * Define el nombre de la tabla del modelo.
     */
    protected $table = 'producto';

    /**
     * Define el nombre de los campos que podrán ser alterados de la tabla del modelo.
     */
    protected $fillable = ['codigo', 'sku', 'nombre', 'descripcion', 'precio', 'stock','tipo_producto', 'foto_producto', 'empresa_id', 'categoria_id', 'subcategoria_id', 'oferta', 'medida'];

    /**
     * Obtiene las categorías de una empresa.
     *
     * @param bool $empresa_id
     * @return $categorias
     */
    public static function categorias_empresa($empresa_id)
    {
         return DB::table('categoria')
        ->where('empresa_id', '=', $empresa_id)
        ->get();
    }

    /**
     * Define la carpeta donde se guardará una imagen de un producto.
     *
     * @param bool $slash
     * @return $folder
     */
    public static function define_folder($slash)
    {
    	$folder = (auth()->user()->empresa_id == 1 ? 'bely' : (auth()->user()->empresa_id == 2 ? 'gdlbox' : (auth()->user()->empresa_id == 3 ? 'cosmeticos' : 'error')));
        $folder = $slash == false ? $folder : $folder.'/';
        return $folder;
    }
}
