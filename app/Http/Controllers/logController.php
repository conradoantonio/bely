<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\loginRequest;
use App\Http\Controllers\Controller;
use App\usuariosModel;
use App\Pedidos;
use App\Cotizaciones;
use DB;
use Session;
use Auth;
use Redirect;
use PDO;

class LogController extends Controller
{
    /**
     * Redirecciona al dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $title = 'Inicio';
            $menu = 'Inicio';
            $dashboard = Auth::user()->empresa_id != 2 ? $this->dashboardAdminData() : $this->dashboardGdlboxUser();
            $ventas_semanales = $this->get_ventas_semanales();

            return view('admin.dashboard', ['title' => $title, 'menu' => $menu, 'dashboard' => json_decode($dashboard), 'ventas_semanales' => $ventas_semanales]);
        } else {
            return redirect::to('/');
        }
    }

    /**
     * Valida el inicio de sesión de un usuario.
     *
     * @param  App\Http\Requests\loginRequest $request
     * @return view admin si el usuario y contraseña son correctos, o view login si falla cualquiera de los datos proporcionados.
     */
    public function store(loginRequest $request)
    {
        if (Auth::attempt(['user' => $request['user'], 'password' => $request['password']])) {
            $empresa = DB::table('empresa')->where('id', auth()->user()->empresa_id)->first();
            Session::put('logo', $empresa->logo);
            return Redirect::to('dashboard');
        }
        return Redirect::to('/');
    }

    /**
     * Termina la sesión de un usuario.
     *
     * @return Regresa al login
     */
    public function logout() 
    {
        Auth::logout();
        return Redirect('/');
    }

    /**
     * @return The total of app users, banned app users, orders.
     */
    public function dashboardAdminData() 
    {
        $main_data = new \stdClass();

        $main_data->total_app_users = usuariosModel::total_app_users();
        $main_data->banned_app_users = usuariosModel::count_banned_app_users();
        $main_data->total_orders = Pedidos::total_orders();
        $main_data->total_sales = Pedidos::total_sales();
        $main_data->banned_app_percent = round((($main_data->banned_app_users / $main_data->total_app_users) * 100), 2, PHP_ROUND_HALF_DOWN);

        return json_encode($main_data);
    }

    /**
     * @return The total of app users, banned app users, orders.
     */
    public function dashboardGdlboxUser() 
    {
        $main_data = new \stdClass();

        $main_data->total_app_users = usuariosModel::total_app_users();
        $main_data->banned_app_users = usuariosModel::count_banned_app_users();
        $main_data->total_cotizaciones = Cotizaciones::total_cotizaciones();
        $main_data->total_cotizaciones_atendidas = Cotizaciones::total_cotizaciones_atendidas();
        $main_data->banned_app_percent = round((($main_data->banned_app_users / $main_data->total_app_users) * 100), 2, PHP_ROUND_HALF_DOWN);

        return json_encode($main_data);
    }
    
    /**
     * @return Regresa el número total de ventas semanales.
     */
    public function get_ventas_semanales() 
    {
        $dias_s = array('','Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');

        date_default_timezone_set('America/Mexico_City');//Esto fue puesto para obtener corectamente la hora en local, remover si es necesario

        $query = Pedidos::ventas_semanales();

        $semana = array();
        $dia_nombre = array();
        for ($i=1; $i <= 7; $i++) {
            $fechaActual = date("Y-m-d");
            $fechaActual = date_create($fechaActual);
            $fechaActual = date_sub($fechaActual, date_interval_create_from_date_string($i.' days'));
            array_push($semana, $fechaActual->format('Y-m-d'));
        }

        foreach ($semana as $dia) {
            array_push($dia_nombre, $dias_s[date('N', strtotime($dia))]);
        }

        $array_wd = array();
        foreach ($query as $value) {
            array_push($array_wd, $value->created_at);
        }

        $numero_logs = array();
        foreach ($query as $value) {
            array_push($numero_logs, $value->Costo_total);
        }
        
        $final_array = $semana;

        foreach ($final_array as $key => $value) { $final_array[$key] = 0; }

        foreach ($array_wd as $key => $val) {
            $numero_logs[$key];
            $pasa = array_search($val, $semana);
            if (is_int($pasa)) {
                $final_array[$pasa] = $numero_logs[$key];
            } 
        }

        $object = new \stdClass();
        $object->dias_semana = array_reverse($dia_nombre);
        $object->total_ventas = array_reverse($final_array);

        return json_encode($object);
    }
}
