@extends('admin.main')

@section('content')
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-select2/select2.css')}}"  type="text/css" media="screen"/>
<link rel="stylesheet" href="{{ asset('plugins/jquery-datatable/css/jquery.dataTables.css')}}"  type="text/css" media="screen"/>
<style>
textarea {
    resize: none;
}
th {
    text-align: center!important;
}
/* Change the white to any color ;) */
input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0px 1000px white inset !important;
}
</style>
<div class="text-center" style="margin: 20px;">

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="titulo_form_producto" id="formulario_producto">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="titulo_form_producto">Nuevo producto</h4>
                </div>
                <form id="form_producto" action="" enctype="multipart/form-data" method="POST" autocomplete="off">
                    <input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}" base-url="<?php echo url();?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 col-xs-12 hidden">
                                <div class="form-group">
                                    <label for="id">ID</label>
                                    <input type="text" class="form-control" id="id" name="id">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="codigo">Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="sku">SKU</label>
                                    <input type="text" class="form-control" id="sku" name="sku" placeholder="SKU">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="precio">Precio</label>
                                    <input type="text" class="form-control" id="precio" name="precio" placeholder="Precio">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <select class="form-control" id="categoria_id" name="categoria_id">
                                        <option value="0">Elija una opción</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{$categoria->id}}">{{$categoria->categoria}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label>Subcategoría</label>
                                    <select class="form-control" id="subcategoria_id" name="subcategoria_id" categoria-id="0">
                                        <option value="0">Elija una opción</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="stock">Stock</label>
                                    <input type="number" min="0" class="form-control" id="stock" name="stock" placeholder="Stock">
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12" style="padding-bottom: 20px;">
                                <label for="oferta">Oferta</label>
                                <div class="row-fluid">
                                    <div class="checkbox check-primary">
                                        <input id="oferta" name="oferta" type="checkbox">
                                        <label for="oferta" style="padding-left:0px;"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="alert alert-info alert-dismissible text-left" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                                    <strong>Nota: </strong>
                                    Solo se permiten subir imágenes con formato jpg, png, jpeg y gif con un tamaño menor a 5mb. 
                                    Procura que su resolución sea de 460x460 px o su equivalente a escala.
                                </div>
                            </div>
                            <div id="input_foto_producto" class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label for="foto_producto">Foto producto</label>
                                    <input type="file" class="form-control" id="foto_producto" name="foto_producto">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="foto_producto">
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label>Foto actual</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="guardar_producto">Guardar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <h2>Lista de productos</h2>

    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="importar-excel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">Importar desde Excel</h4>
                </div>
                <form method="POST" action="<?php echo url();?>/productos/importar_productos" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="_token" id="token" value="{!! csrf_token() !!}" base-url="<?php echo url();?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="text-justify">Para importar usuarios a través de Excel, los datos deben estar acomodados como se describe a continuación: <br>
                                Los campos de la primera fila de la hoja de excel deben de ir los campos llamados 
                                <strong>codigo, sku, nombre, descripcion, precio, stock, foto, tipo, categoria, subcategoria, oferta</strong><br>
                                Finalmente, debajo de cada uno de estos campos deberán de ir los datos correspondientes de los productos.
                                <br><strong>Nota: </strong>Solo se aceptan archivos con extensión <kbd>xls y xlsx</kbd> y 
                                los productos repetidos en el excel no serán creados.
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="file" id="archivo-excel" name="archivo-excel">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="enviar-excel">Importar</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="row-fluid">
        <div class="span12">
            <div class="grid simple ">
                <div class="grid-title">
                    <h4>Opciones <span class="semi-bold">adicionales</span></h4>
                    <div>
                        @if(count($productos) > 0)                    
                            <button type="button" class="btn btn-info" id="exportar_productos_excel"><i class="fa fa-download" aria-hidden="true"></i> Exportar productos</button>
                        @endif
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#importar-excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Importar productos</button>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#formulario_producto" id="nuevo_producto"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo producto</button>
                    </div>
                    <div class="grid-body ">
                        <div class="table-responsive">
                            <table class="table" id="example3">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Código</th>
                                        <th>SKU</th>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th class="hide">Descripción</th>
                                        <th class="hide">categoria_id</th>
                                        <th class="hide">subcategoria_id</th>
                                        <th class="hide">oferta</th>
                                        <th class="hide">foto_producto</th>
                                        <th class="hide">Medida</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($productos) > 0)
                                        @foreach($productos as $producto)
                                            <tr class="" id="{{$producto->id}}">
                                                <td>{{$producto->id}}</td>
                                                <td>{{$producto->codigo}}</td>
                                                <td>{{$producto->sku}}</td>
                                                <td>{{$producto->nombre}}</td>
                                                <td>{{$producto->precio}}</td>
                                                <td><?php echo $producto->stock <= 0 ? '<span class="label label-important">'.$producto->stock.'</span>' : '<span class="label label-info">'.$producto->stock.'</span>';?></td>
                                                <td class="hide">{{$producto->descripcion}}</td>
                                                <td class="hide">{{$producto->categoria_id}}</td>
                                                <td class="hide">{{$producto->subcategoria_id}}</td>
                                                <td class="hide">{{$producto->oferta}}</td>
                                                <td class="hide">{{$producto->foto_producto}}</td>
                                                <td class="hide">{{$producto->medida}}</td>
                                                <td>
                                                    <button type="button" class="btn btn-info editar_producto">Editar</button>
                                                    <button type="button" class="btn btn-danger eliminar_producto">Borrar</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <td colspan="7">No hay productos disponibles</td>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('plugins/jquery-datatable/js/jquery.dataTables.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/jquery-datatable/extra/js/dataTables.tableTools.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/datatables-responsive/js/datatables.responsive.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/datatables-responsive/js/lodash.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<script src="{{ asset('js/productosAjax.js') }}"></script>
<script src="{{ asset('js/validacionesProductos.js') }}"></script>
<script type="text/javascript">

$('#formulario_producto').on('hidden.bs.modal', function (e) {
    $('#formulario_producto div.form-group').removeClass('has-error');
    $('input.form-control, textarea.form-control').val('');
    $("#formulario_producto select").val(0);
    $("#formulario_producto input#oferta").prop('checked',false);
    reiniciarSelect();
});

$('#formulario_producto').on('shown.bs.modal', function () {
    categoria_id = $('select#subcategoria_id').attr('categoria-id');
    $("#formulario_producto select#subcategoria_id").val(categoria_id);
});

$('body').delegate('button#exportar_productos_excel','click', function() {
    empresa_id = $('#token').attr('empresa-id');
    fecha_inicio = false;
    fecha_fin = false;
    window.location.href = "<?php echo url();?>/productos/exportar_productos/"+empresa_id+"/"+fecha_inicio+"/"+fecha_fin;
});

$('body').delegate('button#nuevo_producto','click', function() {
    $('input.form-control').val('');
    $('div#foto_producto').hide();
    $("h4#titulo_form_producto").text('Nuevo producto');
    $("form#form_producto").get(0).setAttribute('action', '<?php echo url();?>/productos/guardar');
});

$('body').delegate('.editar_producto','click', function() {
    $('input.form-control').val('');
    id = $(this).parent().siblings("td:nth-child(1)").text(),
    codigo = $(this).parent().siblings("td:nth-child(2)").text(),
    sku = $(this).parent().siblings("td:nth-child(3)").text(),
    nombre = $(this).parent().siblings("td:nth-child(4)").text(),
    precio = $(this).parent().siblings("td:nth-child(5)").text(),
    stock = $(this).parent().siblings("td:nth-child(6)").text(),
    descripcion = $(this).parent().siblings("td:nth-child(7)").text(),
    categoria_id = $(this).parent().siblings("td:nth-child(8)").text(),
    subcategoria_id = $(this).parent().siblings("td:nth-child(9)").text(),
    oferta = $(this).parent().siblings("td:nth-child(10)").text(),
    imagen = $(this).parent().siblings("td:nth-child(11)").text(),
    medida = $(this).parent().siblings("td:nth-child(12)").text(),

    $("h4#titulo_form_producto").text('Editar producto');
    $("form#form_producto").get(0).setAttribute('action', '<?php echo url();?>/productos/editar');
    $("#formulario_producto input#id").val(id);
    $("#formulario_producto input#codigo").val(codigo);
    $("#formulario_producto input#sku").val(sku);
    $("#formulario_producto input#nombre").val(nombre);
    $("#formulario_producto input#precio").val(precio);
    $("#formulario_producto input#stock").val(stock);
    $("#formulario_producto textarea#descripcion").val(descripcion);
    $("#formulario_producto select#categoria_id").val(categoria_id);
    $("#formulario_producto input#oferta").prop('checked',oferta == 1 ? true : false );
    $("#formulario_producto select#medida").val(medida);

    token = $('#token').val();
    cargarSubcategorías(categoria_id,token);
    $("#formulario_producto select#subcategoria_id").attr('categoria-id',subcategoria_id);

    $('#formulario_producto div#usuario_caracteristicas').hide();

    $('div#foto_producto').children().children().children().remove('img#foto_producto');
    $('div#foto_producto').children().children().append(
        "<img src='<?php echo asset('');?>/"+imagen+"' class='img-responsive img-thumbnail' alt='Responsive image' id='foto_producto'>"
    );
    $("div#foto_producto").show();

    $('#formulario_producto').modal();
});

$('body').delegate('.eliminar_producto','click', function() {
    var codigo = $(this).parent().siblings("td:nth-child(2)").text();
    var token = $("#token").val();
    var id = $(this).parent().parent().attr('id');

    swal({
        title: "¿Realmente desea eliminar al producto con el código <span style='color:#F8BB86'>" + codigo + "</span>?",
        text: "¡Cuidado!",
        html: true,
        type: "warning",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Si, continuar",
        showLoaderOnConfirm: true,
        allowEscapeKey: true,
        allowOutsideClick: true,
        closeOnConfirm: false
    },
    function() {
        eliminarProducto(id,token);
    });
});

$( "select#categoria_id" ).change(function() {
    categoria_id = $(this).val();
    token = $('#token').val();
    cargarSubcategorías(categoria_id,token);
});

function reiniciarSelect() {
    $("#formulario_producto select#subcategoria_id").attr('categoria-id', 0);
    $('select#subcategoria_id option').remove();
    $('select#subcategoria_id').append('<option value="0" selected="selected">Elija una opción</option>');  
}
</script>
@endsection