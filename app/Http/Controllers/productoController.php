<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use Redirect;
use App\Producto;
use App\ProductoMedidas;
use Image;
use Input;

class productoController extends Controller
{
    /**
     * Carga la tabla de productos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $title = "Productos";
            $menu = "Productos";
            $categorias = DB::table('categoria')->where('empresa_id', Auth::user()->empresa_id)->get();
            $productos = DB::table('producto')->where('empresa_id', Auth::user()->empresa_id)->get();
            return view('productos.productos', ['menu' => $menu, 'productos' => $productos, 'categorias' => $categorias, 'title' => $title]);
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Carga las subcategorías de una categoría.
     *
     * @return \Illuminate\Http\Response
     */
    public function cargar_subcategorias(Request $request)
    {
        $subcategorias = DB::table('subcategoria')->where('categoria_id', $request->categoria_id)
        ->get();
        return $subcategorias;
    }

    /**
     * Guarda un producto nuevo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect /productos
     */    
    public function guardar_producto(Request $request)
    {
        $folder = Producto::define_folder(true);
        $name = "img/img_productos/".$folder."default.jpg";
        if ($request->file('foto_producto')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png", "4"=>"gif");
            $file = Input::file('foto_producto');
            $extension_archivo = $file->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/img_productos/'.$folder.$file->getClientOriginalName();
                $imagen_producto = Image::make($request->file('foto_producto'))
                ->resize(460, 460)
                ->save($name);
            }
        }

        $producto = new Producto;
        
        $producto->codigo = $request->codigo;
        $producto->sku = $request->sku;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $producto->foto_producto = $name;
        $producto->categoria_id = $request->categoria_id;
        $producto->subcategoria_id = $request->subcategoria_id;
        $producto->empresa_id = Auth::user()->empresa_id;
        $producto->oferta = $request->oferta == "on" ? '1' : '0';
        $producto->medida = $request->medida;

        $producto->save();

        /*if ($request->medida) {
            foreach ($request->medida as $key => $val) {
                $medida = New ProductoMedidas;
                $medida->producto_id = $request->id;
                $medida->medida = $val;
                $medida->save();
            }
        }*/

        return back();
    }

    /**
     * Edita un producto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect /productos
     */
    public function editar_producto(Request $request)
    {
        $folder = Producto::define_folder(true);
        $name = "img/img_productos/".$folder."default.jpg";
        if ($request->file('foto_producto')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png", "4"=>"gif");
            $file = Input::file('foto_producto');
            $extension_archivo = $file->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/img_productos/'.$folder.$file->getClientOriginalName();
                $imagen_producto = Image::make($request->file('foto_producto'))
                ->resize(460, 460)
                ->save($name);
            }
        }

        $producto = Producto::find($request->id);
        
        $producto->codigo = $request->codigo;
        $producto->sku = $request->sku;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;
        $producto->stock = $request->stock;
        $name != "img/img_productos/".$folder."default.jpg" ? $producto->foto_producto = $name : '';
        $producto->categoria_id = $request->categoria_id;
        $producto->subcategoria_id = $request->subcategoria_id;
        $producto->oferta = $request->oferta == "on" ? '1' : '0';
        $producto->medida = $request->medida;

        $producto->save();

        /*if ($request->medida) {
            foreach ($request->medida as $key => $val) {
                $medida = New ProductoMedidas;
                $medida->producto_id = $request->id;
                $medida->medida = $val;
                $medida->save();
            }
        }*/
        return back();
    }

    /**
     * Elimina un producto.
     *
     * @param  \Illuminate\Http\Request $request
     * @return ["success" => true]
     */
    public function eliminar_producto(Request $request)
    {
        try {
            $producto = Producto::find($request->id);
            $producto->delete();
            return ["success" => true];
        } catch(\Illuminate\Database\QueryException $ex) {
            return $ex->getMessage();
        }
    }
}
