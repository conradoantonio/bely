<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\usuariosModel;
use App\usuariosSistemaModel;
use App\User;
use DB;
use Session;
use Auth;
use Redirect;
use Hash;
use Image;
use Mail;

class UsuariosController extends Controller
{
    /**
     * Muestra la tabla de los usuarios registrados del sistema.
     *
     * @param  $valido Verifica si se guardó o actualizó un usuario correctamente, variable recibida de  UsuariosController@guardar_usuario
     * @return view usuarios.usuariosSistema.usuariosSistema
     */
    public function index()
    {
        if (Auth::check()) {
            $title = "Usuarios Sistema";
            $menu = "Usuarios";
            $empresas = DB::table('empresa')->get();
            $usuarios = DB::table('users')
            ->where('user', '!=', Auth::user()->user)
            ->get();
            return view('usuarios.usuariosSistema.usuariosSistema', ['empresas' => $empresas, 'menu' => $menu, 'usuarios' => $usuarios, 'title' => $title]);
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Cambia la contraseña del usuario logeado del sistema.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return contraseña status
     */
    public function change_password(Request $request)
    {
        $user = DB::table('users')
        ->where('user', '=', $request->user)
        ->where('id', '=', $request->id)
        ->first();


        if (Hash::check($request->actualPassword, $user->password)) {
            if ($request->newPassword == $request->confirmPassword) {
                $change = User::find($request->id);
                $change->password = bcrypt($request->newPassword);
                $change->save();
                return 'contra cambiada';
            } else {
                return 'contra nueva diferentes';
            }
        } else {
            return 'contra erronea';
        }
    }

    /**
     * Guarda o edita un usuario del sistema, validando imagen y que el nombre de usuario sea único
     *
     * @param  \Illuminate\Http\Request  $request
     * @return redirect /admin/usuarios/sistema
     */
    public function guardar_usuario(Request $request)
    {
        $name = "img/user_perfil/default.jpg";//Solo permanecerá con ese nombre cuando NO se seleccione una imágen como tal.
        if ($request->file('foto_usuario')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png");
            $extension_archivo = $request->file('foto_usuario')->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/user_perfil/'.time().'.'.$request->file('foto_usuario')->getClientOriginalExtension();
                $imagen_portada = Image::make($request->file('foto_usuario'))
                ->resize(300, 300)
                ->save($name);
            }
        }

        if ($request->id != '') {// es un edit
            $validado = DB::table('users')
            ->where('user', '=', $request->user_name)
            ->where('user', '!=', $request->user_name_old)
            ->get();
        } else {// es un insert
            $validado = DB::table('users')
            ->where('user', '=', $request->user_name)
            ->get();
        }

        if(count($validado) > 0) {
            return redirect()->action('UsuariosController@index', ['valido' => md5('false')]);
        } else {

            if ($request->id != '') {//Es un edit
                $usuarioSistema = usuariosSistemaModel::find($request->id);
                $usuarioSistema->user != '' ? $usuarioSistema->user = $request->user_name : '';
                $request->password != '' ? $usuarioSistema->password = bcrypt($request->password) : '';
                $usuarioSistema->nombre != '' ? $usuarioSistema->nombre = $request->nombre : '';
                $usuarioSistema->apellido != '' ? $usuarioSistema->apellido = $request->apellido : '';
                $usuarioSistema->email != '' ? $usuarioSistema->email = $request->email : '';
                $usuarioSistema->empresa_id = $request->empresa;
                $name != 'img/user_perfil/default.jpg' ? $usuarioSistema->foto_usuario = $name : '';
            } else {//Es un insert
                $usuarioSistema = new usuariosSistemaModel;
                $usuarioSistema->user = $request->user_name;
                $usuarioSistema->password = bcrypt($request->password);
                $usuarioSistema->foto_usuario = $name;
                $usuarioSistema->nombre = $request->nombre;
                $usuarioSistema->apellido = $request->apellido;
                $usuarioSistema->email = $request->email;
                $usuarioSistema->empresa_id = $request->empresa;
            }

            $usuarioSistema->save();

            return redirect::to('/usuarios/sistema');
        }
    }

    /**
     * Elimina un usuario del sistema permanentemente
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Nada
     */
    public function eliminar_usuario(Request $request)
    {
        $usuarioSistema = usuariosSistemaModel::find($request->id);

        $usuarioSistema->delete();
    }

    /**
     * Guarda la foto de perfil de un usuario del sistema
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Vuelve a la página anterior
     */
    public function guardar_foto_usuario_sistema(Request $request)
    {
        $name = "img/user_perfil/default.jpg";//Solo permanecerá con ese nombre cuando NO se seleccione una imágen como tal.
        if ($request->file('foto_usuario_sistema')) {
            $extensiones_permitidas = array("1"=>"jpeg", "2"=>"jpg", "3"=>"png");
            $extension_archivo = $request->file('foto_usuario_sistema')->getClientOriginalExtension();
            if (array_search($extension_archivo, $extensiones_permitidas)) {
                $name = 'img/user_perfil/'.time().'.'.$request->file('foto_usuario_sistema')->getClientOriginalExtension();
                $imagen_portada = Image::make($request->file('foto_usuario_sistema'))
                ->resize(300, 300)
                ->save($name);
            }
        }
        $usuarioSistema = usuariosSistemaModel::find($request->id);

        $usuarioSistema->foto_usuario = $name;

        $usuarioSistema->save();

        return back();
    }

    /**
     *=====================================================================================================================
     *=                        Empiezan las funciones relacionadas a los usuarios de la aplicación                        =
     *=====================================================================================================================
     */

    /**
     * Muestra la tabla de los usuarios registrados de la aplicación.
     *
     * @param  $order Especifica el orden de los registros de los usuarios de la aplicación
     * @return view usuarios.usuariosApp.usuariosApp
     */
    public function usuariosApp()
    {
        if (Auth::check()) {
            $title = "Usuarios App";
            $menu = "Usuarios";
            $generos = DB::table('genero')->where('status', '=', '1')->get();
            $usuarios = DB::table('usuario')
            ->select(DB::raw("usuario.*, genero.id as idGenero, genero.nombreGenero as genero"))
            ->leftJoin('genero', 'usuario.genero_id', '=', 'genero.id')
            ->orderBy('nombre', 'id')
            ->get();
            
            return view('usuarios.usuariosApp.usuariosApp', ['menu' => $menu , 'usuarios' => $usuarios, 'generos' => $generos, 'title' => $title]);
        } else {
            return Redirect::to('/');
        }
    }

    /**
     * Guarda un nuevo usuario de la aplicación
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Nada
     */
    public function guardar_usuario_app(Request $request)
    {
        date_default_timezone_set('America/Mexico_City');//Esto fue puesto para obtener corectamente la hora en local.
        $validado = DB::table('usuario')
        ->where('correo', '=', $request->correo)
        ->get();

        if (count($validado) > 0) {
            return redirect()->action('UsuariosController@usuariosApp', ['valido' => md5('false')]);
        } else {
            $usuarioSistema = new usuariosModel;
            $usuarioSistema->password = md5($request->password);
            $usuarioSistema->nombre = $request->nombre;
            $usuarioSistema->apellido = $request->apellido;
            $usuarioSistema->correo = $request->correo;
            $usuarioSistema->tarjeta = $request->tarjeta;
            $usuarioSistema->estado = $request->estado;
            $usuarioSistema->genero_id = $request->genero;
            $usuarioSistema->fechaNacimiento = $request->fechaNacimiento;
            $usuarioSistema->foto_perfil = "img/usuario_app/default.jpg";
            $usuarioSistema->fechaRegistro =  date("Y-m-d H:i:s");
       
            $usuarioSistema->save();

            return redirect::to('/usuarios/app');
        }
    }

    /**
     * Guarda un nuevo usuario de la aplicación
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Nada
     */
    public function editar_usuario_app(Request $request)
    {
         $validado = DB::table('usuario')
        ->where('correo', '=', $request->correo)
        ->where('correo', '!=', $request->correo_viejo)
        ->get();

        if(count($validado) > 0) {
            return redirect()->action('UsuariosController@usuariosApp', ['valido' => md5('false')]);
        } else {
            $usuarioSistema = usuariosModel::find($request->id);
            $request->password ? $usuarioSistema->password = md5($request->password) : '';
            $usuarioSistema->nombre = $request->nombre;
            $usuarioSistema->apellido = $request->apellido;
            $usuarioSistema->correo = $request->correo;
            $usuarioSistema->tarjeta = $request->tarjeta;
            $usuarioSistema->estado = $request->estado;
            $usuarioSistema->genero_id = $request->genero;
            $usuarioSistema->fechaNacimiento = $request->fechaNacimiento;
       
            $usuarioSistema->save();

            return redirect::to('/usuarios/app');
        }
    }

    /**
     * Cambia el status de un usuario de la aplicación a bloqueado, activo o eliminado.
     *
     * @param  Request $request
     * @return Nada
     */
    public function destroy(Request $request)
    {
        $status_anterior = DB::table('usuario')->where('id', $request->id)->pluck('status');

        if ($request->status == 3 || $request->status == 4) {//Significa que el usuario se va a borrar
            DB::table('usuario')
            ->where('id', $request->id)
            ->delete();
        } else if ($request->status == 1 || $request->status == 2){
            DB::table('usuario')
            ->where('id', $request->id)
            ->update(['status' => 1]);
        } else {
            DB::table('usuario')
            ->where('id', $request->id)
            ->update(['status' => $request->status]);
        }

        if ($status_anterior == '2') {
            $this->enviar_correo_aceptacion($request->correo, $request->status);
        }
    }

    public function enviar_correo_aceptacion($correo, $status)
    {
        if ($status == '2') {
            $msg = "¡Le damos la bienvenida! \nYa se ha completado el primer paso, ahora que tu solicitud de aceptación ha sido aprobada ya puedes empezar a utilizar nuestra app.";
            $subject = "Aceptación de cuenta";
        } else {
            $msg = "Lo sentimos pero su petición para empezar a usar una cuenta en la aplicación belyapp ha sido rechazada.";
            $subject = "Rechazo de cuenta";
        }

        $to = $correo;
        $enviado = Mail::raw($msg, function($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
        if ($enviado == 1) {
            return ['msg'=>'enviado'];
        }
    }
}