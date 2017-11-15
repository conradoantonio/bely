<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel, Input, File;
use DB;
use App\Producto;
use App\usuariosModel;

class ExcelController extends Controller
{
    public function importar_productos()
    {
        if (Input::hasFile('archivo-excel')) {

            //DB::setFetchMode(PDO::FETCH_ASSOC);
            $codigos = DB::table('producto')->lists('codigo');//Arreglo que contiene los códigos de los productos existentes      
            $codigo_array = array();//Arreglo que contendrá los códigos de los productos del EXCEL
            $path = Input::file('archivo-excel')->getRealPath();
            $extension = Input::file('archivo-excel')->getClientOriginalExtension();
            $folder = Producto::define_folder(true);

            if ($extension == 'xlsx' || $extension == 'xls') {
                $data = Excel::load($path, function($reader) {
                    $reader->setDateFormat('Y-m-d');
                })->get();

                if (!empty($data) && $data->count()) {
                    foreach ($data as $key => $value) {
                        //dd($data);
                        if (in_array($value->codigo, $codigos))
                            continue;

                        if (in_array($value->codigo, $codigo_array))
                            continue;

                        if ($value->codigo == null || $value->codigo == "")
                            continue;

                        $categoria_id = DB::table('categoria')->where('categoria', $value->categoria)->pluck('id');
                        if ($value->subcategoria != "" || $value->subcategoria != null) {
                            $subcategoria_id = DB::table('subcategoria')->where('subcategoria', $value->subcategoria)->pluck('id');
                        } else {
                            $subcategoria_id = 0;
                        }
                        $oferta = $value->oferta == 'si' ? 1 : 0;
                        $insert[] = [
                            'codigo' => $value->codigo,
                            'sku' => $value->sku,
                            'nombre' => $value->nombre,
                            'precio' => $value->precio,
                            'stock' => $value->stock,
                            'descripcion' => $value->descripcion,
                            'empresa_id' => auth()->user()->empresa_id,
                            'categoria_id' => $categoria_id,
                            'subcategoria_id' => $subcategoria_id,
                            'oferta' => $oferta,
                            'foto_producto' => 'img/img_productos/'.$folder.$value->foto
                        ];

                        /*Producto::firstOrCreate([
                            'empresa_id' => auth()->user()->empresa_id,
                            'codigo' => $insert['codigo']
                        ], $insert);*/

                        array_push($codigo_array , $value->codigo);
                    }
                    if (!empty($insert)) {
                        DB::table('producto')->insert($insert);
                    }//End insert if
                }//End data count if
            }//End of extension if
        }//End first if
        return back();   
    }

    public function exportar_productos($empresa_id,$fecha_inicio,$fecha_fin)
    {
        $matchThese = array();
        $empresa_id != "" && $empresa_id != 'false' ? $matchThese['producto.empresa_id'] = $empresa_id : '';
        $fecha_inicio != "" && $fecha_inicio != 'false' ? $matchThese['producto.created_at'] = $fecha_inicio : '';
        $fecha_fin != "" && $fecha_fin != 'false' ? $matchThese['created_at'] = $fecha_fin : '';

        $productos = Producto::query()
        ->select(DB::raw("producto.codigo, producto.sku, producto.nombre, producto.precio, producto.stock, producto.descripcion, empresa.nombre as empresa, categoria.categoria, IFNULL(subcategoria.subcategoria, '') AS subcategoria, IF(producto.oferta = '1', 'si', 'no') AS oferta, SUBSTRING_INDEX(foto_producto, '/', -1) AS foto"))
        ->leftJoin('empresa', 'producto.empresa_id', '=', 'empresa.id')
        ->leftJoin('categoria', 'producto.categoria_id', '=', 'categoria.id')
        ->leftJoin('subcategoria', 'producto.subcategoria_id', '=', 'subcategoria.id')
        ->orderBy("codigo")
        ->where(function($q) use ($matchThese) {
            foreach($matchThese as $key => $value) {
                if ($key == "producto.created_at") { $q->where($key, '>=', $value); }
                elseif ($key == "created_at") { $q->where($key, '<=', $value); }
                else { $q->where($key, '=', $value); }
            }
        })
        ->get();

        Excel::create('Productos', function($excel) use($productos) {
            $excel->sheet('Hoja 1', function($sheet) use($productos) {
                $sheet->cells('A:K', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:K1', function($cells) {
                    $cells->setFontWeight('bold');
                });

                $sheet->fromArray($productos);
            });
        })->export('xlsx');

        return ['msg'=>'Excel creado'];
    }

    public function exportar_usuarios_app()
    {
        $productos = usuariosModel::query()
        ->select(DB::raw("usuario.id, usuario.nombre, usuario.apellido, usuario.correo, usuario.fechaRegistro, usuario.estado, genero.nombreGenero AS genero, IF(usuario.status = 0, 'bloqueado', IF(usuario.status = 1, 'activo', IF(usuario.status = 2, 'pendiente', 'Unkonwn status'))) as status"))
        ->leftJoin('genero', 'usuario.genero_id', '=', 'genero.id')
        ->get();

        Excel::create('Usuarios aplicación', function($excel) use($productos) {
            $excel->sheet('Hoja 1', function($sheet) use($productos) {
                $sheet->cells('A:I', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                
                $sheet->cells('A1:I1', function($cells) {
                    $cells->setFontWeight('bold');
                });

                $sheet->fromArray($productos);
            });
        })->export('xlsx');

        return ['msg'=>'Excel creado'];
    }
}
