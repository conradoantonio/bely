@extends('admin.main')

@section('content')
<style>
th {
    text-align: center!important;
}
textarea {
    resize: none;
}
.table td.text {
    max-width: 177px;
}
.table td.text span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    max-width: 100%;
}
</style>
<div class="content">
    <div class="page-title text-center">
        <h3>Dashboard </h3>
    </div>

    @if(auth()->user()->empresa_id != 2)
        <div class="row" id="data_user_admin">
            <div class="col-md-3 col-sm-6 spacing-bottom-sm spacing-bottom text-left">
                <div class="tiles blue added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                        <div class="tiles-title"> Ventas totales </div>
                        <div class="heading"> <span class="animate-number" data-value="{{$dashboard->total_orders}}" data-animation-duration="1000">0</span> </div>
                        <div class="progress transparent progress-small no-radius">
                            <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="100%" ></div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">Hechas desde la app</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 spacing-bottom-sm spacing-bottom text-left">
                <div class="tiles green added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                        <div class="tiles-title"> Total de usuarios </div>
                        <div class="heading"> <span class="animate-number" data-value="{{$dashboard->total_app_users}}" data-animation-duration="1200">0</span> </div>
                        <div class="progress transparent progress-small no-radius">
                            <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="100%"></div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">Registrados en la aplicación</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 spacing-bottom text-left">
                <div class="tiles red added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                        <div class="tiles-title"> Usuarios bloqueados </div>
                        <div class="heading"> <span class="animate-number" data-value="{{$dashboard->banned_app_users}}" data-animation-duration="1200">0</span> </div>
                        <div class="progress transparent progress-white progress-small no-radius">
                            <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="" ></div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">{{$dashboard->banned_app_percent}}% usuarios totales (app) </span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="tiles purple added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                            <div class="tiles-title"> Ganancia en ventas </div>
                            <div class="row-fluid">
                            <div class="heading">$<span class="animate-number" data-value="{{$dashboard->total_sales/100}}" data-animation-duration="700">0</span> </div>
                            <div class="progress transparent progress-white progress-small no-radius">
                                <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="100%"></div>
                            </div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">Hechas desde la app</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="text-center col-sm-12 col-xs-12"><!-- Se imprime con todo el ancho de la página -->
                <canvas id="myChart" height="200" width="700"></canvas>  
            </div>
        </div>
    @endif

    @if(auth()->user()->empresa_id == 2)
        <div class="row" id="data_user_admin">
            <div class="col-md-3 col-sm-6 spacing-bottom-sm spacing-bottom text-left">
                <div class="tiles blue added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                        <div class="tiles-title"> Cotizaciones totales </div>
                        <div class="heading"> <span class="animate-number" data-value="{{$dashboard->total_cotizaciones}}" data-animation-duration="1000">0</span> </div>
                        <div class="progress transparent progress-small no-radius">
                            <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="100%" ></div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">Hechas desde la app</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 spacing-bottom-sm spacing-bottom text-left">
                <div class="tiles green added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                        <div class="tiles-title"> Total de usuarios </div>
                        <div class="heading"> <span class="animate-number" data-value="{{$dashboard->total_app_users}}" data-animation-duration="1200">0</span> </div>
                        <div class="progress transparent progress-small no-radius">
                            <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="100%"></div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">Registrados en la aplicación</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 spacing-bottom text-left">
                <div class="tiles red added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                        <div class="tiles-title"> Usuarios bloqueados </div>
                        <div class="heading"> <span class="animate-number" data-value="{{$dashboard->banned_app_users}}" data-animation-duration="1200">0</span> </div>
                        <div class="progress transparent progress-white progress-small no-radius">
                            <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="" ></div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">{{$dashboard->banned_app_percent}}% usuarios totales (app) </span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="tiles purple added-margin">
                    <div class="tiles-body">
                        <div class="controller"> <a href="javascript:;" class=""></a> <a href="javascript:;" class="remove"></a> </div>
                            <div class="tiles-title"> Cotizaciones atendidas </div>
                            <div class="row-fluid">
                            <div class="heading"><span class="animate-number" data-value="{{$dashboard->total_cotizaciones_atendidas}}" data-animation-duration="700">0</span> </div>
                            <div class="progress transparent progress-white progress-small no-radius">
                                <div class="progress-bar progress-bar-white animate-progress-bar" data-percentage="100%"></div>
                            </div>
                        </div>
                        <div class="description"><span class="text-white mini-description ">Desde el panel</span></div>
                    </div>
                </div>
            </div>
        </div>
    @endif    
</div>
<script type="text/javascript">
var ctx = document.getElementById("myChart");
var ventas = <?php echo $ventas_semanales;?>;

var data = {
    labels: ventas.dias_semana,
    datasets: [
        {
            label: "Ventas de última semana en Pesos (MXN)",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: ventas.total_ventas,
            spanGaps: false,
        }
    ]
};

var myLineChart = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:false
                }
            }]
        }
    }
});
</script>
@endsection