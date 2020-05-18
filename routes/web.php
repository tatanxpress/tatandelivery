<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

 
Route::get('/', 'FrontendController@index')->name('inicio'); // inicio del sitio web
Route::get('/preguntas-frecuentes', 'FrontendController@verFAQ'); // pagina de preguntas
Route::get('/terminos-condiciones', 'FrontendController@verTerminos'); // pagina de preguntas

// Rutas administrador
 
Route::get('admin/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');  // pagina inicio de sesion admin
Route::post('admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout'); // cerrado de sesion
Route::post('/admin', 'Auth\AdminLoginController@login')->name('admin.login.submit'); // inicio de sesion

  // RUTA ADMINISTRADOR 
  Route::prefix('admin')->group(function () {  
  Route::get('/', 'DashboardController@index')->name('admin.dashboard'); // inicio del panel de control
  Route::get('/inicio', 'DashboardController@getInicio')->name('admin.inicio'); // carga la primera pagina 
  // mapa zonas
  Route::get('/zona/mapa/zona', 'MapaZonaController@index');
  Route::get('/zona/tablas/zona', 'MapaZonaController@zonatabla');
  Route::post('/zona/informacion-zona','MapaZonaController@informacionZona');
  Route::post('/zona/nueva-zona','MapaZonaController@nuevaZona'); 
  Route::post('/zona/editar-zona','MapaZonaController@editarZona');
  Route::post('/zona/nuevo-poligono','MapaZonaController@crearPoligono');
  Route::post('/zona/borrar-poligono','MapaZonaController@borrarPoligono');
  Route::get('/zona/ver-poligonos/{id}', 'MapaZonaController@verMapa');
  // tipos
  Route::get('/tipos/lista-tipos', 'TipoServicioController@index2');
  Route::get('/tipos/tablas/lista-tipos', 'TipoServicioController@tipostabla');
  Route::post('/tipos/nuevo','TipoServicioController@nuevoTipos');
  Route::post('/tipos/informacion','TipoServicioController@informacionTipos'); 
  Route::post('/tipos/editar-tipos','TipoServicioController@editarTipos');
  // tipo servicios 
  Route::get('/tiposervicio/lista-tipo-servicio', 'TipoServicioController@index');
  Route::get('/tiposervicio/tablas/lista-tipo-servicio', 'TipoServicioController@serviciotabla');
  Route::post('/tiposervicio/nuevo','TipoServicioController@nuevoTipoServicio');
  Route::post('/tiposervicio/informacion','TipoServicioController@informacionTipo'); 
  Route::post('/tiposervicio/editar-tipo','TipoServicioController@editarTipo');
  // tipo servicio zonas 
  Route::get('/tiposerviciozona/lista-tipo-servicio-zona', 'TipoServicioZonaController@index');
  Route::get('/tiposerviciozona/tablas/lista-tipo-servicio-zona', 'TipoServicioZonaController@serviciotabla');
  Route::post('/tiposerviciozona/buscar/servicio', 'TipoServicioZonaController@buscarServicio');
  Route::post('/tiposerviciozona/nuevo', 'TipoServicioZonaController@nuevoTipoServicioZona');
  Route::post('/tiposerviciozona/informacion','TipoServicioZonaController@informacionTipoZona');
  Route::post('/tiposerviciozona/editar-tipo','TipoServicioZonaController@editarTipo');
  Route::get('/tiposerviciozona/{id}', 'TipoServicioZonaController@filtrado'); // filtrado
  Route::get('/tiposerviciozona/tabla/{id}', 'TipoServicioZonaController@tablaFiltrado'); // tabla filtrado
  Route::post('/tiposerviciozona/ordenar', 'TipoServicioZonaController@ordenar');     
  // clientes 
  Route::get('/cliente/lista-clientes', 'ClientesController@index');
  Route::get('/cliente/tablas/cliente', 'ClientesController@clienteTabla');
  Route::post('/cliente/informacion','ClientesController@informacion');
  Route::post('/cliente/editar','ClientesController@editar');
  Route::get('/cliente/direcciones/{id}', 'ClientesController@direccionesCliente');
  Route::get('/cliente/tablas/direccion/{id}', 'ClientesController@direccionesTabla'); 
  Route::post('/cliente/direcciones/informacion','ClientesController@infoDireccion');
  Route::get('/cliente/ubicacion/{id}', 'ClientesController@clienteUbicacion');

  // servicios locales
  Route::get('/servicios/lista', 'ServiciosController@index'); 
  Route::get('/servicios/tabla/lista', 'ServiciosController@serviciotabla');
  Route::post('/servicios/nuevo', 'ServiciosController@nuevo'); 
  Route::post('/servicios/informacion/servicio', 'ServiciosController@informacionServicio');
  Route::post('/servicios/informacion-horario/servicio', 'ServiciosController@informacionHorario');
  Route::post('/servicios/editar-servicio', 'ServiciosController@editarServicio');
  Route::post('/servicios/editar-horas', 'ServiciosController@editarHoras'); 
  Route::get('/servicios/mapa/ubicacion/{id}', 'ServiciosController@servicioUbicacion'); 
  // categorias menu 
  Route::get('/categorias/{id}', 'CategoriasController@index'); 
  Route::get('/categorias/tablas/{id}', 'CategoriasController@tablaCategorias');
  Route::post('/categorias/nuevo', 'CategoriasController@nuevo');
  Route::post('/categorias/informacion', 'CategoriasController@informacion');
  Route::post('/categorias/editar', 'CategoriasController@editar');
  Route::post('/categorias/ordenar', 'CategoriasController@ordenar'); 
  // zonas servicio  
  Route::get('/zonaservicios/lista', 'ZonaServiciosController@index');
  Route::get('/zonaservicios/tabla/lista', 'ZonaServiciosController@serviciotabla');
  Route::post('/zonaservicios/nuevo', 'ZonaServiciosController@nuevo');
  Route::post('/zonaservicios/informacion', 'ZonaServiciosController@informacion');
  Route::post('/zonaservicios/editar', 'ZonaServiciosController@editarServicio');
  Route::get('/zonaservicios/{id}/{id1}', 'ZonaServiciosController@filtrado');
  Route::get('/zonaservicios/tabla/{id}/{id1}', 'ZonaServiciosController@tablaFiltrado');
  Route::post('/zonaservicios/ordenar', 'ZonaServiciosController@ordenar'); 
  Route::post('/zonaservicios/enviogratis', 'ZonaServiciosController@setearEnvioGratis'); 
  Route::post('/zonaservicios/mitadprecio', 'ZonaServiciosController@setearMitadPrecio'); 
  
     
  // productos 
  Route::get('/productos/{id}', 'ProductoController@index');  
  Route::get('/productos/tablas/{id}', 'ProductoController@tablaProductos');
  Route::post('/productos/nuevo', 'ProductoController@nuevo');
  Route::post('/productos/informacion', 'ProductoController@informacion');
  Route::post('/productos/editar', 'ProductoController@editar');
  Route::post('/productos/ordenar', 'ProductoController@ordenar'); 
  // publicidad
  Route::get('/publicidad/lista', 'PublicidadController@index');
  Route::get('/publicidad/tabla/lista', 'PublicidadController@publicidadtabla');
  Route::get('/publicidad/lista-inactivo', 'PublicidadController@indexinactivo');
  Route::get('/publicidad/tabla/lista-inactivo', 'PublicidadController@publicidadtablainactivo');
  Route::post('/publicidad/nuevo-promocion', 'PublicidadController@nuevoPromocion');
  Route::post('/publicidad/nuevo-publicidad', 'PublicidadController@nuevoPublicidad');
  Route::post('/publicidad/informacion', 'PublicidadController@informacion'); 
  Route::post('/publicidad/editar-promo', 'PublicidadController@editarPromo');
  Route::post('/publicidad/editar-publi', 'PublicidadController@editarPubli');
  // zona publicidad
  Route::get('/zonapublicidad/lista', 'ZonaPublicidadController@index');
  Route::get('/zonapublicidad/tabla/lista', 'ZonaPublicidadController@tablazona');
  Route::post('/zonapublicidad/nuevo', 'ZonaPublicidadController@nuevo'); 
  Route::post('/zonapublicidad/borrar', 'ZonaPublicidadController@borrar');
  Route::get('/zonapublicidad/{id}', 'ZonaPublicidadController@filtrado');
  Route::get('/zonapublicidad/tabla/{id}', 'ZonaPublicidadController@tablaFiltrado');
  Route::post('/zonapublicidad/ordenar', 'ZonaPublicidadController@ordenar'); 
  // producto a promocion 
  Route::get('/productopromocion/{id}/{id1}', 'ProductoPublicidadController@index');
  Route::get('/promo/tablas/{id}', 'ProductoPublicidadController@productoPromocion');
  Route::get('/promo/producto/{id}/{id2}', 'ProductoPublicidadController@index2');  
  Route::get('/pr/producto/tablas/{id}', 'ProductoPublicidadController@productoServicio');
  Route::post('/productopromocion/borrar', 'ProductoPublicidadController@borrar');
  Route::post('/productopromocion/revision', 'ProductoPublicidadController@revision');
  Route::post('/promo/producto/nuevo', 'ProductoPublicidadController@nuevo');
  // propietarios 
  Route::get('/propietarios/lista', 'PropiController@index');
  Route::get('/propietarios/tabla/lista', 'PropiController@propitabla');
  Route::post('/propietarios/nuevo', 'PropiController@nuevo'); 
  Route::post('/propietarios/informacion', 'PropiController@informacion');
  Route::post('/propietarios/editar', 'PropiController@editar');
  // motoristas   
  Route::get('/motoristas/lista', 'MotoristaController@index');
  Route::get('/motoristas/tabla/lista', 'MotoristaController@mototabla');
  Route::post('/motoristas/nuevo', 'MotoristaController@nuevo');
  Route::post('/motoristas/informacion', 'MotoristaController@informacion');
  Route::post('/motoristas/editar', 'MotoristaController@editar');
  Route::post('/motoristas/promedio', 'MotoristaController@promedio');
 
  // motorista servicios
  Route::get('/motoristasservicio/lista', 'MotoristaController@index2');
  Route::get('/motoristasservicio/tabla/lista', 'MotoristaController@motoserviciotabla');
  Route::post('/motoristasservicio/borrar', 'MotoristaController@borrar');
  Route::post('/motoristasservicio/nuevo', 'MotoristaController@nuevomotoservicio');
  // revisadores de ordenes 
  Route::get('/revisador/lista', 'RevisadorController@index');
  Route::get('/revisador/tabla/lista', 'RevisadorController@revisadortabla');
  Route::post('/revisador/nuevo', 'RevisadorController@nuevo'); 
  Route::post('/revisador/reseteo', 'RevisadorController@reseteo');
  Route::post('/revisador/informacion', 'RevisadorController@informacion'); 
  Route::post('/revisador/editar', 'RevisadorController@editar');
  // revisador motorista 
  Route::get('/revisadormoto/lista', 'RevisadorController@index2');
  Route::get('/revisadormoto/tabla/lista', 'RevisadorController@revisadormototabla');
  Route::post('/revisadormoto/nuevo', 'RevisadorController@nuevomoto');
  Route::post('/revisadormoto/borrar', 'RevisadorController@borrar');
  // revisador bitacora
  Route::get('/revisadorbitacora/lista', 'RevisadorController@index3');
  Route::get('/revisadorbitacora/tabla/lista', 'RevisadorController@revisadorbitacoratabla');
  Route::post('/revisadorbitacora/nuevo', 'RevisadorController@nuevabitacora');
  Route::post('/revisadorbitacora/informacion', 'RevisadorController@infobitacora');
  Route::post('/revisadorbitacora/editar', 'RevisadorController@editarbitacora');
  // ver ordenes
  Route::get('/ordenes/lista', 'OrdenesController@index');
  Route::get('/ordenes/tabla/lista', 'OrdenesController@tablaorden'); // ultimas 100 ordenes
  //Route::post('/ordenes/informacion', 'OrdenesController@informacion'); // no utilizada
  Route::get('/ordenes/ubicacion/{id}', 'OrdenesController@entregaUbicacion'); 
  Route::get('/ordenes/listaproducto/{id}', 'OrdenesController@listaproducto'); 
  Route::get('/ordenes/tabla/producto/{id}', 'OrdenesController@productos'); 
  // motorista ordenes   
  Route::get('/motoorden/lista', 'OrdenesController@index2');
  Route::get('/motoorden/tabla/lista', 'OrdenesController@tablamotoorden');
  // experiencia 
  Route::get('/motoexpe/lista', 'OrdenesController@index3');
  Route::get('/motoexpe/tabla/lista', 'OrdenesController@tablamotoexpe');
  // buscador ordenes de un motorista especifico
  Route::get('/buscar/moto/ordenes', 'OrdenesController@index4'); 
  Route::get('/buscar/moor/{id}/{id1}/{id2}', 'OrdenesController@buscador');  // buscar ordenes motorista
  Route::get('/buscar2/moor/{id}/{id1}/{id2}', 'OrdenesController@buscador2');
  
  // calificacion global del motorista
  Route::post('/moto/calficacion-global', 'OrdenesController@calificacionGlobal');
  

  // buscar numero de orden
  Route::get('/buscar/numero/orden', 'OrdenesController@index6');
  Route::get('/buscar/num/orden/{id}', 'OrdenesController@buscarNumOrden');

  Route::post('/buscar3/orden/servicio', 'OrdenesController@buscador3');

  // informacion de las ordenes
  Route::post('/buscar/orden/infocliente', 'OrdenesController@informacioncliente');
  Route::post('/buscar/orden/infoorden', 'OrdenesController@informacionorden');
  Route::post('/buscar/orden/infocargo', 'OrdenesController@informacioncargo');
  Route::post('/buscar/orden/infomotorista', 'OrdenesController@informacionmotorista');
  Route::post('/buscar/orden/infotipocargo', 'OrdenesController@informaciontipocargo');
 
   

  Route::post('/buscar/orden/filtraje', 'OrdenesController@filtro');
  Route::get('/generar/reporte2/{id}/{id2}/{id3}', 'OrdenesController@reporte1'); // reporte de pago a motoristas
  // guardar registro de pago motorista
  Route::get('/motopago/lista', 'MotoristaPagoController@index');
  Route::get('/motopago/tabla/lista', 'MotoristaPagoController@tablapago'); // datos de pagos a motoristas
  Route::post('/registro/pago/motorista', 'MotoristaPagoController@nuevo');
  Route::post('/motopago/pago/ver', 'MotoristaPagoController@totalpagadomotorista');
    
  // pago a servicios  
  Route::get('/pagoservicios/lista', 'MotoristaPagoController@index2');  
  Route::get('/buscarservicio/{id}/{id1}/{id2}/{d3}', 'MotoristaPagoController@buscador'); // buscar ordenes completas del servicio
  Route::get('/generar/reporte3/{id}/{id2}/{id3}/{d4}', 'MotoristaPagoController@reporte'); // reporte de ordenes completas
  Route::get('/generar/reporte4/{id}/{id2}/{id3}', 'MotoristaPagoController@reporte2');
  Route::get('/generar/reporte5/{id}/{id2}/{id3}', 'MotoristaPagoController@reporteordencancelada');
  Route::get('/generar/reporte7/{id}/{id2}/{id3}', 'MotoristaPagoController@reporteproductovendido'); // reporte de productos vendidos

     
  // reporte por tipos de cargo de envio
  Route::get('/generar/tipocargo/{id}/{id2}/{id3}/{d4}', 'MotoristaPagoController@reporteTipoCargoRevuelto'); // servicio uso min de $$ para envio gratis
  

  // ver ordenes revisadas 
  Route::get('/ordenrevisada/lista', 'MotoristaPagoController@index3'); 
  Route::get('/ordenrevisada/{id}/{id1}/{id2}', 'MotoristaPagoController@buscarOrdenRevisada');
  Route::get('/ordenrevisada2/{id}', 'MotoristaPagoController@buscarOrdenRevisada2');
  Route::get('/ordenrevisada3/{id}/{id1}/{id2}', 'MotoristaPagoController@reporteordenrevisada');
  // ordenes pendiente sin motorista
  Route::get('/ordenpendite/lista', 'OrdenesController@index5');
  Route::get('/ordenpendite/tabla/lista', 'OrdenesController@tablaordenpendiente');
  Route::post('/ocultar/ordenpendiente', 'OrdenesController@ocultarordenpendiente');
  // guardar registro de pago servicios
  Route::get('/serviciopago/lista', 'MotoristaPagoController@index4'); 
  Route::get('/serviciopago/tabla/lista', 'MotoristaPagoController@tablapagoservicio');
  Route::post('/registro/pago/servicio', 'MotoristaPagoController@nuevopagoservicio');
  Route::post('/registro/pago/ver', 'MotoristaPagoController@totalpagadoservicio'); 
  
  // agregar administradores para revision de ordenes
  Route::get('/adminrevisador/lista', 'AdminController@index3'); 
  Route::get('/adminrevisador/tabla/lista', 'AdminController@tablaadminrevisador');
  Route::post('/adminrevisador/informacion', 'AdminController@informacion');
  Route::post('/adminrevisador/nuevo', 'AdminController@nuevoadmin');
  Route::post('/adminrevisador/editar', 'AdminController@editar');  
  Route::post('/adminrevisador/reseteo', 'AdminController@reseteo'); 
  // editar datos de administrador
  Route::get('/editarinfo', 'AdminController@index4'); 
  Route::post('/editar-datos', 'AdminController@editardatos');
  Route::post('/editar-password', 'AdminController@editarpassword');
  // registro de publicidad y promocion para reportes. 
  Route::get('/registropromo/lista', 'ZonaPublicidadController@index2'); 
  Route::get('/registropromo/tabla/lista', 'ZonaPublicidadController@tablaregistropromo');
  Route::post('/registropromo/nuevo', 'ZonaPublicidadController@nuevoregistro');
  Route::get('/registropromo/reporte/{id}/{id2}', 'ZonaPublicidadController@reporte');
  Route::get('/registropromo/reporte2/{id}', 'ZonaPublicidadController@reporte2'); // buscar promo por vencer
  Route::post('/registropromo/informacion', 'ZonaPublicidadController@informacion');
  Route::post('/registropromo/editar', 'ZonaPublicidadController@editar');
  
  // cancelacion de una orden por panel de control
  Route::post('/cancelarorden/panel', 'OrdenesController@cancelarOrdenPanel');

  // configuracion
  Route::get('/dinero/limite', 'ConfiguracionesController@index'); 
  Route::post('/dinero/limite/informacion', 'ConfiguracionesController@informacion');
  Route::post('/dinero/limite/actualizar', 'ConfiguracionesController@actualizar');

  // numeros temporales
  Route::get('/numeros/temporales', 'ClientesController@index2'); 
  Route::get('/numeros/tabla/temporales', 'ClientesController@tablaTemporales'); 
  Route::post('/numeros/nuevo/registro', 'ClientesController@nuevoRegistro');
  Route::post('/numeros/informacion', 'ClientesController@infoNumTemporal');
  Route::post('/numeros/editar', 'ClientesController@editarRegistro');

  // lista de tipos de cupones
  Route::get('/cupones/tipo/lista', 'CuponesController@indextipo'); 
  Route::get('/cupones/tipo/tabla/lista', 'CuponesController@tablatipocupones'); 

  // lista de cupones para envio gratis
  Route::get('/cupones/lista/enviogratis', 'CuponesController@indexcuponesenviogratis'); 
  Route::get('/cupones/tabla/lista', 'CuponesController@tablacuponesenviogratis'); 
  Route::post('/cupones/nuevo/cupon/gratis', 'CuponesController@nuevoCuponEnvioGratis');
  Route::post('/cupones/editar/informacion', 'CuponesController@editarInformacion');
  Route::post('/cupones/informacion', 'CuponesController@cuponInformacion');
  Route::post('/cupones/desactivar', 'CuponesController@desactivarCupon');
  Route::post('/cupones/activar', 'CuponesController@activarCupon');
  Route::get('/cupones/vista/envio/{id}', 'CuponesController@vistaEnvioGratis');
  Route::get('/cupones/tabla/zonasenviogratis/{id}', 'CuponesController@tablaZonasEnvioGratis');
  Route::get('/cupones/tabla/serviciosenviogratis/{id}', 'CuponesController@tablaServiciosEnvioGratis');
  Route::post('/cupones/minimo/enviogratis', 'CuponesController@actualizarMinimoEnvioGratis');
  Route::post('/cupones/envio/borrarservicio', 'CuponesController@borrarServicioDeEnvio');
  Route::post('/cupones/envio/borrarzona', 'CuponesController@borrarZonaDeEnvio');
  Route::post('/cupones/envio/agregarzona', 'CuponesController@nuevaZonaEnvio');
  Route::post('/cupones/envio/agregarservicio', 'CuponesController@nuevoServicioEnvio');
 

  Route::get('/cupones/vistausogeneral/{id}', 'CuponesController@vistaUsosGeneral');
  Route::get('/cupones/tabla/vistausogeneral/{id}', 'CuponesController@tablaVistaUsosGeneral');

  // lista de cupones para descuento de dinero
  Route::get('/cupones/lista/descuentod', 'CuponesController@indexDescuentoD'); 
  Route::get('/cupones/tabla/descuentod', 'CuponesController@tablaDescuentoD'); 
  Route::post('/cupones/nuevo/descuentod', 'CuponesController@nuevoCuponDescuentoD');
  Route::post('/cupones/editar/descuentod', 'CuponesController@editarDescuentoD');
  Route::post('/cupones/info/descuentod', 'CuponesController@cuponInfoDescuentoD');
  Route::get('/cupones/vista/descd/{id}', 'CuponesController@vistaDescuentoD');
  Route::get('/cupones/tabla/serviciodescuentod/{id}', 'CuponesController@tablaServicioDescuentoD');
  Route::post('/cupones/descuentod/agregarservicio', 'CuponesController@nuevaServicioDescuentoD');
  Route::post('/cupones/descuentod/borrarservicio', 'CuponesController@borrarServicioDescuentoD');
  Route::post('/cupones/descuentod/actualizadinero', 'CuponesController@actualizarDescuentoD');
 
  // lista de cupones para descuento en porcentaje
  Route::get('/cupones/lista/descuentop', 'CuponesController@indexDescuentoP'); 
  Route::get('/cupones/tabla/descuentop', 'CuponesController@tablaDescuentoP'); 
  Route::post('/cupones/nuevo/descuentop', 'CuponesController@nuevoCuponDescuentoP');
  Route::post('/cupones/editar/descuentop', 'CuponesController@editarDescuentoP');
  Route::post('/cupones/info/descuentop', 'CuponesController@cuponInfoDescuentoP');
  Route::get('/cupones/vista/descp/{id}', 'CuponesController@vistaDescuentoP');
  Route::get('/cupones/tabla/serviciodescuentop/{id}', 'CuponesController@tablaServicioDescuentoP');
  Route::post('/cupones/descuentop/agregarservicio', 'CuponesController@nuevaServicioDescuentoP');
  Route::post('/cupones/descuentop/borrarservicio', 'CuponesController@borrarServicioDescuentoP');
  Route::post('/cupones/descuentop/actualizadinero', 'CuponesController@actualizarDescuentoP');

  // lista de cupones para productos gratis
  Route::get('/cupones/lista/productos', 'CuponesController@indexProducto'); 
  Route::get('/cupones/tabla/productos', 'CuponesController@tablaProducto'); 
  Route::post('/cupones/nuevo/progratis', 'CuponesController@nuevoCuponProGratis');
  Route::post('/cupones/info/progratis', 'CuponesController@cuponInfoProGratis');
  Route::post('/cupones/editar/progratis', 'CuponesController@editarProGratis');
  Route::get('/cupones/vista/progratis/{id}', 'CuponesController@vistaProGratis');

  // lista de instituciones
  Route::get('/cupones/lista/instituciones', 'CuponesController@indexInstituciones'); 
  Route::get('/cupones/tabla/instituciones', 'CuponesController@tablaInstituciones'); 
  Route::post('/cupones/nuevo/institucion', 'CuponesController@nuevaInstitucion');
  Route::post('/cupones/info/institucion', 'CuponesController@infoInstitucion');
  Route::post('/cupones/editar/institucion', 'CuponesController@editarInstitucion');

  // lista de cupones para donaciones
  Route::get('/cupones/lista/donacion', 'CuponesController@indexDonacion'); 
  Route::get('/cupones/tabla/donacion', 'CuponesController@tablaDonacion');
  Route::post('/cupones/nuevo/donacion', 'CuponesController@nuevaDonacion');
  Route::post('/cupones/info/donacion', 'CuponesController@infoDonacion');
  Route::post('/cupones/editar/donacion', 'CuponesController@editarDonacion');

  // control de ordenes
  Route::get('/control/lista/ordeneshoy', 'ControlOrdenesController@indexHoy'); 
  Route::get('/control/tabla/ordeneshoy', 'ControlOrdenesController@tablaHoy');
 
  // notificaciones 
  Route::get('/control/lista/notificacion', 'ControlOrdenesController@indexNotificacion'); 

  // notificacion a propietarios
  Route::get('/control/tabla/notipropi/{id}', 'ControlOrdenesController@tablaPropiNoti');
  Route::post('/control/propi/device', 'ControlOrdenesController@devicePropietario');
  Route::post('/control/propi/notificacion', 'ControlOrdenesController@enviarNotiPropi');

  // notificacion a clientes por zona
  Route::get('/control/lista/notificacioncliente', 'ControlOrdenesController@indexNotiCliente'); 
  Route::post('/control/buscar/clienteszona', 'ControlOrdenesController@buscarClientes');
  Route::post('/control/enviarnoti/clienteszona', 'ControlOrdenesController@EnviarNotiClientesZonas');
  Route::post('/control/buscar/cliente', 'ControlOrdenesController@buscarCliente');
  Route::post('/control/enviarnoti/clienteunico', 'ControlOrdenesController@enviarNotiIndividual');



  
});         
   
