@extends('backend.menus.superior')

 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

 @stop

 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Buscador de ordenes de motorista</h1>
           </div>
           <button type="button" onclick="modalBuscar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro para ordenes del Motorista
          </button>

          <button type="button" style="margin-left:15px" onclick="modalFiltro()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro para datos basicos
          </button>

          <button type="button" style="margin-left:15px" onclick="reportePago()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte pago de ordenes
          </button>


          <button type="button" style="margin-left:15px" onclick="reportePagoEncargos()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte pago de encargos
          </button>

       </div>
     </section>

   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           <div class="card-header">
             <h3 class="card-title">Tabla de registros</h3>
           </div>
           <div class="card-body">
             <div class="row">
                 <div class="col-md-12">
                 <div id="tablaDatatable"></div>
               </div>
             </div>
           </div>
           </div>
       </div>
     </section>

<!-- modal buscar -->
<div class="modal fade" id="modalBuscar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscador de ordenes</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-buscar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                            <div class="form-group">
                                    <label style="color:#191818">Motorista identificador</label>
                                    <br>
                                    <div>

                                        <select class="form-control selectpicker" id="motoid" data-live-search="true" required>
                                            @foreach($moto as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="buscar()">Buscar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal filtro -->
<div class="modal fade" id="modalFiltro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtrar datos para registros</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                            <div class="form-group">
                                    <label style="color:#191818">Motorista identificador</label>
                                    <br>
                                    <div>

                                        <select class="form-control selectpicker" id="motoid-filtro" data-live-search="true" required>
                                            @foreach($moto as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-filtro">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-filtro">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtro()">Filtrar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal vista info -->
<div class="modal fade" id="modalDato">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Datos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-dato">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Ordenes agarradas</label>
                                    <input type="text" disabled class="form-control" id="totalagarradas">
                                </div>

                                <div class="form-group">
                                    <label>Total completadas</label>
                                    <input type="text" disabled class="form-control" id="totalcompletadas">
                                </div>

                                <div class="form-group">
                                    <label>Ordenes canceladas</label>
                                    <input type="text" disabled class="form-control" id="totalcanceladas">
                                </div>

                                <div class="form-group">
                                    <label>Total ganacia</label>
                                    <input type="text" disabled class="form-control" id="totalganancia">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>




<!-- modal filtro para conocer datos de los servicios a cobrar -->
<div class="modal fade" id="modalFiltroRegistro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtrar datos para registros</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-registro">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                            <div class="form-group">
                                    <label style="color:#191818">Motorista identificador</label>
                                    <br>
                                    <div>

                                        <select class="form-control selectpicker" id="motoid-registro" data-live-search="true" required>
                                            @foreach($moto as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-registro">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-registro">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtroRegistro()">Filtrar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal filtro para conocer datos de los servicios a cobrar -->
<div class="modal fade" id="modalReportePago">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generar reporte de ordenes completadas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reportepago">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                            <div class="form-group">
                                    <label style="color:#191818">Motorista identificador</label>
                                    <br>
                                    <div>

                                        <select class="form-control selectpicker" id="motoid-reportepago" data-live-search="true" required>
                                            @foreach($moto as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reportepago">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reportepago">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtroReportePago()">Generar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal filtro para conocer datos de los servicios a cobrar -->
<div class="modal fade" id="modalReportePagoEncargos">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Generar reporte de encargos completados</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reportepago-encargos">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                            <div class="form-group">
                                    <label style="color:#191818">Motorista identificador</label>
                                    <br>
                                    <div>

                                        <select class="form-control selectpicker" id="motoid-reportepago-encargos" data-live-search="true" required>
                                            @foreach($moto as $item)
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reportepago-encargos">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reportepago-encargos">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtroReportePagoEncargos()">Generar</button>
            </div>
        </div>
    </div>
</div>

<!-- modal cancelar -->
<div class="modal fade" id="modalCancelar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cancelar orden (solo si motorista inicio la orden)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-cancelar">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Mensaje</label>
                                    <input type="hidden" id="idcancelar">
                                    <input type="text" class="form-control" maxLength="200" id="mensajecancelar">
                                </div>

                                <div class="form-group">
                                    <label>Título notificación</label>
                                    <input type="text" class="form-control" maxLength="75" id="titulo">
                                </div>

                                <div class="form-group">
                                    <label>Mensaje notificación</label>
                                    <input type="text" class="form-control" maxLength="100" id="mensaje">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

            <button type="button" class="btn btn-primary" onclick="cancelarOrden()">Cancelar</button>

            </div>
        </div>
    </div>
</div>

 @extends('backend.menus.inferior')

 @section('content-admin-js')

     <script src="{{ asset('js/backend/jquery.dataTables.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/backend/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>

   <script>

    function modalBuscar(){
        document.getElementById("formulario-buscar").reset();
        $('#modalBuscar').modal('show');
    }

    function cancelar(id){
        document.getElementById("formulario-cancelar").reset();
        $('#idcancelar').val(id);
        $('#modalCancelar').modal('show');
    }

    function buscar(){
        var idmoto = document.getElementById('motoid').value;
        var fechadesde = document.getElementById('fechadesde').value;
        var fechahasta = document.getElementById('fechahasta').value;

        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            var ruta = "{{ url('/admin/buscar/moor') }}/"+idmoto+"/"+fechadesde+"/"+fechahasta;
            $('#tablaDatatable').load(ruta);
        }
    }

    function modalFiltro(){
        document.getElementById("formulario-filtro").reset();
        $('#modalFiltro').modal('show');
    }

    function modalFiltroPrestado(){
        document.getElementById("formulario-filtroprestado").reset();
        $('#modalFiltroPrestado').modal('show');
    }

    function modalFiltroRegistro(){
        document.getElementById("formulario-registro").reset();
        $('#modalFiltroRegistro').modal('show');
    }

    function reportePago(){
        document.getElementById("formulario-reportepago").reset();
        $('#modalReportePago').modal('show');
    }

    function reportePagoEncargos(){
        document.getElementById("formulario-reportepago-encargos").reset();
        $('#modalReportePagoEncargos').modal('show');
    }

    function filtro(){
        var id = document.getElementById('motoid-filtro').value;
        var fechadesde = document.getElementById('fechadesde-filtro').value;
        var fechahasta = document.getElementById('fechahasta-filtro').value;

        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            spinHandle = loadingOverlay().activate();

            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha1', fechadesde);
            formData.append('fecha2', fechahasta);

            axios.post('/admin/buscar/orden/filtraje',formData,{
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);

                if(response.data.success == 1){
                    $('#modalDato').modal('show');
                    $('#totalagarradas').val(response.data.totalagarradas);
                    $('#totalcompletadas').val(response.data.totalcompletada);
                    $('#totalcanceladas').val(response.data.totalcancelada);

                    $('#totalganancia').val(response.data.totalganancia);


                }else{
                    toastr.error('ID no encontrado');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error del servidor');
        });
        }
    }




    function validarNuevo(fechadesde, fechahasta){

        if(fechadesde === ''){
            toastr.error("fecha desde es requerido");
            return;
        }

        if(fechahasta === ''){
            toastr.error("fecha hasta desde es requerido");
            return;
        }

        return true;
    }

    function informacion(id){
        document.getElementById("formulario-info").reset();
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/buscar/orden/informacion',{
        'id': id
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

                if(response.data.success == 1){
                    $('#modalInfo').modal('show');
                    $('#nombreservicio').val(response.data.orden.nombreServicio);
                    $('#nombrecliente').val(response.data.orden.nombreCliente);
                    $('#zonaidentificador').val(response.data.orden.identificador);
                    $('#direccion').val(response.data.orden.direccion);
                    $('#numerocasa').val(response.data.orden.numero_casa);
                    $('#puntoreferencia').val(response.data.orden.punto_referencia);
                    $('#telefono').val(response.data.orden.telefono);
                    $('#notaorden').val(response.data.orden.nota_orden);
                    $('#preciototal').val(response.data.orden.precio_total);
                    $('#precioenvio').val(response.data.orden.precio_envio);
                    $('#fechaorden').val(response.data.orden.fecha_orden);
                    $('#cambiovuelto').val(response.data.orden.cambio);

                    if(response.data.orden.estado_2 == 0){
                        $("#estado2").prop("checked", false);
                    }else{
                        $("#estado2").prop("checked", true);
                    }

                    $('#fecha2').val(response.data.orden.fecha_2);
                    $('#minutosespera').val(response.data.orden.hora_2);


                    $('#fecha3').val(response.data.orden.fecha_3);

                    if(response.data.orden.estado_3 == 0){
                        $("#estado3").prop("checked", false);
                    }else{
                        $("#estado3").prop("checked", true);
                    }

                    $('#fecha4').val(response.data.orden.fecha_4);

                    if(response.data.orden.estado_4 == 0){
                        $("#estado4").prop("checked", false);
                    }else{
                        $("#estado4").prop("checked", true);
                    }

                    $('#fecha5').val(response.data.orden.fecha_5);

                    if(response.data.orden.estado_5 == 0){
                        $("#estado5").prop("checked", false);
                    }else{
                        $("#estado5").prop("checked", true);
                    }

                    $('#fecha6').val(response.data.orden.fecha_6);

                    if(response.data.orden.estado_6 == 0){
                        $("#estado6").prop("checked", false);
                    }else{
                        $("#estado6").prop("checked", true);
                    }

                    $('#fecha7').val(response.data.orden.fecha_7);

                    if(response.data.orden.estado_7 == 0){
                        $("#estado7").prop("checked", false);
                    }else{
                        $("#estado7").prop("checked", true);
                    }

                    $('#fecha8').val(response.data.orden.fecha_8);
                    $('#mensaje8').val(response.data.orden.mensaje_8);

                    if(response.data.orden.estado_8 == 0){
                        $("#estado8").prop("checked", false);
                    }else{
                        $("#estado8").prop("checked", true);
                    }

                    if(response.data.orden.visible == 0){
                        $("#visible").prop("checked", false);
                    }else{
                        $("#visible").prop("checked", true);
                    }


                    if(response.data.orden.visible_p == 0){
                        $("#visiblep").prop("checked", false);
                    }else{
                        $("#visiblep").prop("checked", true);
                    }


                    if(response.data.orden.visible_p2 == 0){
                        $("#visiblep2").prop("checked", false);
                    }else{
                        $("#visiblep2").prop("checked", true);
                    }


                    if(response.data.orden.visible_p3 == 0){
                        $("#visiblep3").prop("checked", false);
                    }else{
                        $("#visiblep3").prop("checked", true);
                    }


                    if(response.data.orden.cancelado_cliente == 0){
                        $("#canceladocliente").prop("checked", false);
                    }else{
                        $("#canceladocliente").prop("checked", true);
                    }


                    if(response.data.orden.cancelado_propietario == 0){
                        $("#canceladopropietario").prop("checked", false);
                    }else{
                        $("#canceladopropietario").prop("checked", true);
                    }


                    if(response.data.orden.visible_m == 0){
                        $("#productovisible").prop("checked", false);
                    }else{
                        $("#productovisible").prop("checked", true);
                    }

                    $('#gananciamotorista').val(response.data.orden.ganancia_motorista);


                }else{
                    toastr.error('publicidad o promo no encontrada');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error del servidor');
        });
    }

    // generar reporte de cobro a servicio por motorista prestado
    function reporte(){
        var idmoto = document.getElementById("id-motorista").value;
        var select = document.getElementById("select-servicio").value;
        var fecha1 = document.getElementById("fecha1-cobro").value;
        var fecha2 = document.getElementById("fecha2-cobro").value;

        window.open("{{ URL::to('admin/generar/reporte1') }}/" + idmoto + "/" + select + "/" + fecha1 + "/" + fecha2);
    }

    function cancelarOrden(){
        var id = document.getElementById('idcancelar').value;
        var mensajecancelar = document.getElementById("mensajecancelar").value;

        var titulo = document.getElementById("titulo").value;
        var mensaje = document.getElementById("mensaje").value;

        if(mensajecancelar === ''){
            toastr.error('Mensaje es requerido');
            return;
        }

        if(id === ''){
            toastr.error('ID no encontrado');
            return;
        }

        if(titulo === ''){
            toastr.error('titulo es requerido');
            return;
        }

        if(mensaje === ''){
            toastr.error('mensaje es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('mensaje', mensajecancelar);

        formData.append('titulo', titulo);
        formData.append('mensaje2', mensaje);

        axios.post('/admin/cancelarorden/panel', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            if (response.data.success == 0) {
                toastr.error('Validacion incorrecta');
            } else if (response.data.success == 1) {
                toastr.success('Cancelado');

                $('#modalCancelar').modal('hide');
            } else if(response.data.success == 2){
                // orden no puede ser cancelada
                toastr.error('Esta orden ya fue cancelada');
            }else if(response.data.success == 3){
                // orden no puede ser cancelada
                toastr.error('Orden aun no puede ser cancelada');
            }
            else {
                toastr.error('Error desconocido');
            }

        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });

    }




    function filtroReportePago(){
        var idmoto = document.getElementById('motoid-reportepago').value;
        var fechadesde = document.getElementById('fechadesde-reportepago').value;
        var fechahasta = document.getElementById('fechahasta-reportepago').value;

        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){
            window.open("{{ URL::to('admin/generar/reporte2') }}/" + idmoto + "/" + fechadesde + "/" + fechahasta);
        }
    }


    function filtroReportePagoEncargos(){
        var idmoto = document.getElementById('motoid-reportepago-encargos').value;
        var fechadesde = document.getElementById('fechadesde-reportepago-encargos').value;
        var fechahasta = document.getElementById('fechahasta-reportepago-encargos').value;

        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){
            window.open("{{ URL::to('admin/generar/reporte-motorista-encargo') }}/" + idmoto + "/" + fechadesde + "/" + fechahasta);
        }
    }

   </script>



 @stop
