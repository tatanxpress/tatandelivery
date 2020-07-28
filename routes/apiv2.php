<?php

use Illuminate\Http\Request;
 
/*
|--------------------------------------------------------------------------
| API JWT
|--------------------------------------------------------------------------
|
| RUTAS PROTEGIDAS CON LARAVEL JWT
|
*/
 

Route::post('verificar/telefono', 'Api\Auth\LoginController@verificarNumero'); // verificar si el telefono esta registrado
Route::post('verificar-codigo-temporal', 'Api\Auth\LoginController@verificarCodigoTemporal'); // verificar telefono + codigo temporal.

Route::post('usuario/login', 'Api\Auth\LoginController@loginUsuario'); // login usuario, version antigua
Route::post('usuario/login-token', 'Api\Auth\LoginController@loginUsuarioToken'); // login que trae token

Route::post('usuario/codigo-correo', 'Api\Auth\LoginController@codigoCorreo'); // enviar codigo al correo para recuperacion
Route::post('usuario/revisar-codigo', 'Api\Auth\LoginController@revisarCodigoCorreo'); // revisar codigo del correo
Route::post('usuario/registro', 'Api\Auth\RegisterController@registroUsuario'); // registro usuario
Route::post('usuario/nueva-password', 'Api\PerfilController@nuevaPassword'); // cambia contraseña con correo

// usuarios que ya habian iniciado sesion, solo solicitar 1 sola vez esta ruta
Route::post('usuario/servicios/lista-pedir-token', 'Api\ServiciosController@getServiciosZonaToken'); // lista de tipo servicio por zona
 

// RUTAS TOKEN JWT
//Route::middleware(['jwt.auth'])->group(function(){

    // perfil  
    Route::post('usuario/informacion', 'Api\PerfilController@infoPerfil'); // cambia contraseña en perfil
    Route::post('usuario/editar-perfil', 'Api\PerfilController@editarPerfil'); // cambiar datos del perfil
    Route::post('usuario/direcciones', 'Api\PerfilController@verDirecciones'); // lista de direcciones
    Route::post('usuario/cambiar-password', 'Api\PerfilController@cambiarPassword'); // cambia contraseña con id
    Route::post('usuario/zonas/lista', 'Api\PoligonoController@getListaZonas'); // lista de zonas para mapa
    Route::post('usuario/nueva/direccion', 'Api\PerfilController@guardarDireccion'); // nueva direccion
    Route::post('usuario/seleccionar/direccion', 'Api\PerfilController@seleccionarDireccion'); // seleccionar direccion
    Route::post('usuario/eliminar/direccion', 'Api\PerfilController@eliminarDireccion'); // eliminar una direccion
            
    // servicios   
    Route::post('usuario/servicios/lista-token', 'Api\ServiciosController@getServiciosZonaMejorado'); // lista de tipo servicio por zona
   
    Route::post('usuario/servicios/tipo/servicio', 'Api\ServiciosController@getTipoServicios'); // locales tipo cualquiera
    Route::post('usuario/servicios/todo/producto', 'Api\ServiciosController@getTodoProductoVistaComida'); // menu de local tipo comida
    Route::post('usuario/servicios/info/producto', 'Api\ServiciosController@getProductoIndividual'); // informacion de producto individual
    Route::post('usuario/servicios/ver/publicidad', 'Api\ServiciosController@verPublicidad'); // ver promocionales
            
    

    // vista tipo tienda   
    Route::post('usuario/servicios/tienda/producto', 'Api\VistaTipoTiendaController@getTodoProductoTienda'); // productos de tienda
        
    // carrito 
    Route::post('usuario/carrito/producto/agregar', 'Api\CarritoTemporalController@agregarProducto'); 
    Route::post('usuario/carrito/ver/orden', 'Api\CarritoTemporalController@verCarritoCompras'); // ver la orden
    Route::post('usuario/carrito/eliminar/producto', 'Api\CarritoTemporalController@eliminarProducto'); // eliminar producto individual       
    Route::post('usuario/carrito/ver/producto', 'Api\CarritoTemporalController@verProducto'); // ver producto individual
    Route::post('usuario/carrito/borrar/orden', 'Api\CarritoTemporalController@eliminarCarritoCompras'); // eliminar carrito de compras
    Route::post('usuario/carrito/cambiar/cantidad', 'Api\CarritoTemporalController@cambiarCantidad'); // cambiar cantidad de este producto
    Route::post('usuario/carrito/ver/proceso', 'Api\CarritoTemporalController@verProcesarOrden'); // ver info para procesar la orden
        
    // buscador
    Route::post('usuario/servicios/buscar/producto', 'Api\BuscadorController@buscarProducto'); // buscador
    Route::post('usuario/productos/ver/seccion', 'Api\BuscadorController@buscarProductoSeccion'); // lista de productos "ver todos"
    Route::post('usuario/servicios/buscar/global', 'Api\BuscadorController@buscarProductoGlobal'); // buscador global de productos

    // proceso    
    Route::post('usuario/proceso/ver/ordenes', 'Api\ProcesadorOrdenesController@verOrdenes'); // ver orden hecha por mismo usuario
    Route::post('usuario/proceso/ver/orden/id', 'Api\ProcesadorOrdenesController@verOrdenPorID'); // ver orden por id

    // version mejorada para android, pero ya no utilizada en nueva actualizacion
    Route::post('usuario/proceso/ver/ordenes-android', 'Api\Clientes\InformacionOrdenClienteController@verOrdenesInfo'); // ver orden hecha por mismo usuario

    
    Route::post('usuario/proceso/orden/productos', 'Api\ProcesadorOrdenesController@ordenProductos'); // ver lista de producto de orden
    Route::post('usuario/proceso/orden/producto/individual', 'Api\ProcesadorOrdenesController@ordenProductosIndividual'); // ver producto de la orden
    Route::post('usuario/proceso/ver/motorista', 'Api\ProcesadorOrdenesController@motoristaAsignado'); // ver motorista de la orden
    Route::post('usuario/proceso/calificar/motorista', 'Api\ProcesadorOrdenesController@calificarMotorista'); // calificar motorista
    
    Route::post('usuario/proceso/orden/cancelar', 'Api\ProcesadorOrdenesController@cancelarOrden'); // cancelar orden 
    Route::post('usuario/proceso/borrar/vista/orden', 'Api\ProcesadorOrdenesController@borrarVistaOrden'); // borrar vista
    

    Route::post('usuario/proceso/orden/estado-1', 'Api\ProcesadorOrdenesController@procesarOrdenEstado1'); // procesar orden primer paso *
    Route::post('usuario/proceso/orden/estado-3', 'Api\ProcesadorOrdenesController@procesarOrdenEstado3'); // procesar orden tercer paso *
        
    Route::post('usuario/verificar/cupon', 'Api\ProcesadorOrdenesController@verificarCupon'); // procesar orden primer paso *
    

    
    // seccion encargos
    Route::post('usuario/encargos/por-zona', 'Api\EncargosController@encargosPorZona'); // lista de encargos
    Route::post('usuario/encargos/categorias-vertical', 'Api\EncargosController@listaDeCategorias'); // lista de categorias con productos verticales
    Route::post('usuario/encargos/categorias-horizontal', 'Api\EncargosController@listaDeCategoriasHorizontal'); // lista de categorias con productos horizontales
    Route::post('usuario/encargos/categorias-horizontal-seccion', 'Api\EncargosController@listaDeCategoriasHorizontalSeccion'); // lista de categorias con productos horizontales

    
    Route::post('usuario/encargos/producto-individual', 'Api\EncargosController@productoIndividual'); // producto individual del encargo
    Route::post('usuario/encargos/agregar-producto', 'Api\EncargosController@agregarProductoEncargo'); // agregar producto de encargo
    Route::post('usuario/encargos/ver-carrito', 'Api\EncargosController@verCarritoDeCompras'); // ver carrito de compras

    Route::post('usuario/encargos/carrito/producto-individual', 'Api\EncargosController@verProductoDeCarrito'); // ver producto del carrito encargo
    Route::post('usuario/encargos/carrito/producto-individual-update', 'Api\EncargosController@verProductoDeCarritoActualizar'); // actualizar


    Route::post('usuario/encargos/carrito/borrar-producto', 'Api\EncargosController@eliminarProductoEncargo'); // eliminar producto individual del encargo      
    Route::post('usuario/encargos/carrito/borrar-carrito', 'Api\EncargosController@eliminarCarritoEncargo'); // eliminar carrito encargos
    Route::post('usuario/encargos/guardar-modoespera', 'Api\EncargosController@guadarEncargoModoEspera'); // guardar encargo temporal
    Route::post('usuario/encargos/ver-lista', 'Api\EncargosController@verListaDeEncargos'); // ver lista de encargos
    Route::post('usuario/encargos/buscador-productos', 'Api\EncargosController@buscadorProductoPorEncargo'); // buscador productos

    Route::post('usuario/encargos/ver-lista-productos', 'Api\EncargosController@verListaProductosDeEncargo'); // ver lista de productos del encargo
    Route::post('usuario/encargos/ver-lista-productos-individual', 'Api\EncargosController@verProductoDelEncargoIndividual'); // ver producto individual del encargo
    Route::post('usuario/encargos/actualizar-producto', 'Api\EncargosController@actualizarProductoDelEncargo'); // actualizar producto del encargo
    Route::post('usuario/encargos/cancelar-encargo', 'Api\EncargosController@cancelarEncargo'); // cancelar un encargo
    Route::post('usuario/encargos/completar-encargo', 'Api\EncargosController@completarEncargo'); // completar encargo


//});




 
