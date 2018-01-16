<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function __construct() {
        date_default_timezone_set('America/Mexico_City');
        $this->summer = date('I');
        $this->actual_datetime = date('Y-m-d H:i:s');
        $this->app_id = "107aa653-66c1-4b96-acef-57ba31cb8ace";
        $this->app_key = "MDY3OTE5MzItODExYi00NTVkLThiMDUtZmI0Nzg5NmU3NTZh";
        $this->regular_icon = url('img/regular_icon.png');
    }
}
