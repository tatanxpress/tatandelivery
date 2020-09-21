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
  Route::post('/zona/actualizar-marcados','MapaZonaController@actualizarMarcados');
  
 
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

  // posiciones globales para tipo de servicios
  Route::get('/tiposerviciozona/posiciones-globales', 'TipoServicioZonaController@indexGlobal');
  Route::get('/tiposerviciozona/tablas/tablatiposervicioglobal', 'TipoServicioZonaController@tablaGlobalTipos');
  Route::post('/tiposerviciozona/ordenar-globalmente', 'TipoServicioZonaController@orderTipoServicioGlobalmente');

  // posiciones globales para servicios por zona
  Route::get('/zonaservicio/posiciones-globales', 'ZonaServiciosController@indexGlobal');
  Route::get('/zonaservicio/tablas/tablatiposervicioglobal', 'ZonaServiciosController@tablaGlobalTipos');
  Route::get('/zonaservicio/tablas/tablatiposervicioglobal2/{id}', 'ZonaServiciosController@tablaGlobalTipos2');


  Route::post('/zonaservicio/ordenar-globalmente', 'ZonaServiciosController@orderTipoServicioGlobalmente');


 
  Route::post('/tiposerviciozona/buscar/servicio', 'TipoServicioZonaController@buscarServicio');
  Route::post('/tiposerviciozona/nuevo', 'TipoServicioZonaController@nuevoTipoServicioZona');
  Route::post('/tiposerviciozona/informacion','TipoServicioZonaController@informacionTipoZona');
  Route::post('/tiposerviciozona/editar-tipo','TipoServicioZonaController@editarTipo');
  Route::get('/tiposerviciozona/{id}', 'TipoServicioZonaController@filtrado'); // filtrado
  Route::get('/tiposerviciozona/tabla/{id}', 'TipoServicioZonaController@tablaFiltrado'); // tabla filtrado
  Route::post('/tiposerviciozona/ordenar', 'TipoServicioZonaController@ordenar');     
  Route::post('/activar/desactivar/tiposervicio', 'TipoServicioZonaController@activarDesactivarTipoServicio');     
  Route::post('/activar/desactivar/zonaservicio', 'TipoServicioZonaController@activarDesactivarZonaServicio');     
 


  // clientes 
  Route::get('/cliente/lista-clientes', 'ClientesController@index'); 
  Route::get('/cliente/tablas/cliente', 'ClientesController@clienteTabla'); 

  Route::get('/cliente/lista-clientes-todos', 'ClientesController@indexTodos'); 
  Route::get('/cliente/tablas/cliente-todos', 'ClientesController@clienteTablaTodos');
  
  Route::post('/cliente/informacion','ClientesController@informacion');
  Route::post('/cliente/historial-cliente','ClientesController@historialCliente'); // historial de compras

 
  Route::post('/cliente/editar','ClientesController@editar');
  Route::get('/cliente/direcciones/{id}', 'ClientesController@direccionesCliente');
  Route::get('/cliente/tablas/direccion/{id}', 'ClientesController@direccionesTabla'); 
  Route::post('/cliente/direcciones/informacion','ClientesController@infoDireccion');
  Route::get('/cliente/ubicacion/{id}', 'ClientesController@clienteUbicacion');
  Route::get('/cliente/ubicacion-real/{id}', 'ClientesController@clienteUbicacion2');
 
 
  Route::get('/cliente/vista-buscar-cliente', 'ClientesController@vistaBuscarCliente'); 
  Route::get('/cliente/info-buscar-cliente/{tel}','ClientesController@buscarClienteConNumero');
  Route::post('/cliente/actualizar-info-direccion','ClientesController@actualizarDireccionCliente');
  Route::post('/cliente/actualizar/extranjero/direccion','ClientesController@actualizarExtranjero');

 
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
   
  // cambiar precio de envio a todos los servicios por zona
  Route::post('/zonaservicios/nuevo-precio-varios', 'ZonaServiciosController@precioEnvioPorZona'); 
  // aplicar nuevo cargo de envio por zona y servicios
  Route::post('/zonaservicios/modificar-min-gratis', 'ZonaServiciosController@aplicarNuevoCargoZonaServicio'); 
  // cambiar precio ganancia motorista a todos los servicios por zona
  Route::post('/zonaservicios/nuevo-precio-ganancia', 'ZonaServiciosController@precioGananciaPorZona'); 
  
      
  // productos 
  Route::get('/productos/{id}', 'ProductoController@index');  
  Route::get('/productos/tablas/{id}', 'ProductoController@tablaProductos');
  Route::post('/productos/nuevo', 'ProductoController@nuevo'); 
  Route::post('/productos/informacion', 'ProductoController@informacion');
  Route::post('/productos/editar', 'ProductoController@editar');
  Route::post('/productos/ordenar', 'ProductoController@ordenar'); 

  // ver todos los productos
  Route::get('/ver/todos/productos/{id}', 'ProductoController@indexTodos');  
  Route::get('/ver/tabla/todos/productos/{id}', 'ProductoController@tablaTodosLosProductos');
 
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

  // posiciones globales para zona de publicidad
  Route::get('/publicidad/pos', 'ZonaPublicidadController@indexGlobalPublicidad');
  Route::get('/publicidad/tablas/tablasgloblal', 'ZonaPublicidadController@tablaGlobalPubli');
  Route::post('/publicidad/ordenar-globalmente', 'ZonaPublicidadController@ordenarPubliGlobal');

  
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
  Route::post('/motoristasservicio/borrartodo', 'MotoristaController@borrarTodo');

  Route::post('/motoristasservicio/nuevo', 'MotoristaController@nuevomotoservicio');
  Route::post('/motoristasservicio/nuevo-global', 'MotoristaController@nuevoGlobal');
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
  Route::get('/ordenes/ubicacion/{id}', 'OrdenesController@entregaUbicacion'); 
  Route::get('/ordenes/listaproducto/{id}', 'OrdenesController@listaproducto'); 
  Route::get('/ordenes/tabla/producto/{id}', 'OrdenesController@productos'); 

  // buscar motorista de esta orden para cambiar
  Route::post('/ordenes/buscar-su-motorista', 'OrdenesController@buscarSuMotorista'); 
  Route::post('/ordenes/cambiar-su-motorista', 'OrdenesController@editarMotoristaASuOrden'); 

 

  // agregar mas fotos a un producto
  Route::get('/productos/mas/fotografias/{id}', 'ProductoController@indexMasFotos'); 
  Route::get('/productos/tabla/mas/fotografias/{id}', 'ProductoController@indexMasFotosTabla'); 
  Route::post('/productos/agregar/imagen-extra', 'ProductoController@nuevaFotoExtra'); 
  Route::post('/productos/imagenes/extra-borrar', 'ProductoController@borrarImagenExtra'); 
  Route::post('/productos/editar/imagen-entra', 'ProductoController@editarProductoImagenExtra'); 
  Route::post('/productos/imagenes-extra/ordenar', 'ProductoController@ordenarImagenesExtra'); 

  

  // agregar un video al producto
  Route::get('/productos/mas/video/{id}', 'ProductoController@indexMasVideo'); 
  Route::post('/productos/agregar/video', 'ProductoController@agregarVideoProducto'); 
  Route::post('/productos/video/borrar', 'ProductoController@borrarVideoProducto'); 
  Route::post('/productos/editar/video', 'ProductoController@editarProductoVideo'); 


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

  // ubicacion
  Route::get('/mapa/orden/cliente/direccion/{id}', 'OrdenesController@mapaOrdenGPS1');
  Route::get('/mapa/orden/cliente/direccion-real/{id}', 'OrdenesController@mapaOrdenGPS2');

 


  Route::post('/buscar3/orden/servicio', 'OrdenesController@buscador3');

  // informacion de las ordenes
  Route::post('/buscar/orden/infocliente', 'OrdenesController@informacioncliente');
  Route::post('/buscar/orden/infoorden', 'OrdenesController@informacionorden');
  Route::post('/buscar/orden/infocargo', 'OrdenesController@informacioncargo');
  Route::post('/buscar/orden/infomotorista', 'OrdenesController@informacionmotorista');
  Route::post('/buscar/orden/infotipocargo', 'OrdenesController@informaciontipocargo');
 
   

  Route::post('/buscar/orden/filtraje', 'OrdenesController@filtro');
  Route::get('/generar/reporte2/{id}/{id2}/{id3}', 'OrdenesController@reporte1'); // reporte de pago ordenes a motoristas
  Route::get('/generar/reporte-motorista-encargo/{id}/{id2}/{id3}', 'OrdenesController@reporteEncargoMotorista'); // reporte de pago encargos a motoristas




  // guardar registro de pago motorista
  Route::get('/motopago/lista', 'MotoristaPagoController@index');
  Route::get('/motopago/tabla/lista', 'MotoristaPagoController@tablapago'); // datos de pagos a motoristas
  Route::post('/registro/pago/motorista', 'MotoristaPagoController@nuevo');
  Route::post('/motopago/pago/ver', 'MotoristaPagoController@totalpagadomotorista');
      
  // pago a servicios  
  Route::get('/pagoservicios/lista', 'MotoristaPagoController@index2');  
  Route::get('/buscarservicio/{id}/{id1}/{id2}/{d3}', 'MotoristaPagoController@buscador'); // buscar ordenes completas del servicio
  Route::get('/generar/reporte3/{id}/{id2}/{id3}/{d4}', 'MotoristaPagoController@reporte'); // reporte de ordenes completas
  // solo saca las tablas 
  Route::get('/generar/reporte3-tablas/{id}/{id2}/{id3}/{d4}', 'MotoristaPagoController@reporteTablas'); // reporte de ordenes completas

  
  //Route::get('/generar/reporte4/{id}/{id2}/{id3}', 'MotoristaPagoController@reporte2');
  Route::get('/generar/reporte5/{id}/{id2}/{id3}', 'MotoristaPagoController@reporteordencancelada');
  Route::get('/generar/reporte7/{id}/{id2}/{id3}', 'MotoristaPagoController@reporteproductovendido'); // reporte de productos vendidos
  Route::get('/generar/reporte-encargo/{id}/{id2}/{id3}', 'MotoristaPagoController@reporteOrdenesEncargo'); // reporte de ordenes encargo

      
  // reporte por tipos de cargo de envio
  Route::get('/generar/tipocargo/{id}/{id2}/{id3}/{d4}', 'MotoristaPagoController@reporteTipoCargoRevuelto'); // servicio uso min de $$ para envio gratis
   

  // ver ordenes revisadas 
  Route::get('/ordenrevisada/lista', 'MotoristaPagoController@index3'); 

  Route::get('/ordenrevisada/{id}/{id1}/{id2}', 'MotoristaPagoController@buscarOrdenRevisada');

 
  Route::get('/ordenrevisada2/{id}', 'MotoristaPagoController@buscarOrdenRevisada2'); // ordenes de motorista sin depositar
  Route::get('/ordenrevisada-encargos/{id}', 'MotoristaPagoController@ordenesEncargoMotoristaSinEntregar'); // ordenes encargo de motorista sin depositar
 
  
  Route::get('/ordenrevisada3/{id}/{id1}/{id2}', 'MotoristaPagoController@reporteordenrevisada'); // buscar ordenes revisadas
  Route::get('/ordenrevisada-reporte-encargo/{id}/{id1}/{id2}', 'MotoristaPagoController@reporteOrdenRevisadaEncargo'); // reporte de ordenes revisadas por cobrador
   
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

  // Encargos
  Route::get('/encargos/lista', 'EncargosWebController@verListaActivos'); 
  Route::get('/encargos/tabla/lista', 'EncargosWebController@verListaActivosTabla');
  Route::post('/encargos/ver-informacion', 'EncargosWebController@informacionEncargos');
  Route::post('/encargos/nuevo', 'EncargosWebController@nuevoEncargo');
  Route::post('/encargos/editar-encargos', 'EncargosWebController@editarEncargo');
  Route::get('/encargos/lista-finalizo', 'EncargosWebController@verListaFinalizado'); 
  Route::get('/encargos/tabla/lista-finalizo', 'EncargosWebController@verListaFinalizadoTabla');
  Route::post('/encargos/asignar-servicio', 'EncargosWebController@asignarServicioAlEncargo');


  Route::post('/encargos/activarlo', 'EncargosWebController@activarEncargo');
  Route::get('/encargos/lista-negocios', 'EncargosWebController@verListaNegocios'); 
  Route::get('/encargos/tabla/lista-negocios', 'EncargosWebController@VerListaNegociosTabla');
  Route::post('/encargos/nuevo-negocio', 'EncargosWebController@nuevoNegocio');
  Route::post('/encargos/negocios-informacion', 'EncargosWebController@informacionNegocio');
  Route::post('/encargos/editar-negocios', 'EncargosWebController@editarNegocio');
  Route::get('/encargos/negocios/categorias/{id}', 'EncargosWebController@verCategoriasNegocio'); 
  Route::get('/encargos/tabla/negocio-categorias/{id}', 'EncargosWebController@verCategoriasNegocioTabla'); 
  Route::post('/encargos/negocios/nueva-categoria', 'EncargosWebController@guardarCategoria');
  Route::post('/encargos/negocios/categorias-informacion', 'EncargosWebController@informacionCategoria');
  Route::post('/encargos/negocios/categorias-editar', 'EncargosWebController@editarCategoria');
  Route::get('/encargos/negocios/categorias-productos/{id}', 'EncargosWebController@verProductosCategoriasNegocio'); 
  Route::get('/encargos/tabla/negocios/categorias-productos/{id}', 'EncargosWebController@verProductosCategoriasNegocioTabla'); 
  Route::post('/encargos/negocios/categorias/productos-nuevo', 'EncargosWebController@guadarProductoCategoriaNegocio');
  Route::post('/encargos/negocios/categorias/productos-informacion', 'EncargosWebController@informacionProductoCategoria');
  Route::post('/encargos/negocios/categorias/productos-editar', 'EncargosWebController@editarProductoCategoria');

  Route::get('/encargos/zonas-lista/{id}', 'EncargosWebController@verListaZonasEncargo'); 
  Route::get('/encargos/tabla/zonas-lista/{id}', 'EncargosWebController@verListaZonasEncargoTabla'); 

  
  
  Route::post('/encargos/zonas/zona-borrar', 'EncargosWebController@borrarZonaEncargo');
  Route::post('/encargos/zonas/zona-nuevo', 'EncargosWebController@nuevoEncargoZona');
  Route::post('/encargos/zonas/zona-informacion', 'EncargosWebController@informacionZonaEncargo');  
  Route::post('/encargos/zonas/zona-editar', 'EncargosWebController@editarZonaEncargo');
          
         
  Route::get('/encargos/lista-categorias/{id}', 'EncargosWebController@listadoCategoriasEncargo'); 
  Route::get('/encargos/tabla/lista-categorias/{id}', 'EncargosWebController@listadoCategoriasEncargoTabla'); 
  Route::post('/encargos/lista/buscador-categoria', 'EncargosWebController@buscadorCategoriaNegocio');
  Route::post('/encargos/info/lista-encargo-categoria', 'EncargosWebController@infoListaEncargoCategoria');

  Route::post('/encargos/lista/editar', 'EncargosWebController@editarListaEncargo');
  Route::post('/encargos/lista-nuevo', 'EncargosWebController@nuevaListaEncargo');
 
  Route::get('/encargos/lista/ver-productos/{id}', 'EncargosWebController@verListaDeProductosCategorias'); 
  Route::get('/encargos/lista/ver-productos-tabla/{id}', 'EncargosWebController@verListaDeProductosCategoriasTabla'); 

  Route::post('/encargos/lista/producto/guardar', 'EncargosWebController@guardarProductoEnLista');
  Route::post('/encargos/lista/ordenar', 'EncargosWebController@ordenarListaCategorias'); 
  Route::post('/encargos/lista/ordenar-productos', 'EncargosWebController@ordenarListaCategoriasProducto'); 
  Route::post('/encargos/lista/producto/activar-desactivar', 'EncargosWebController@activarDesactivarListaProducto'); 

  
 

  Route::get('/encargos/ordenes-lista/{id}', 'EncargosWebController@verOrdenesEncargoPendientes'); 
  Route::get('/encargos/tabla/ordenes-lista/{id}', 'EncargosWebController@verOrdenesEncargoPendientesTabla'); 
  Route::post('/encargos/ordenes/informacion', 'EncargosWebController@informacionOrdenesEncargo'); 
  Route::get('/encargos/ordenes/direccion/mapa-gps/{id}', 'EncargosWebController@mapaGPS'); // latitud y longitud de direccion encargo
  Route::get('/encargos/ordenes/direccion/mapa-gps-real/{id}', 'EncargosWebController@mapaGPS2'); // lati real y longi real de direccion encargo


  Route::get('/encargos/ordenes/productos-ver/{id}', 'EncargosWebController@verProductoDeOrdenesEncargo'); 
  Route::get('/encargos/ordenes/tabla-productos-ver/{id}', 'EncargosWebController@verProductoDeOrdenesEncargoTabla'); 
  Route::post('/encargos/ordenes/ver-motorista-asignado', 'EncargosWebController@verMotoristaAgarroEncargo'); 
  Route::post('/encargos/ordenes/cancelamiento', 'EncargosWebController@cancelarOrdenEncargo'); 
  Route::post('/encargos/ordenes/confirmar', 'EncargosWebController@confirmarOrdenEncargo');  

  Route::get('/encargos/asignar/motorista-encargo/{id}', 'EncargosWebController@asignarMotoristaAlEncargo'); 
  Route::get('/encargos/asignar/tabla/motorista-encargo/{id}', 'EncargosWebController@asignarMotoristaAlEncargoTabla'); 
  Route::post('/encargos/ordenes/motorista-asignando-encargo', 'EncargosWebController@asignandoMotoristaAlEncargo'); 
  Route::post('/encargos/ordenes/motorista-asignando-encargo-borrar', 'EncargosWebController@asignandoMotoristaAlEncargoBorrar'); 

  // Encargos
  Route::post('/encargos/asignar-motorista', 'EncargosWebController@asignarMotoristaEncargo');
  Route::get('/encargos/buscar/numero/orden-encargo', 'EncargosWebController@indexVistaNumEncargo');
  Route::get('/encargos/buscar/num/encargo/{id}', 'EncargosWebController@buscarNumeroOrdenEncargo');
  Route::post('/encargos/buscar/encargo/infocliente', 'EncargosWebController@informacionOrdenEncargo');
  

   


 
  
  
  // cancelacion de una orden por panel de control
  Route::post('/cancelarorden/panel', 'OrdenesController@cancelarOrdenPanel');

  // configuracion
  Route::get('/dinero/limite', 'ConfiguracionesController@index'); 
  Route::post('/dinero/limite/informacion', 'ConfiguracionesController@informacion');
  Route::post('/dinero/limite/actualizar', 'ConfiguracionesController@actualizar');
  Route::post('/informacion/de/aplicacion', 'ConfiguracionesController@informacionApp');
  Route::post('/actualizar/versiones/app', 'ConfiguracionesController@actualizarAppVersion');
  
 
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
  Route::post('/control/total/de/ventas-hoy', 'ControlOrdenesController@totalVentasHoy');

  // productos de orden nueva
  Route::get('/productos/orden/nueva/{id}', 'ControlOrdenesController@indexProductosHoy'); 
  Route::get('/productos/tabla/orden/nueva/{id}', 'ControlOrdenesController@tablaProductosHoy');

 
    
  // notificaciones 
  Route::get('/control/lista/notificacion', 'ControlOrdenesController@indexNotificacion'); 

  // notificacion a propietarios
  Route::get('/control/tabla/notipropi/{id}', 'ControlOrdenesController@tablaPropiNoti');
  Route::post('/control/propi/device', 'ControlOrdenesController@devicePropietario');
  Route::post('/control/propi/notificacion', 'ControlOrdenesController@enviarNotiPropi');

  // notificacion a motorista
  Route::post('/control/motorista/notificacion', 'ControlOrdenesController@envarNotificacionMotorista');
  // notificacion a administradores
  Route::post('/control/administradores/notificacion', 'ControlOrdenesController@envarNotificacionAdministradores');
 

  // notificacion a clientes por zona
  Route::get('/control/lista/notificacioncliente', 'ControlOrdenesController@indexNotiCliente'); 
  Route::post('/control/buscar/clienteszona', 'ControlOrdenesController@buscarClientes');
  Route::post('/control/enviarnoti/clienteszona', 'ControlOrdenesController@EnviarNotiClientesZonas');
  Route::post('/control/buscar/cliente', 'ControlOrdenesController@buscarCliente');
  Route::post('/control/enviarnoti/clienteunico', 'ControlOrdenesController@enviarNotiIndividual');


  // reporte de todas las ordenes que ha hecho el cliente
  Route::get('/generar/reporte/cliente-ordenes/{id}', 'OrdenesController@reporteClienteOrdenes'); // reporte de pago ordenes a motoristas
  Route::post('/editar/orden/punto-gps', 'OrdenesController@actualizarCoordenadasOrden');
 
  // ** REVISION DE CREDI PUNTOS COMPRADOS ** //
  Route::get('/usuario/credipuntos', 'ClientesController@vistaCrediPuntos'); 
  Route::get('/credipuntos/lista', 'ClientesController@obtenerListaCrediPuntosClientes');
  Route::post('/verificar/credipuntos/cliente', 'ClientesController@aprobarCrediPuntos');
  Route::post('/ver/credito/actual', 'ClientesController@verCreditoActual'); // por id creditos_usuarios
  Route::post('/ver/credito/actual2', 'ClientesController@verCreditoActual2'); // por id del usuario
  
 
  Route::post('/buscar/cliente/areanumero', 'ClientesController@buscarClienteAreaNumero');
   
  Route::post('/agregar/credito/manual', 'ClientesController@agregarCreditoManual');
  Route::post('/eliminar/credito/manual', 'ClientesController@eliminarCreditoManual');

  Route::get('/lista/credito/para/quitar', 'ClientesController@indexCreditoParaQuitar'); 
  Route::get('/lista/tabla/credito/para/quitar/{num}', 'ClientesController@tablaCreditoParaQuitar');

   

  
  // Ciudades
  Route::get('/usuario/ciudades', 'ClientesController@indexCiudades'); 
  Route::get('/ciudades/tabla/lista', 'ClientesController@tablasCiudades');
  Route::post('/agregar/nueva/ciudad', 'ClientesController@agregarNuevaCiudad');
  Route::post('/ciudades/informacion', 'ClientesController@informacionCiudades');
  Route::post('/ciudades/editar', 'ClientesController@editarCiudades');
  Route::post('/borrar/ciudad', 'ClientesController@borrarCiudad');


  // extranjeros ultimos 100, aqui veremos para editarlos rapidamente
  Route::get('/extranjeros/extranjeros', 'ClientesController@indexExtranjeros'); 
  Route::get('/extranjeros/tabla/lista', 'ClientesController@tablaExtranjeros'); 
  Route::get('/extranjero/todas/direcciones/{id}', 'ClientesController@todasLasDirecciones');
  Route::get('/extranjero/tabla/todas/direcciones/{id}', 'ClientesController@tablaTodasLasDirecciones');

  // iniciar orden manualmente
  Route::post('/iniciar/orden/manual', 'ClientesController@agregarNuevaCiudad');
  Route::post('/informacion/direccion/extranjero', 'ClientesController@informacionExtrajero');

  // ver registros de credi puntos
  Route::get('/registro/credipuntos', 'ClientesController@verRegistroCredito'); 
  Route::get('/registro/tabla/credipuntos', 'ClientesController@tablaRegistroCredito'); 



});         
   

