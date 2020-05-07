<?php

use Illuminate\Http\Request;
 
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::post('ejemplo', 'Api\Auth\LoginController@ejemplo');
// USUARIOS  
Route::post('verificar/telefono', 'Api\Auth\LoginController@verificarNumero'); // verificar si el telefono esta registrado
Route::post('verificar-codigo-temporal', 'Api\Auth\LoginController@verificarCodigoTemporal'); // verificar telefono + codigo temporal.
Route::post('usuario/login', 'Api\Auth\LoginController@loginUsuario'); // login usuario
Route::post('usuario/codigo-correo', 'Api\Auth\LoginController@codigoCorreo'); // enviar codigo al correo para recuperacion
Route::post('usuario/revisar-codigo', 'Api\Auth\LoginController@revisarCodigoCorreo'); // revisar codigo del correo
Route::post('usuario/registro', 'Api\Auth\RegisterController@registroUsuario'); // registro usuario
Route::post('usuario/nueva-password', 'Api\PerfilController@nuevaPassword'); // cambia contraseña con correo
   
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
Route::post('usuario/servicios/lista', 'Api\ServiciosController@getServiciosZona'); // lista de tipo servicio por zona
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
Route::post('usuario/proceso/orden/productos', 'Api\ProcesadorOrdenesController@ordenProductos'); // ver lista de producto de orden
Route::post('usuario/proceso/orden/producto/individual', 'Api\ProcesadorOrdenesController@ordenProductosIndividual'); // ver producto de la orden
Route::post('usuario/proceso/ver/motorista', 'Api\ProcesadorOrdenesController@motoristaAsignado'); // ver motorista de la orden
Route::post('usuario/proceso/calificar/motorista', 'Api\ProcesadorOrdenesController@calificarMotorista'); // calificar motorista
  
Route::post('usuario/proceso/orden/cancelar', 'Api\ProcesadorOrdenesController@cancelarOrden'); // cancelar orden 
Route::post('usuario/proceso/borrar/vista/orden', 'Api\ProcesadorOrdenesController@borrarVistaOrden'); // borrar vista
 

Route::post('usuario/proceso/orden/estado-1', 'Api\ProcesadorOrdenesController@procesarOrdenEstado1'); // procesar orden primer paso *
Route::post('usuario/proceso/orden/estado-3', 'Api\ProcesadorOrdenesController@procesarOrdenEstado3'); // procesar orden tercer paso *
    
Route::post('usuario/verificar/cupon', 'Api\ProcesadorOrdenesController@verificarCupon'); // procesar orden primer paso *
 
 
  
// PROPIETARIOS  
  
Route::post('propietario/login', 'Api\PropietarioController@loginPropietario'); // login propietario 
Route::post('propietario/password/recuperacion', 'Api\PropietarioController@codigoCorreo'); // enviar codigo recuperacion
Route::post('propietario/revisar/codigo', 'Api\PropietarioController@revisarCodigoCorreo'); // revisar codigo correo
Route::post('propietario/cambiar/password', 'Api\PropietarioController@nuevaPassword'); // cambio de contraseña
Route::post('propietario/buscar/telefono', 'Api\PropietarioController@buscarTelefono'); // buscar telefono
  
// nuevas ordenes 
Route::post('propietario/nueva/ordenes', 'Api\PropietarioController@nuevaOrdenes'); // ver nuevas ordenes
Route::post('propietario/ver/productos', 'Api\PropietarioController@verProductosOrden'); // ver productos de la orden
Route::post('propietario/ver/producto/individual', 'Api\PropietarioController@ordenProductosIndividual'); // ver producto individual de la orden
Route::post('propietario/ver/orden/id', 'Api\PropietarioController@verOrdenPorID'); // ver estados orden por id
   
// procesar ordenes 
Route::post('propietario/proceso/orden/estado-2', 'Api\PropietarioController@procesarOrdenEstado2'); // dar tiempo de espera *
Route::post('propietario/proceso/orden/estado-4', 'Api\PropietarioController@procesarOrdenEstado4'); // iniciar preparacion, avisa a motoristas
  
// ordenes en proceso
Route::post('propietario/preparando/ordenes', 'Api\PropietarioController@preparandoOrdenes'); // ver nuevas ordenes de prepracion
Route::post('propietario/ver/preparando/orden/id', 'Api\PropietarioController@verOrdenPreparandoPorID'); // ver estados orden preparando por id
Route::post('propietario/finalizar/orden', 'Api\PropietarioController@finalizarOrdenFinal'); // finalizar orden propietario
 
// cancelacion de ordenes
Route::post('propietario/cancelar/orden', 'Api\PropietarioController@cancelarOrden'); // cancelar orden
Route::post('propietario/borrar/orden', 'Api\PropietarioController@borrarOrden'); // borrar vista

// cancelar orden extraordinariamente
Route::post('propietario/cancelar/extraordinario', 'Api\PropietarioController@cancelarOrdenExtraordinariamente'); // cancelar orden extraordinariamente
  
 
Route::post('propietario/ver/pago', 'Api\PropietarioController@verPagosCompletos'); // ver ordenes completadas
Route::post('propietario/ver/pago/canceladas', 'Api\PropietarioController@verPagosCancelados'); // ver ordenes completadas
Route::post('propietario/completadas/hoy', 'Api\PropietarioController@verCompletadasHoy'); // ver ordenes completadas hoy
    
 
// configuraciones
Route::post('propietario/horarios', 'Api\PropietarioController@verHorarios'); // ver horarios 
Route::post('propietario/info/disponibilidad', 'Api\PropietarioController@informacionDisponibilidad'); // informacion de disponibilidad
Route::post('propietario/guadar/configuracion', 'Api\PropietarioController@modificarDisponibilidad'); // guarda configuracion
Route::post('propietario/info/cuenta', 'Api\PropietarioController@informacionCuenta'); // informacion cuenta
Route::post('propietario/cambiar/correo', 'Api\PropietarioController@cambiarCorreo'); // cambiar correo cuenta
Route::post('propietario/actualizar/password', 'Api\PropietarioController@actualizarPassword'); // actualizar contraseña
Route::post('propietarios/estado/adomicilio', 'Api\PropietarioController@estadoAdomicilio'); // ver estado de adomicilio
Route::post('propietarios/estado/tiempo', 'Api\PropietarioController@estadoAutomatico'); // estado automatico de ordenes
Route::post('propietario/guardar/tiempo', 'Api\PropietarioController@guardarTiempo'); // guarda tiempo para la orden automatica o no
Route::post('propietarios/zonas/cobertura', 'Api\PropietarioController@zonaCobertura'); // lista de zonas que da cobertura este servicio
Route::post('propietarios/zonas/informacion', 'Api\PropietarioController@informacionZona'); //info de la zona que modificara el propietario
Route::post('propietarios/zonas/actualizar/zonahora', 'Api\PropietarioController@actualizarZonaHora'); //info de la zona que modificara el propietario
Route::post('propietarios/zonas/mapa', 'Api\PropietarioController@verMapaZona'); //info de la zona que modificara el propietario


// productos 
Route::post('propietario/productos', 'Api\PropietarioController@verProductos'); // listado de productos
Route::post('propietario/producto/individual', 'Api\PropietarioController@verProductosIndividual'); // ver producto individual
Route::post('propietario/actualizar/producto', 'Api\PropietarioController@actualizarProducto'); // actualizar producto
Route::post('propietarios/buscar/producto', 'Api\PropietarioController@buscarProducto'); // locales tipo tienda
 
 
// MOTORISTA  
 
Route::post('motorista/login', 'Api\MotoristaController@loginMotorista'); // login motorista
Route::post('motorista/password/recuperacion', 'Api\MotoristaController@codigoCorreo'); // enviar codigo recuperacion
Route::post('motorista/revisar/codigo', 'Api\MotoristaController@revisarCodigoCorreo'); // revisar codigo correo
Route::post('motorista/cambiar/password', 'Api\MotoristaController@nuevaPassword'); // cambio de contraseña
Route::post('motorista/buscar/telefono', 'Api\MotoristaController@buscarTelefono'); // buscar telefono
   
// nueva ordenes 
Route::post('motorista/nueva/ordenes', 'Api\MotoristaController@nuevaOrdenes'); // ver nuevas ordenes
Route::post('motorista/ver/orden/id', 'Api\MotoristaController@verOrdenPorID'); // ver estados orden por id
Route::post('motorista/ver/productos', 'Api\MotoristaController@verProductosOrden'); // ver productos de la orden
Route::post('motorista/ver/producto/individual', 'Api\MotoristaController@ordenProductosIndividual'); // ver producto individual de la orden
Route::post('motorista/obtener/orden', 'Api\MotoristaController@obtenerOrden'); // motorista obtiene la orden
 
// orden proceso
Route::post('motorista/orden/proceso', 'Api\MotoristaController@verProcesoOrdenes'); // ver orden en proceso
Route::post('motorista/orden/procesoentrega', 'Api\MotoristaController@verProcesoOrdenesEntrega'); // ver orden en proce
  
Route::post('motorista/ver/orden/proceso/id', 'Api\MotoristaController@verOrdenProcesoPorID'); // ver estados orden proceso por id
Route::post('motorista/iniciar/entrega', 'Api\MotoristaController@iniciarEntrega'); // iniciar entrega de la orden
Route::post('motorista/finalizar/entrega', 'Api\MotoristaController@finalizarEntrega'); // finalizar entrega de la orden
Route::post('motorista/borrar/orden/cancelada', 'Api\MotoristaController@borrarOrdenCancelada'); // borrar orden cancelada
   
Route::post('motorista/info/cuenta', 'Api\MotoristaController@informacionCuenta'); // informacion cuenta
Route::post('motorista/info/disponibilidad', 'Api\MotoristaController@informacionDisponibilidad'); // informacion de disponibilidad
Route::post('motorista/cambiar/correo', 'Api\MotoristaController@cambiarCorreo'); // cambiar correo cuenta
Route::post('motorista/guadar/configuracion', 'Api\MotoristaController@modificarDisponibilidad'); // guarda configuracion
Route::post('motorista/actualizar/password', 'Api\MotoristaController@actualizarPassword'); // actualizar contraseña
 
Route::post('motorista/ver/historial', 'Api\MotoristaController@verHistorial'); // ver historial*/


// ordenes pendiente de pagar
Route::post('motorista/pendiente/pago', 'Api\MotoristaController@pendientePago'); // pendientes de pago

 
// REVISADOR DE PAGOS

Route::post('revisador/login', 'Api\PagaderoController@loginRevisador'); // login revisador
 
// cambiar contrasena el revisador
Route::post('revisador/actualizar/password', 'Api\PagaderoController@reseteo'); // login revisador

 
// ver ordenes pendiente de pago
Route::post('revisador/pendiente/pago', 'Api\PagaderoController@pendientePago'); // 
 
// confirmar pago
Route::post('revisador/confirmar/pago', 'Api\PagaderoController@confirmarPago'); // confirmar revisador

// extras
Route::post('revisador/ver/motoristas', 'Api\PagaderoController@verMotoristas'); // ver motorista

// historial de una fecha a otra 
Route::post('revisador/ver/historial', 'Api\PagaderoController@verHistorial'); // ver historial
 
// ver fecha de recorte
Route::post('revisador/ver/fecharecorte', 'Api\BitacoraRevisadorController@verFechaRecorte'); // ver fecha de recorte de caja


// APP ADMINISTRADORES
Route::post('adminapp/login', 'Api\AdminAppController@loginRevisador'); // login revisador

// ordenes del estado 1-4
Route::post('adminapp/ordenes/urgente', 'Api\AdminAppController@verOrdenesUrgente'); 
 
// ocultar una orden de ordenes_pendiente
Route::post('adminapp/ordenes/ocultar', 'Api\AdminAppController@ocultar'); 

// ver ordenes de solo hoy
Route::post('adminapp/ordenes/hoy', 'Api\AdminAppController@ordenesHoy');
 
// ordenes urgente, tarea programada
Route::post('adminapp/ordenes/programada', 'Api\AdminAppController@verOrdenesProgramada');

// ocultar una orden de ordenes_urgentes
Route::post('adminapp/ordenes/pro/ocultar', 'Api\AdminAppController@ocultarurgente');
  
 
// ordenes sin contestacion, tarea programada
Route::post('adminapp/ordenes/nocontestadas', 'Api\AdminAppController@verOrdenesSinContestacion');

// ocultar una orden de ordenes sin contestacion
Route::post('adminapp/ordenes/ocultar/nocontestadas', 'Api\AdminAppController@ocultarOrdenSinContestacion');


// cambiar contrasena el administrador
Route::post('adminapp/actualizar/password', 'Api\AdminAppController@reseteo'); 

 
