<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Redirect;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Image;

class configuracionController extends Controller
{
    /**
     * Carga la vista para poder cargar un pdf con el aviso de privacidad y/o poder descargar uno existente.
     *
     * @return \Illuminate\Http\Response
     */
    public function preguntas_frecuentes()
    {
        if (Auth::check()) {
            $preguntas = DB::table('preguntas_frecuentes')->paginate(10);
            $title = 'Preguntas frecuentes';
            $menu = 'Configuraciones';
            return view('configuracion.preguntas_frecuentes', ['preguntas' => $preguntas, 'title' => $title, 'menu' => $menu]);
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Guarda una pregunta frecuente
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar_pregunta(Request $request)
    {
        $name = "img/preguntas/default.jpg";//Solo permanecerá con ese nombre cuando NO se seleccione una imágen como tal.
        if ($request->file('imagen_pregunta')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png");
            $extension_archivo = $request->file('imagen_pregunta')->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/preguntas/'.time().'.'.$request->file('imagen_pregunta')->getClientOriginalExtension();
                $imagen_pregunta = Image::make($request->file('imagen_pregunta'))
                ->resize(460, 384)
                ->save($name);
            }
        }

        DB::table('preguntas_frecuentes')->insert(
            ['pregunta' => $request->pregunta, 
             'respuesta' => $request->respuesta,
             'imagen' => $name]
        );
        return back();
    }

    /**
     * Edita una pregunta frecuente
     *
     * @return \Illuminate\Http\Response
     */
    public function editar_pregunta(Request $request)
    {
        $name = "img/preguntas/default.jpg";//Solo permanecerá con ese nombre cuando NO se seleccione una imágen como tal.
        if ($request->file('imagen_pregunta')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png");
            $extension_archivo = $request->file('imagen_pregunta')->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/preguntas/'.time().'.'.$request->file('imagen_pregunta')->getClientOriginalExtension();
                $imagen_pregunta = Image::make($request->file('imagen_pregunta'))
                ->resize(460, 384)
                ->save($name);
            }
        }

        $actualizar = ['pregunta' => $request->pregunta, 'respuesta' => $request->respuesta];
        $name != "img/preguntas/default.jpg" ? $actualizar = ['imagen' => $name] : '';
        DB::table('preguntas_frecuentes')
        ->where('id', $request->id)
        ->update($actualizar);
        return back();
    }

    /**
     * Edita una pregunta frecuente
     *
     * @return \Illuminate\Http\Response
     */
    public function eliminar_pregunta(Request $request)
    {
        DB::table('preguntas_frecuentes')
        ->where('id', $request->id)
        ->delete();
        return back();
    }

    /**
     * Carga la vista para poder cargar un pdf con la información de quienes somos y/o poder descargar el pdf existente.
     *
     * @return \Illuminate\Http\Response
     */
    public function quienes_somos()
    {
        if (Auth::check()) {
            $pdf = DB::table('quienes_somos')
            ->orderBy('id', 'desc')
            ->first();
            $title = '¿Quienes somos?';
            $menu = 'Configuraciones';
            return view('configuracion.quienes_somos', ['pdf' => $pdf, 'title' => $title, 'menu' => $menu]);
        } else {
            return Redirect::to('/');
        }
    }

    /**
     *===========================================================================================================================
     *=                                Empiezan las funciones relacionadas al envío de productos                                =
     *===========================================================================================================================
     */

    /**
     * Carga la vista para las preferencias de envío de los pedidos.
     *
     * @return \Illuminate\Http\Response
     */
    public function preferencias_envio()
    {
        if (Auth::check()) {
            $preferencias = $this->get_preferencias_user();
            $title = 'Preferencias de envío';
            $menu = 'Configuraciones';
            return view('configuracion.preferencias_envio', ['title' => $title, 'menu' => $menu, 'preferencias' => json_decode($preferencias)]);
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Activa o desactiva el envío gratuito
     *
     * @return \Illuminate\Http\Response
     */
    public function activar_envio_gratuito(Request $request)
    {
        if ($request->has('envio')) {
            $registro = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $request->empresa_id)->first();
            if (count($registro) == 0) {
                DB::table('preferencias_envio')
                ->insert(['envio_gratuito' => $request->envio, 'empresa_id' => $request->empresa_id]);
                return ['success'=>'true'];
            } else {
                DB::table('preferencias_envio')
                ->where('empresa_id', $request->empresa_id)
                ->update(['envio_gratuito' => $request->envio]);
                return ['success'=>'true'];
            }
        }

        return ['success'=>'false'];
    }

    /**
     * Cambia el monto mínimo de envío
     *
     * @return \Illuminate\Http\Response
     */
    public function cambiar_monto_minimo(Request $request)
    {
        if ($request->has('monto')) {
            $registro = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $request->empresa_id)->first();
            if (count($registro) == 0) {
                DB::table('preferencias_envio')
                ->insert(['monto_minimo_envio' => $request->monto, 'empresa_id' => $request->empresa_id]);
                return ['success'=>'true'];
            } else {
                DB::table('preferencias_envio')
                ->where('empresa_id', $request->empresa_id)
                ->update(['monto_minimo_envio' => $request->monto]);
                return ['success'=>'true'];
            }
        }

        return ['success'=>'false'];
    }

    /**
     * Cambia la tarifa de envío
     *
     * @return \Illuminate\Http\Response
     */
    public function cambiar_tarifa_envio(Request $request)
    {
        if ($request->has('tarifa')) {
            $registro = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $request->empresa_id)->first();
            if (count($registro) == 0) {
                DB::table('preferencias_envio')
                ->insert(['tarifa_envio' => $request->tarifa, 'empresa_id' => $request->empresa_id]);
                return ['success'=>'true'];
            } else {
                DB::table('preferencias_envio')
                ->where('empresa_id', $request->empresa_id)
                ->update(['tarifa_envio' => $request->tarifa]);
                return ['success'=>'true'];
            }
        }

        return ['success'=>'false'];
    }

    /**
     * Actualiza la información sobre el porcentaje de descuento de los productos
     *
     * @return \Illuminate\Http\Response
     */
    public function cambiar_descuento_productos(Request $request)
    {
        if ($request->has('descuento_porcentaje')) {
            $registro = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $request->empresa_id)->first();
            if (count($registro) == 0) {
                DB::table('preferencias_envio')
                ->insert(['descuento_activo' => $request->descuento_activo, 'empresa_id' => $request->empresa_id, 'descuento_porcentaje' => $request->descuento_porcentaje]);
                return ['success'=>'true'];
            } else {
                DB::table('preferencias_envio')
                ->where('empresa_id', $request->empresa_id)
                ->update(['descuento_activo' => $request->descuento_activo, 'descuento_porcentaje' => $request->descuento_porcentaje]);
                return ['success'=>'true'];
            }
        }

        return ['success'=>'false'];
    }

    /**
     * Configura una fecha y hora para los precios de productos
     *
     * @return \Illuminate\Http\Response
     */
    public function configurar_fecha_promocion(Request $request)
    {
        $registro = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', $request->empresa_id)->first();
        if (count($registro) == 0) {
            DB::table('preferencias_envio')
            ->insert(['mostrar_timer' => $request->mostrar_timer, 'empresa_id' => $request->empresa_id, 'dia_limite' => $request->dia, 'hora_limite' => $request->hora]);
            return response(['msg' => 'Éxito guardando cambios', 'status' => 'ok'], 200);
        } else {
            DB::table('preferencias_envio')
            ->where('empresa_id', $request->empresa_id)
            ->update(['mostrar_timer' => $request->mostrar_timer, 'dia_limite' => $request->dia, 'hora_limite' => $request->hora]);
            return response(['msg' => 'Éxito modificando cambios', 'status' => 'ok'], 200);
        }

        return response(['msg' => 'Ocurrió un problema', 'status' => 'error'], 200);
    }

    

    public function get_preferencias_user()
    {
        $objeto = new \stdClass();
        $registro = DB::table('preferencias_envio')->orderBy('id', 'desc')->where('empresa_id', Auth::user()->empresa_id)->first();
        if (count($registro)) {
            $objeto->envio_gratuito = $registro->envio_gratuito;
            $objeto->monto_minimo_envio = $registro->monto_minimo_envio;
            $objeto->tarifa_envio = $registro->tarifa_envio;
            $objeto->descuento_activo = $registro->descuento_activo;
            $objeto->descuento_porcentaje = $registro->descuento_porcentaje;
            $objeto->mostrar_timer = $registro->mostrar_timer;
            $objeto->dia_limite = $registro->dia_limite;
            $objeto->hora_limite = $registro->hora_limite;
        } else {
            $objeto->envio_gratuito = 0;
            $objeto->monto_minimo_envio = 0;
            $objeto->tarifa_envio = 0;
            $objeto->descuento_activo = 0;
            $objeto->descuento_porcentaje = 0;
            $objeto->mostrar_timer = 0;
            $objeto->dia_limite = 0;
            $objeto->hora_limite = 0;
        }

        return json_encode($objeto);
    }

    /**
     *==========================================================================================================================
     *=                           Empiezan las funciones relacionadas a la información de la empresa                           =
     *==========================================================================================================================
     */

    /**
     * Carga la vista para las preferencias de envío de los pedidos.
     *
     * @return \Illuminate\Http\Response
     */
    public function info_empresa()
    {
        if (Auth::check()) {
            $title = 'Información empresa';
            $menu = 'Configuraciones';
            $datos = DB::table('informacion_empresa')->where('empresa_id', Auth::user()->empresa_id)->first();
            $header = count($datos) > 0 ? 'Editar' : 'Guardar';
            return view('configuracion.info_empresa', ['title' => $title, 'menu' => $menu, 'datos' => $datos, 'header' => $header]);
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Guarda información de una empresa
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar_info_empresa(Request $request)
    {
        $name = "img/logo_empresa/default.jpg";//Solo permanecerá con ese nombre cuando NO se seleccione una imágen como tal.
        if ($request->file('logo')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png");
            $extension_archivo = $request->file('logo')->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/logo_empresa/'.time().'.'.$request->file('logo')->getClientOriginalExtension();
                $logo = Image::make($request->file('logo'))
                ->resize(460, 384)
                ->save($name);
            }
        }

        DB::table('informacion_empresa')->insert(
            ['direccion' => $request->direccion, 
             'telefono' => $request->telefono,
             'numeroExt' => $request->numeroExt,
             'numeroInt' => $request->numeroInt,
             'codigo_postal' => $request->codigo_postal,
             'logo' => $name,
             'empresa_id' => Auth::user()->empresa_id]
        );
        return back();
    }

    /**
     * Guarda información de una empresa
     *
     * @return \Illuminate\Http\Response
     */
    public function editar_info_empresa(Request $request)
    {
        $name = "img/logo_empresa/default.jpg";//Solo permanecerá con ese nombre cuando NO se seleccione una imágen como tal.
        if ($request->file('logo')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png");
            $extension_archivo = $request->file('logo')->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/logo_empresa/'.time().'.'.$request->file('logo')->getClientOriginalExtension();
                $logo = Image::make($request->file('logo'))
                ->resize(460, 384)
                ->save($name);
            }
        }

        $actualizar = [ 'direccion' => $request->direccion, 
                        'telefono' => $request->telefono,
                        'numeroExt' => $request->numeroExt,
                        'numeroInt' => $request->numeroInt,
                        'codigo_postal' => $request->codigo_postal];

        $name != "img/logo_empresa/default.jpg" ? $actualizar = ['logo' => $name] : '';
        
        DB::table('informacion_empresa')
        ->where('empresa_id', Auth::user()->empresa_id)
        ->update($actualizar);
        return back();
    }
}
