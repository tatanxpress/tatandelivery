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

// mejorado para version 1.18 android, para ver que tipo de cupon se ha aplicado 05/08/2020
Route::post('usuario/proceso/ver/ordenes-mejorado', 'Api\ProcesadorOrdenesController@verOrdenesMejorado');

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
Route::post('propietario/productos', 'Api\PropietarioController@verProductos'); // listado de productos vertical
Route::post('propietario/productos-h', 'Api\PropietarioController@verProductosHorizontal'); // listado de productos horizontal
Route::post('propietario/productos-h-seccion', 'Api\PropietarioController@buscarProductoSeccion'); // lista de productos "ver todos"
   
Route::post('propietario/producto/individual', 'Api\PropietarioController@verProductosIndividual'); // ver producto individual
Route::post('propietario/actualizar/producto', 'Api\PropietarioController@actualizarProducto'); // actualizar producto
Route::post('propietarios/buscar/producto', 'Api\PropietarioController@buscarProducto'); // locales tipo tienda
 
    //** Version 2 para Afiliados*/
 
Route::post('afiliado/productos/ver-categorias', 'Api\Afiliados\AfiliadosVersion2Controller@verCategoriasProductos'); // lista de categorias
Route::post('afiliado/productos/actualizar-categorias', 'Api\Afiliados\AfiliadosVersion2Controller@actualizarCategoria'); // actualizar categoria android
Route::post('afiliado/productos/actualizar-categorias-ios', 'Api\Afiliados\AfiliadosVersion2Controller@actualizarNombreCategoria'); // actualizar categoria ios
Route::post('afiliado/productos/categoria-activar-ios', 'Api\Afiliados\AfiliadosVersion2Controller@activarCategoria'); // activar categoria ios
Route::post('afiliado/productos/categoria-inactivar-ios', 'Api\Afiliados\AfiliadosVersion2Controller@desactivarCategoria'); // desactivar categoria ios
Route::post('afiliado/productos/categoria-editar-ios', 'Api\Afiliados\AfiliadosVersion2Controller@editarCategoria'); // desactivar categoria ios
Route::post('afiliado/categoria/actualizar-posiciones', 'Api\Afiliados\AfiliadosVersion2Controller@actualizarCategoriaPosiciones'); // cambiar nombre a categoria
Route::post('afiliado/categoria/actualizar-posiciones-ios', 'Api\Afiliados\AfiliadosVersion2Controller@actualizarCategoriaPosicionesIos'); // cambiar nombre a categoria
Route::post('afiliado/categoria/productos-lista', 'Api\Afiliados\AfiliadosVersion2Controller@productosDeCategoria'); // lista de productos por categoria
Route::post('afiliado/categoria/pro/actualizar-posiciones', 'Api\Afiliados\AfiliadosVersion2Controller@actualizarProductoPosiciones'); // cambiar nombre a categoria
Route::post('afiliado/categoria/pro/actualizar-posiciones-ios', 'Api\Afiliados\AfiliadosVersion2Controller@actualizarProductoPosicionesIos'); // cambiar nombre a categoria
Route::post('afiliado/actualizar/producto', 'Api\Afiliados\AfiliadosVersion2Controller@actualizarProducto'); // actualizar producto
 
// encargos
Route::post('afiliado/encargos/ver-mis-asignados', 'Api\Afiliados\AfiliadosVersion2Controller@verMisEncargos');
Route::post('afiliado/encargos/ver-mis-asignados-lista', 'Api\Afiliados\AfiliadosVersion2Controller@verOrdenesEncargosLista');
Route::post('afiliado/encargos/ocultar-tarjeta', 'Api\Afiliados\AfiliadosVersion2Controller@ocultarLaTarjetaEncargo');
Route::post('afiliado/encargos/iniciar/orden-encargo', 'Api\Afiliados\AfiliadosVersion2Controller@iniciarOrdenEncargoPropietario');
Route::post('afiliado/encargos/finalizar/orden-encargo', 'Api\Afiliados\AfiliadosVersion2Controller@finalizarOrdenEncargoPropietario');
Route::post('afiliado/encargos/orden/ver-productos', 'Api\Afiliados\AfiliadosVersion2Controller@verProductoOrdenEncargo');
Route::post('afiliado/encargos/orden/ver-productos-individual', 'Api\Afiliados\AfiliadosVersion2Controller@verProductoIndividualOrdenEncargo');

Route::post('afiliado/encargos/ver/finalizados-hoy', 'Api\Afiliados\AfiliadosVersion2Controller@verEncargosFinalizadosHoy');

Route::post('afiliado/encargos/ver/historial-encargos', 'Api\Afiliados\AfiliadosVersion2Controller@verHistorialDeEncargosFinalizados');


  

    //** Fin Version 2 para Afiliados */


 
 

 
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

// notificar al cliente que su orden esta cerca o afuera
Route::post('motorista/notificar/cliente/orden', 'Api\MotoristaController@notificarClienteOrden');

 
// Seccion encargos motoristas  
Route::post('motorista/encargo/nuevos', 'Api\MotoristaController@verNuevosOrdenesEncargos'); // ver nuevos encargos
Route::post('motorista/encargo/aceptar-entrega', 'Api\MotoristaController@aceptarOrdenEncargo'); // seleccionar el encargo
 
Route::post('motorista/encargo/en-proceso', 'Api\MotoristaController@verNuevosOrdenesEncargosProceso'); // ver encargos en proceso   
Route::post('motorista/encargo/en-proceso-estados', 'Api\MotoristaController@verNuevosOrdenesEncargosProcesoEstado'); // ver encargos en proceso   
Route::post('motorista/encargo/iniciar-entrega', 'Api\MotoristaController@iniciarEntregaEncargo');   
Route::post('motorista/encargo/borrar/vista-entrega', 'Api\MotoristaController@ocultarOrdenEncargoMotorista'); // oculta la orden que va iniciar porque se cancelo 
Route::post('motorista/encargo/lista-en-entrega', 'Api\MotoristaController@listaEncargosEnEntrega'); // lista de encargos en entrega

Route::post('motorista/encargo/ver-estado-finalizar', 'Api\MotoristaController@verEstadoOrdenEncargoAFinalizar'); // ver esado del encargo a finalizar
Route::post('motorista/encargo/finalizar-entrega', 'Api\MotoristaController@finalizarEntregaEncargo'); // finalizar entrega del encargo
Route::post('motorista/encargo/notificar-cliente', 'Api\MotoristaController@notificarClienteDelEncargo'); // mandar notificacion al cliente del encargo

Route::post('motorista/encargos/ver/historial-encargos', 'Api\MotoristaController@verHistorialEncargosCompletados'); // historial encargo cmpletado

Route::post('motorista/encargo/lista-productos', 'Api\MotoristaController@verListaDeProductosDeEncargo'); // ver lista de producto del encargo   
Route::post('motorista/encargo/lista-productos-individual', 'Api\MotoristaController@verListaDeProductosDeEncargoIndividual'); // ver producto del encargo individual 






 




  
 
   
// REVISADOR DE PAGOS 

Route::post('revisador/login', 'Api\PagaderoController@loginRevisador'); // login revisador
Route::post('revisador/actualizar/password', 'Api\PagaderoController@reseteo'); // cambiar contrasena el revisador
Route::post('revisador/pendiente/pago', 'Api\PagaderoController@pendientePago');// ver ordenes pendiente de pago
Route::post('revisador/confirmar/pago', 'Api\PagaderoController@confirmarPago'); // confirmar revisador
 
Route::post('revisador/pendiente-encargo/pago', 'Api\PagaderoController@pendienteEncargoPago');// ver ordenes encargo pendiente de pago
Route::post('revisador/confirmar/pago-encargo', 'Api\PagaderoController@confirmarPagoEncargo'); // confirmar revisador


Route::post('revisador/ver/motoristas', 'Api\PagaderoController@verMotoristas'); // ver motorista
  
// DESACTUALIZADA 22/07/2020
//Route::post('revisador/ver/historial', 'Api\PagaderoController@verHistorial'); // ver historial de ordenes
 
Route::post('revisador/ver/historial', 'Api\PagaderoController@verHistorialNuevo'); // ver historial de ordenes
Route::post('revisador/ver/historial-encargos', 'Api\PagaderoController@verHistorialEncargos'); // ver historial de encargos

// No utilizado 08/08/2020
Route::post('revisador/ver/fecharecorte', 'Api\BitacoraRevisadorController@verFechaRecorte'); // ver fecha de recorte de caja
 
// revisar todas las ordenes pendientes de un motorista 
Route::post('revisador/confirmar/todos/pago', 'Api\BitacoraRevisadorController@revisarTodos');


// APP ADMINISTRADORES

Route::post('adminapp/login', 'Api\AdminAppController@loginRevisador'); // login administrador
Route::post('adminapp/actualizar/password', 'Api\AdminAppController@reseteo'); // cambio de contrasena

Route::post('adminapp/ordenes/hoy', 'Api\AdminAppController@ordenesHoy'); // solo ordenes de hoy
Route::post('adminapp/encargos/ordenes/hoy', 'Api\AdminAppController@ordenesEncargoHoy'); // solo ordenes de hoy
  
// ordenes sin contestar con 1 minutos extra
Route::post('adminapp/ordenes/nocontestadas', 'Api\AdminAppController@verOrdenesSinContestacion'); // ordenes sin contestacion
Route::post('adminapp/ordenes/ocultar/nocontestadas', 'Api\AdminAppController@ocultarOrdenSinContestacion');// ocultar una orden de ordenes sin contestacion
 
// paso la mitar del tiempo que el propietario dijo que entregaria la orden
// y ningun motorista agarrado esta orden
Route::post('adminapp/ordenes/urgentes-cuatro', 'Api\AdminAppController@verOrdenesUrgenteCuatro'); // ver ordenes_urgentes_cuatro
Route::post('adminapp/urgentes/cuatro/ocultar', 'Api\AdminAppController@ocultarOrdenesCuatro'); // ocultar ordenes_urgentes_cuatro
 

// ordenes completadas, aun no sale motorista, ya paso la hora estimada de entrega que dio el propietario + 2 min extra. 
Route::post('adminapp/ordenes/urgentes-uno', 'Api\AdminAppController@verOrdenesUrgenteUno'); // ver ordenes_urgentes_uno
Route::post('adminapp/urgentes/uno/ocultar', 'Api\AdminAppController@ocultarUrgenteUno'); // ocultar ordenes_urgentes_uno 

// propietario termino de preparar la orden y ningun motorista agarro la orden
Route::post('adminapp/ordenes/urgentes-dos', 'Api\AdminAppController@verOrdenesUrgenteDos'); // ver ordenes_urgentes_dos
Route::post('adminapp/urgentes/dos/ocultar', 'Api\AdminAppController@ocultarUrgenteDos'); // ocultar ordenes_urgentes_dos 

// paso el tiempo hora de entrega del cliente sumandole + 5 minutos
// hora que dio el propietario + hora extra de zona + 5 minutos 
Route::post('adminapp/ordenes/urgentes-tres', 'Api\AdminAppController@verOrdenesUrgenteTres'); // ver ordenes_urgentes_tres
Route::post('adminapp/urgentes/tres/ocultar', 'Api\AdminAppController@ocultarUrgenteTres'); // ocultar ordenes_urgentes_tres 

/* los 4 estados que puede tener problemas una orden
1- propietario ninguno disponible en una nueva orden
2- orden nueva y no hay motorista disponible para este servicio
3- orden inicio preparacion y no tiene motoristas disponibles
4- orden termino de prepararse y no tiene motorista asignado esta orden
*/ 

Route::post('adminapp/ordenes/estado-problema', 'Api\AdminAppController@verEstados'); // ver estados de problema de ordenes
Route::post('adminapp/ocultar/estado-problema', 'Api\AdminAppController@ocultarEstados'); // ocultar estados
Route::post('adminapp/ver/productos-ordenes', 'Api\AdminAppController@verProductosOrden'); // ver productos de la orden
Route::post('adminapp/ver/productos-encargos', 'Api\AdminAppController@verProductosOrdenEncargo'); // ver productos del encargo

 
// ver servicios lista
Route::post('adminapp/ver/lista-servicios', 'Api\AdminAppController@verListaServicios');
Route::post('adminapp/ver/lista-servicios-propietarios', 'Api\AdminAppController@verListaServiciosPropietarios');
Route::post('adminapp/ver/notificacion/propietario', 'Api\AdminAppController@enviarNotificacionPropietario');
Route::post('adminapp/ver/lista-motoristas', 'Api\AdminAppController@verListaMotoristas');
Route::post('adminapp/ver/notificacion/motorista', 'Api\AdminAppController@enviarNotificacionMotorista');


// obtener lista de imagenes de un producto
Route::post('usuario/producto/lista-de-imagenes', 'Api\ServiciosController@obtenerListaDeImagenes');
Route::post('usuario/producto/video-url', 'Api\ServiciosController@obtenerVideoProducto');






Route::get('bienes-servicios', 'Api\Soap\SoapController@BienesServicios');
