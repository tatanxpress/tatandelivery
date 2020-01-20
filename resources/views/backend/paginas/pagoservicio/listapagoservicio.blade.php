@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     <link href="{{ asset('css/backend/bootstrap-select.min.css') }}" type="text/css" rel="stylesheet" />

 @stop   
 
 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Generar pagos a servicios</h1>
           </div>  
           <button type="button" onclick="modalBuscar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar servicio
          </button>  

          <button type="button" onclick="modalReporte()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Generar reporte
          </button>  

          <button type="button" onclick="modalReporte2()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte orden tardia
          </button> 

          <button type="button" onclick="modalReporte3()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte orden cancelada
          </button> 

          <button type="button" onclick="modalReporte4()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte Motorista prestado
          </button> 

       </div>
     </section>
     
   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           <div class="card-header">
             <h3 class="card-title">Tabla de registros para pagos</h3>
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
                <h4 class="modal-title">Buscar ordenes de servicio</h4>
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
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
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

<!-- modal reporte -->
<div class="modal fade" id="modalReporte">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar ordenes de servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reporte">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid-reporte" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reporte">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reporte">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal reporte2 -->
<div class="modal fade" id="modalReporte2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar ordenes tardio de servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reporte2">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid-reporte2" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reporte2">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reporte2">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte2()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal reporte3 -->
<div class="modal fade" id="modalReporte3">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reporte para ordenes canceladas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reporte3">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid-reporte3" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reporte3">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reporte3">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte3()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal reporte4 -->
<div class="modal fade" id="modalReporte4">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reporte para motoristas prestados</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reporte4">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid-reporte4" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reporte4">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reporte4">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte4()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div>

  <!-- modal informacion-->
<div class="modal fade" id="modalInfo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-info">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>Nombre servicio</label>
                                    <input type="text" disabled class="form-control" id="nombreservicio">
                                </div>

                                <div class="form-group">
                                    <label>Nombre Cliente</label>
                                    <input type="text" disabled class="form-control" id="nombrecliente">
                                </div>

                                <div class="form-group">
                                    <label>Zona identificador</label>
                                    <input type="text" disabled class="form-control" id="zonaidentificador">
                                </div>

                                <div class="form-group">
                                    <label>Direccion</label>
                                    <input type="text" disabled class="form-control" id="direccion">
                                </div>

                                <div class="form-group">
                                    <label>Numero de casa</label>
                                    <input type="text" disabled class="form-control" id="numerocasa">
                                </div>

                                <div class="form-group">
                                    <label>Punto de referencia</label>
                                    <input type="text" disabled class="form-control" id="puntoreferencia">
                                </div>

                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" disabled class="form-control" id="telefono">
                                </div>


                                <!-- Ordenes -->

                                <div class="form-group">
                                    <label>Nota orden</label>
                                    <input type="text" disabled class="form-control" id="notaorden">
                                </div>

                                <div class="form-group">
                                    <label>Precio total</label>
                                    <input type="text" disabled class="form-control" id="preciototal">
                                </div>

                                <div class="form-group">
                                    <label>Precio envio</label>
                                    <input type="text" disabled class="form-control" id="precioenvio">
                                </div>

                                <div class="form-group">
                                    <label>Fecha orden</label>
                                    <input type="text" disabled class="form-control" id="fechaorden">
                                </div>

                                <div class="form-group">
                                    <label>Cambio vuelto</label>
                                    <input type="text" disabled class="form-control" id="cambiovuelto">
                                </div>

                                <div class="form-group">
                                    <label>Estado 2 (propietario respondio con tiempo de espera)</label>
                                    <br>
                                    <input type="checkbox" id="estado2" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 2</label>
                                    <input type="text" disabled class="form-control" id="fecha2">
                                </div>

                                <div class="form-group">
                                    <label>Minutos que esperara</label>
                                    <input type="text" disabled class="form-control" id="minutosespera">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 3 (cliente responde, que esperara tiempo)</label>
                                    <br>
                                    <input type="checkbox" id="estado3" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 3</label>
                                    <input type="text" disabled class="form-control" id="fecha3">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 4 (propietario inicia la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado4" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 4</label>
                                    <input type="text" disabled class="form-control" id="fecha4">
                                </div>

                                <div class="form-group">
                                    <label>Hora estimada entrega</label>
                                    <br>
                                    <input type="text" id="horaestimada" disabled class="form-control">
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Estado 5 (propietario finalizo de preparar la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado5" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 5</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha5">
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Estado 6 (Motorista va en camino a dejar la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado6" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 6</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha6" >
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Estado 7 (Motorista entrega la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado7" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 7</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha7" disabled>
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Estado 8 (Orden fue cancelada)</label>
                                    <br>
                                    <input type="checkbox" id="estado8" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 8</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha8" >
                                </div>

                                <div class="form-group">
                                    <label>Mensaje estado 8 (cancelado)</label>
                                    <input type="text" disabled class="form-control" id="mensaje8">
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Visible 1 (Es visible al cliente la orden)</label>
                                    <br>
                                    <input type="checkbox" id="visible1" disabled>
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Visible propietario (Es visible al propietario)</label>
                                    <br>
                                    <input type="checkbox" id="visiblep" disabled>
                                </div>

                                  <!-- -->

                                  <div class="form-group">
                                    <label>Visible propietario en orden preparacion</label>
                                    <br>
                                    <input type="checkbox" id="visiblep2" disabled>
                                </div>

                                  <!-- -->

                                  <div class="form-group">
                                    <label>Visible propietario 3 (Es visible al propietario, orden fue cancelada)</label>
                                    <br>
                                    <input type="checkbox" id="visiblep3" disabled>
                                </div>

                                  <!-- -->

                                  <div class="form-group">
                                    <label>Orden tardia (cliente cancelo por tardio)</label>
                                    <br>
                                    <input type="checkbox" id="ordentardia" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Fecha tardio (hora de cancelado por tardio)</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fechatardio" >
                                </div>

                                  <!-- -->

                                  <div class="form-group">
                                    <label>Cancelado cliente</label>
                                    <br>
                                    <input type="checkbox" id="canceladocliente" disabled>
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Cancelado propietario</label>
                                    <br>
                                    <input type="checkbox" id="canceladopropietario" disabled>
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Marcado como envio gratis</label>
                                    <br>
                                    <input type="checkbox" id="marcadogratis" disabled>
                                </div>

                                  <!-- -->

                                  <div class="form-group">
                                    <label>Producto visible al motorista</label>
                                    <br>
                                    <input type="checkbox" id="productovisible" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Ganancia de motorista</label>
                                    <input type="text" disabled class="form-control" id="gananciamotorista">
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

 
 @extends('backend.menus.inferior')
 
 @section('content-admin-js')
 
     <script src="{{ asset('js/backend/jquery.dataTables.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/backend/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/backend/bootstrap-select.min.js') }}" type="text/javascript"></script>

   <script> 

    function modalBuscar(){
        document.getElementById("formulario-buscar").reset();
        $('#modalBuscar').modal('show');
    }

    function modalReporte(){
        document.getElementById("formulario-reporte").reset();
        $('#modalReporte').modal('show');
    }

    function modalReporte2(){
        document.getElementById("formulario-reporte2").reset();
        $('#modalReporte2').modal('show');
    }

    function modalReporte3(){
        document.getElementById("formulario-reporte3").reset();
        $('#modalReporte3').modal('show');
    }

    function modalReporte4(){
        document.getElementById("formulario-reporte4").reset();
        $('#modalReporte4').modal('show');
    }

    function buscar(){
        var servicioid = document.getElementById('servicioid').value;
        var fechadesde = document.getElementById('fechadesde').value;
        var fechahasta = document.getElementById('fechahasta').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            var ruta = "{{ url('/admin/buscarservicio') }}/"+servicioid+"/"+fechadesde+"/"+fechahasta;
            $('#tablaDatatable').load(ruta);
        }
    }
 
    function reporte(){
        var servicioid = document.getElementById('servicioid-reporte').value;
        var fechadesde = document.getElementById('fechadesde-reporte').value;
        var fechahasta = document.getElementById('fechahasta-reporte').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/generar/reporte3') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta);

        }
    }

    function reporte2(){
        var servicioid = document.getElementById('servicioid-reporte2').value;
        var fechadesde = document.getElementById('fechadesde-reporte2').value;
        var fechahasta = document.getElementById('fechahasta-reporte2').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/generar/reporte4') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta);

        }
    }

    function reporte3(){
        var servicioid = document.getElementById('servicioid-reporte3').value;
        var fechadesde = document.getElementById('fechadesde-reporte3').value;
        var fechahasta = document.getElementById('fechahasta-reporte3').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/generar/reporte5') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta);

        }
    }

    function reporte4(){
        var servicioid = document.getElementById('servicioid-reporte4').value;
        var fechadesde = document.getElementById('fechadesde-reporte4').value;
        var fechahasta = document.getElementById('fechahasta-reporte4').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/generar/reporte6') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta);

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
       
        axios.post('/admin/ordenes/informacion',{
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
                    $('#horaestimada').val(response.data.horaestimada);
                   
                    if(response.data.orden.estado_2 == 0){
                        $("#estado2").prop("checked", false);
                    }else{
                        $("#estado2").prop("checked", true);
                    }

                    $('#fecha2').val(response.data.orden.fecha_2);
                    $('#minutosespera').val(response.data.orden.hora_2);

                    if(response.data.orden.estado_2 == 0){
                        $("#estado2").prop("checked", false);
                    }else{
                        $("#estado2").prop("checked", true);
                    }

                    $('#fecha3').val(response.data.orden.fecha_3);

                    if(response.data.orden.estado_3 == 0){
                        $("#estado3").prop("checked", false);
                    }else{
                        $("#estado3").prop("checked", true);
                    }

                    $('#fecha4').val(response.data.orden.fecha_4);

                    if(response.data.orden.estado_3 == 0){
                        $("#estado4").prop("checked", false);
                    }else{
                        $("#estado4").prop("checked", true);
                    }

                    $('#fecha5').val(response.data.orden.fecha_5);

                    if(response.data.orden.estado_3 == 0){
                        $("#estado5").prop("checked", false);
                    }else{
                        $("#estado5").prop("checked", true);
                    }

                    $('#fecha6').val(response.data.orden.fecha_6);

                    if(response.data.orden.estado_3 == 0){
                        $("#estado6").prop("checked", false);
                    }else{
                        $("#estado6").prop("checked", true);
                    }

                    $('#fecha7').val(response.data.orden.fecha_7);

                    if(response.data.orden.estado_3 == 0){
                        $("#estado7").prop("checked", false);
                    }else{
                        $("#estado7").prop("checked", true);
                    }

                    $('#fecha8').val(response.data.orden.fecha_8);
                    $('#mensaje8').val(response.data.orden.mensaje_8);

                    if(response.data.orden.estado_3 == 0){
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

                    
                    if(response.data.orden.tardio == 0){
                        $("#ordentardia").prop("checked", false);
                    }else{
                        $("#ordentardia").prop("checked", true);
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

                    $('#fechatardio').val(response.data.orden.fecha_tardio);
                    
                    if(response.data.orden.envio_gratis == 0){
                        $("#marcadogratis").prop("checked", false);
                    }else{
                        $("#marcadogratis").prop("checked", true);
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
  
  
   </script>
  
 
 
 @stop