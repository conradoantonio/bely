<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\quienes_somosModel;
use Input;
use Image;

class archivosController extends Controller
{
    /**
     * Carga el archivo pdf que contiene el pdf ¿quienes somos?.
     *
     * @return \Illuminate\Http\Response
     */
    public function cargar_quienes_somos(Request $request)
    {
        $pdf_name = "pdf/default.pdf";
        if ($request->file('quienes_somos_pdf')) {
            $extensiones_permitidas = array("1"=>"pdf");
            $extension_archivo = $request->file('quienes_somos_pdf')->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $pdf = $request->file('quienes_somos_pdf');
                $pdf_name = 'pdf/'.time().'.'.$request->file('quienes_somos_pdf')->getClientOriginalExtension();
                $pdf->move('pdf', $pdf_name);
            }
        }

        $img_name = "img/quienes_somos/default.jpg";
        if ($request->file('quienes_somos_img')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png");
            $file = Input::file('quienes_somos_img');
            $extension_archivo = $file->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $img_name = 'img/quienes_somos/'.time().'.'.$request->file('quienes_somos_img')->getClientOriginalExtension();
                $img_quienes_somos = Image::make($request->file('quienes_somos_img'))
                //->resize(300, 300)
                ->save($img_name);
            }
        }

        $registros = quienes_somosModel::all();
        if (count($registros) > 0) {
            $update = array();
            $pdf_name != "pdf/default.pdf" ? $update = ['nombrePDF' => $pdf_name] : '';
            $img_name != "img/quienes_somos/default.jpg" ? $update = ['imagen' => $img_name] : '';
            $request->link_video != "" && $request->link_video != null ? $update = ['linkVideo' => $request->link_video] : '';
            DB::table('quienes_somos')
            ->update($update);
        } else {
            $quienes_somos = new quienes_somosModel;
            $pdf_name != "pdf/default.pdf" ? $quienes_somos->nombrePDF = $pdf_name : '';
            $img_name != "img/quienes_somos/default.jpg" ? $quienes_somos->imagen = $img_name : '';
            $request->link_video != "" || $request->link_video != null ? $quienes_somos->linkVideo = $request->link_video : '';
            $quienes_somos->save();
        }

        
        if ($pdf_name != "pdf/default.pdf") {
            if ($request->has('pdf_actual')) {//Verificamos si existe un pdf qué eliminar
                $directorio = public_path().'/'.$request->pdf_actual;
                if (file_exists($directorio)) {//Se revisa si el archivo existe
                    if (unlink(public_path().'/'.$request->pdf_actual)) {//Se verifica que se pueda eliminar el archivo
                        echo "Se borró el pdf";
                    } else { 
                        //dd("Error eliminando");
                    }
                } else {
                    //dd("No es directorio la ruta ". $directorio);
                }
            }
        }

        if ($img_name != "img/quienes_somos/default.jpg") {
            if ($request->has('img_actual')) {//Verificamos si existe una imágen qué eliminar
                $directorio = public_path().'/'.$request->img_actual;
                if (file_exists($directorio)) {//Se revisa si el archivo existe
                    if (unlink(public_path().'/'.$request->img_actual)) {//Se verifica que se pueda eliminar el archivo
                        echo "Se borró la imagen";
                    } else {
                        //dd("Error eliminando");
                    }
                } else {
                    //dd("No es directorio la ruta ". $directorio);
                }
            }
        }

        return back();
    }

    /**
     * Descarga el archivo pdf que contiene el aviso de privacidad.
     *
     * @return $pathToFile (pdf)
     */
    public function descargar_quienes_somos($path)
    {
        $nombre = '¿Quienes somos?.pdf';
        $pathToFile = public_path().'/pdf/'.$path;
        return response()->download($pathToFile, $nombre);
    }
}
