<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/cargar/quienes_somos',//Se puso aquí porque daba error al subir archivos de más de 6 mb.
        //Principio de los de conekta prueba
        '/post_send',
        '/crear_cliente',
        '/app/validar_cargo',
        '/app/validar_cargo_oxxo',
        '/procesar_orden',
        '/app/orden_empresa',
/*        '/pedidos/obtener_info_pedido',*/
        //Fin de los de conekta prueba
        '/productos/cargar_subcategorias',
        '/subir_imagenes',
        '/app/registro_usuario',
        '/app/login',
        '/app/usuario/cargar_imagen',
        '/app/actualizar_usuario',
        '/app/recuperar_contra',
        '/app/actualizar_foto',
        '/app/agregar_direccion',
        '/app/actualizar_direccion',
        '/app/eliminar_direccion',
        '/app/listar_direcciones',
        '/app/quienes_somos',
        '/app/info_empresas',
        '/app/preguntas_frecuentes',
        '/app/obtener_pedidos_usuario',
        '/app/info_empresas/costo_envios',
        '/app/generar_cotizacion',
        '/app/obtener_cotizaciones_usuario',
        '/app/enviar_correo_detalle_orden',
        '/app/enviar_correo_detalle_cotizacion',
        '/app/actualizar_player_id',
        '/app/webhook',
        '/app/*'
    ];
}
