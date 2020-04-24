@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
 
 @stop  
 
 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Buscador de # Orden</h1>
           </div>  
           <button type="button" onclick="modalBuscar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar # Orden
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
                                    <input type="number" class="form-control" id="orden">
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
                                    <label>Precio Producto + Envio</label>
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
                                    <label>Hora Estimada Entrega al Cliente</label>
                                    <input type="text" disabled class="form-control" id="estimada">
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
                                    <input type="text" disabled class="form-control" id="fecha5" disabled>
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
                                    <input type="text" disabled class="form-control" id="fecha6" disabled>
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
                                    <input type="text" disabled class="form-control" id="fecha8" disabled>
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

                                <div class="form-group">
                                    <label>Marcado como un minimo compra, sino no puede pedir</label>
                                    <br>
                                    <input type="checkbox" id="minenviogratis" disabled>
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
 
   <script>

    function modalBuscar(){
        document.getElementById("formulario-buscar").reset();
        $('#modalBuscar').modal('show');
    }
 
    function buscar(){
        var orden = document.getElementById('orden').value;

        if(orden === ''){
            toastr.error('# Orden es Requerida'); 
            return;
        }

        var ruta = "{{ url('/admin/buscar/num/orden') }}/"+orden;
        $('#tablaDatatable').load(ruta);        
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

                    const options = {
                    timeZone:"America/El_Salvador",
                    hour12 : true,
                    hour:  "2-digit",
                    minute: "2-digit",
                    second: "2-digit"
                    }

                    $('#modalInfo').modal('show');
                    $('#nombreservicio').val(response.data.orden.nombreServicio);
                    $('#nombrecliente').val(response.data.orden.nombreCliente);
                    $('#zonaidentificador').val(response.data.orden.identificador);
                    $('#direccion').val(response.data.orden.direccion);
                    $('#numerocasa').val(response.data.orden.numero_casa);
                    $('#puntoreferencia').val(response.data.orden.punto_referencia);
                    $('#telefono').val(response.data.orden.telefono);
                    $('#notaorden').val(response.data.orden.nota_orden);
                    $('#preciototal').val(response.data.total);
                    $('#precioenvio').val(response.data.orden.precio_envio);

                    var FormatoFecha1 = response.data.orden.fecha_orden;
                    var fecha1 = new Date(FormatoFecha1);
                    var convertido1 = fecha1.toLocaleDateString('es-ES', options);
                    $('#fechaorden').val(convertido1);
                    $('#cambiovuelto').val(response.data.orden.cambio);
                   
                    if(response.data.orden.estado_2 == 0){
                        $("#estado2").prop("checked", false);
                    }else{
                        $("#estado2").prop("checked", true);
                    }

                    
                    var FormatoFecha2 = response.data.orden.fecha_2;
                    var fecha2 = new Date(FormatoFecha2);
                    var convertido2 = fecha2.toLocaleDateString('es-ES', options);
                    $('#fecha2').val(convertido2);
                    $('#minutosespera').val(response.data.orden.hora_2);

 
                    if(response.data.orden.estado_3 == 0){
                        $("#estado3").prop("checked", false);
                    }else{
                        $("#estado3").prop("checked", true);

                        var FormatoFecha3 = response.data.orden.fecha_3;
                        var fecha3 = new Date(FormatoFecha3);
                        var convertido3 = fecha3.toLocaleDateString('es-ES', options);
                        $('#fecha3').val(convertido3);
                    }

                    if(response.data.orden.estado_4 == 0){
                        $("#estado4").prop("checked", false);
                    }else{
                        $("#estado4").prop("checked", true);
 
                        $('#estimada').val(response.data.estimada);

                        var FormatoFecha4 = response.data.orden.fecha_4;
                        var fecha4 = new Date(FormatoFecha4);
                        var convertido4 = fecha4.toLocaleDateString('es-ES', options);
                        $('#fecha4').val(convertido4);
                    }

                   

                    if(response.data.orden.estado_5 == 0){
                        $("#estado5").prop("checked", false);
                    }else{
                        $("#estado5").prop("checked", true);

                        var FormatoFecha5 = response.data.orden.fecha_5;
                        var fecha5 = new Date(FormatoFecha5);
                        var convertido5 = fecha5.toLocaleDateString('es-ES', options);
                        $('#fecha5').val(convertido5);
                    }

                    if(response.data.orden.estado_6 == 0){
                        $("#estado6").prop("checked", false);
                    }else{
                        $("#estado6").prop("checked", true);

                        var FormatoFecha6 = response.data.orden.fecha_6;
                        var fecha6 = new Date(FormatoFecha6);
                        var convertido6 = fecha6.toLocaleDateString('es-ES', options);
                        $('#fecha6').val(convertido6);
                    }

                   

                    if(response.data.orden.estado_7 == 0){
                        $("#estado7").prop("checked", false);
                    }else{
                        $("#estado7").prop("checked", true);

                        var FormatoFecha7 = response.data.orden.fecha_7;
                        var fecha7 = new Date(FormatoFecha7);
                        var convertido7 = fecha7.toLocaleDateString('es-ES', options);
                        $('#fecha7').val(convertido7);
                    }
                    
                    $('#mensaje8').val(response.data.orden.mensaje_8);

                    if(response.data.orden.estado_8 == 0){
                        $("#estado8").prop("checked", false);
                    }else{
                        $("#estado8").prop("checked", true);

                        var FormatoFecha8 = response.data.orden.fecha_8;
                        var fecha8 = new Date(FormatoFecha8);
                        var convertido8 = fecha8.toLocaleDateString('es-ES', options);
                        $('#fecha8').val(convertido8);
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
                    
                    if(response.data.orden.supero_envio_gratis == 0){
                        $("#minenviogratis").prop("checked", false);
                    }else{
                        $("#minenviogratis").prop("checked", true);
                    }

                }else{
                    toastr.error('publicidad o promo no encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }


 // ubicacion del cliente para la orden
 function mapa(id){        
        window.location.href="{{ url('/admin/ordenes/ubicacion/') }}/"+id;
    }
 
    // pasar id para ver producto de la orden
    function producto(id){
        window.location.href="{{ url('/admin/ordenes/listaproducto') }}/"+id;
    }
 
   </script>
  
 
 
 @stop