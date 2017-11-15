<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Cotizaciones;
use App\CotizacionesDetalles;
use Redirect;

class cotizacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->empresa_id == 2) {//Si está registrado como usuario de GDLBOX
                $title = "Cotizaciones";
                $menu = "Cotizaciones";
                $cotizaciones = Cotizaciones::obtener_cotizaciones();

                return view('cotizaciones.cotizaciones', ['cotizaciones' => $cotizaciones, 'menu' => $menu, 'title' => $title]);
            } else {
                return Redirect::to('/dashboard');
            }            
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Obtiene los detalles de una cotización.
     *
     * @return \Illuminate\Http\Response
     */
    public function ver_cotizacion_detalles(Request $request)
    {
        $detalles = Cotizaciones::ver_cotizacion_detalles($request->cotizacion_id);
        $detalles->productos = $detalles->cotizaciones_detalles;
        return $detalles;
    }

    /**
     * Finaliza una cotización.
     */
    public function finalizar_cotizacion(Request $request)
    {
        return Cotizaciones::finalizar_cotizacion($request->cotizacion_id);
    }
}
