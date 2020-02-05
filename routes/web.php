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


Route::get('/', 'FrontendController@index')->name('inicio');
Route::get('/preguntas-frecuentes', 'FrontendController@verFAQ');
 
// Rutas administrador
 
Route::get('admin/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
Route::post('/admin', 'Auth\AdminLoginController@login')->name('admin.login.submit');

// RUTA ADMINISTRADOR
Route::prefix('admin')->group(function () {  
  Route::get('/', 'DashboardController@index')->name('admin.dashboard');
  Route::get('/inicio', 'DashboardController@getInicio')->name('admin.inicio');
  // mapa zonas
  Route::get('/zona/mapa/zona', 'MapaZonaController@index');
  Route::get('/zona/tablas/zona', 'MapaZonaController@zonatabla');
  Route::post('/zona/informacion-zona','MapaZonaController@informacionZona');
  Route::post('/zona/nueva-zona','MapaZonaController@nuevaZona');
  Route::post('/zona/editar-zona','MapaZonaController@editarZona');
  Route::post('/zona/nuevo-poligono','MapaZonaController@crearPoligono');
  Route::post('/zona/borrar-poligono','MapaZonaController@borrarPoligono');
  Route::get('/zona/ver-poligonos/{id}', 'MapaZonaController@verMapa');
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
 // codigo temporal
  //Route::get('/codigotemporal/lista', 'CodigoTemporalController@index');
  //Route::get('/codigotemporal/tabla/codigo', 'CodigoTemporalController@codigotabla');
  //Route::post('/activosms/informacion','CodigoTemporalController@informacion');
 // Route::post('/activosms/editar','CodigoTemporalController@editar');
  // servicios locales
  Route::get('/servicios/lista', 'ServiciosController@index'); 
  Route::get('/servicios/tabla/lista', 'ServiciosController@serviciotabla');
  Route::post('/servicios/nuevo', 'ServiciosController@nuevo'); 
  Route::post('/servicios/informacion/servicio', 'ServiciosController@informacionServicio');
  Route::post('/servicios/informacion-tiempo/servicio', 'ServiciosController@informacionTiempo');
  Route::post('/servicios/informacion-horario/servicio', 'ServiciosController@informacionHorario');
  Route::post('/servicios/editar-servicio', 'ServiciosController@editarServicio');
  Route::post('/servicios/editar-tiempo', 'ServiciosController@editarTiempo');
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
  Route::get('/ordenes/tabla/lista', 'OrdenesController@tablaorden');
  Route::post('/ordenes/informacion', 'OrdenesController@informacion');
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
  Route::get('/buscar/moor/{id}/{id1}/{id2}', 'OrdenesController@buscador');
  Route::get('/buscar2/moor/{id}/{id1}/{id2}', 'OrdenesController@buscador2');
  Route::post('/buscar3/orden/servicio', 'OrdenesController@buscador3');
  Route::post('/buscar/orden/informacion', 'OrdenesController@infoordenbuscada');
  Route::post('/buscar/orden/filtraje', 'OrdenesController@filtro');
  // reporte de servicio por motorista prestado 
  Route::get('/generar/reporte1/{id}/{id2}/{id3}/{id4}', 'OrdenesController@reporte');
  Route::get('/generar/reporte2/{id}/{id2}/{id3}', 'OrdenesController@reporte1');
  // guardar registro de pago motorista
  Route::get('/motopago/lista', 'MotoristaPagoController@index');
  Route::get('/motopago/tabla/lista', 'MotoristaPagoController@tablapago');
  Route::post('/registro/pago/motorista', 'MotoristaPagoController@nuevo');
  Route::post('/motopago/pago/ver', 'MotoristaPagoController@totalpagadomotorista');
   
  // pago a servicios 
  Route::get('/pagoservicios/lista', 'MotoristaPagoController@index2'); 
  Route::get('/buscarservicio/{id}/{id1}/{id2}', 'MotoristaPagoController@buscador'); // buscar ordenes del servicio
  Route::get('/generar/reporte3/{id}/{id2}/{id3}', 'MotoristaPagoController@reporte'); // reporte de ordenes completas
  Route::get('/generar/reporte4/{id}/{id2}/{id3}', 'MotoristaPagoController@reporte2');
  Route::get('/generar/reporte5/{id}/{id2}/{id3}', 'MotoristaPagoController@reporteordencancelada');
  Route::get('/generar/reporte6/{id}/{id2}/{id3}', 'MotoristaPagoController@reportemotoristaprestado');
  Route::get('/generar/reporte7/{id}/{id2}/{id3}', 'MotoristaPagoController@reporteproductovendido'); // reporte de productos vendidos
 
     
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
  
  // agregar administradores para revision de ordenes sin motorista
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
  // envio de notificaciones
  Route::get('/notificacion/vista', 'AdminController@vistanotificacion'); 
 
 
});       
  
