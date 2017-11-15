<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;

class ionicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $title = 'Notificaciones App';
            $menu = 'Ionic';
            return view('ionic.index', ['menu' => $menu, 'title' => $title]);
        } else {
            return redirect::to('/');
        }
    }
}
