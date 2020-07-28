@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
 
 @stop  
 
<style>


.info {background-color: #2196F3;} /* Blue */
</style>
 
 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Buscador de # Orden Encargo</h1>
           </div>  
           <button type="button" onclick="modalBuscar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar # Orden Encargo
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

<!-- modal informacion del cliente-->
<div class="modal fade" id="modalInfoCliente">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion del cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-infocliente">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>Nombre Cliente</label>
                                    <input type="hidden" id="iddireccion">
                                    <input type="text" disabled class="form-control" id="nombrecliente">
                                </div>

                                <div class="form-group">
                                    <label>Zona identificador</label>
                                    <input type="text" disabled class="form-control" id="zonaidentificador">
                                </div>

                                <div class="form-group">
                                    <label>Nombre Zona</label>
                                    <input type="text" disabled class="form-control" id="nombrezona">
                                </div>

                                <div class="form-group">
                                    <label>Nombre Encargo</label>
                                    <input type="text" disabled class="form-control" id="nombreencargo">
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
                                    <label>Telefono Registrado en la App</label>
                                    <input type="text" disabled class="form-control" id="telefonoreal">
                                </div>

                                <div class="form-group">
                                    <label>Latitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="latitud">
                                </div>

                                <div class="form-group">
                                    <label>Longitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="longitud">
                                </div>

                                </br>

                                <button type="button" onclick="mapa()" class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                        Mapa
                                </button>  

                                </br></br>

                                <div class="form-group">
                                    <label>Latitud Real</label>
                                    <input type="text" disabled class="form-control" id="latitudreal">
                                </div>

                                <div class="form-group">
                                    <label>Longitud real</label>
                                    <input type="text" disabled class="form-control" id="longitudreal">
                                </div>

                                </br>

                                <button type="button" onclick="mapa2()" class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                        Mapa
                                </button>  

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

<!-- modal informacion de la orden-->
<div class="modal fade" id="modalInfoOrden">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion Orden</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-infoorden">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                                                          
                                       
                                <div class="form-group">
                                    <label>Estado Orden</label>
                                    <input type="text" disabled class="form-control" id="estadoorden-info">
                                </div>

                                <div class="form-group">
                                    <label>Nombre Encargo</label>
                                    <input type="text" disabled class="form-control" id="nombreencargo-info">
                                </div>

                                <div class="form-group">
                                    <label>ID Encargo</label>
                                    <input type="text" disabled class="form-control" id="idencargo-info">
                                </div>

                                <div class="form-group">
                                    <label>Fecha Orden</label>
                                    <input type="text" disabled class="form-control" id="fechaorden-info">
                                </div>

                                <div class="form-group">
                                    <label>Precio Sub Total</label>
                                    <input type="text" disabled class="form-control" id="subtotal-info">
                                </div>

                                <div class="form-group">
                                    <label>Precio Envio</label>
                                    <input type="text" disabled class="form-control" id="precioenvio-info">
                                </div>

                                <div class="form-group">
                                    <label>Precio Total (Sub total + Envio)</label>
                                    <input type="text" disabled class="form-control" id="total-info">
                                </div>

                                <div class="form-group">
                                    <label>Ganancia Motorista</label>
                                    <input type="text" disabled class="form-control" id="gananciamoto-info">
                                </div>


                                <div class="form-group">
                                    <label>Estado 0 (Inicio la preparacion)</label>
                                    <br>
                                    <input type="checkbox" id="estado0-info">
                                </div>  

                                <div class="form-group">
                                    <label>Fecha Estado 0</label>
                                    <input type="text" disabled class="form-control" id="fecha0-info">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 1 (Finalizo la preparacion)</label>
                                    <br>
                                    <input type="checkbox" disabled id="estado1-info">
                                </div>  

                                <div class="form-group">
                                    <label>Fecha Estado 1</label>
                                    <input type="text" disabled class="form-control" id="fecha1-info">
                                </div>


                                  <!-- -->

                                  <div class="form-group">
                                    <label>Estado 2 (Motorista va en camino)</label>
                                    <br>
                                    <input type="checkbox" disabled id="estado2-info">
                                </div>  

                                <div class="form-group">
                                    <label>Fecha Estado 2</label>
                                    <input type="text" disabled class="form-control" id="fecha2-info">
                                </div>

                                     <!-- -->

                                <div class="form-group">
                                    <label>Estado 3 (Motorista completo el encargo)</label>
                                    <br>
                                    <input type="checkbox" disabled id="estado3-info">
                                </div>  

                                <div class="form-group">
                                    <label>Fecha Estado 3</label>
                                    <input type="text" disabled class="form-control" id="fecha3-info">
                                </div>


                                <div class="form-group">
                                    <label>Cancelado Por</label>
                                    <input type="text" disabled class="form-control" id="canceladopor-info">
                                </div>

                                <div class="form-group">
                                    <label>Mensaje Cancelado</label>
                                    <input type="text" disabled class="form-control" id="mensajecancelado-info">
                                </div>

                                <!-- calificacion de la orden -->                           

                                <div class="form-group">
                                    <label>Calificacion Orden Encargo</label>
                                    <input type="text" disabled class="form-control" id="calificacion-info">
                                </div>

                                <div class="form-group">
                                    <label>Mensaje</label>
                                    <input type="text" disabled class="form-control" id="mensaje-info">
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

<!-- modal informacion sobre el cargo-->
<div class="modal fade" id="modalInfoCargo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion Cargo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-infocargo">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                                                          
                                       
                                <div class="form-group">
                                    <label>Sub Total</label>
                                    <input type="text" disabled class="form-control" id="subtotal">
                                </div>

                                <div class="form-group">
                                    <label>Tipo Cargo Aplicado</label>
                                    <input type="text" disabled class="form-control" id="tipocargo">
                                </div>

                                <div class="form-group">
                                    <label>Cargo Envio</label>
                                    <input type="text" disabled class="form-control" id="precioenvio">
                                </div>

                                <div class="form-group">
                                    <label>Aplico Cupon</label>
                                    <input type="text" disabled class="form-control" id="aplico">
                                </div>

                                <div class="form-group">
                                    <label>**** Cupon Para $0.00 Cargo de envio ****</label>                       
                                </div>
                                <div class="form-group">
                                    <label>Aplico Cupon</label>
                                    <input type="text" disabled class="form-control" id="aplico1">
                                </div>
                                <div class="form-group">
                                    <label>Minimo a comprar en carrito para aplicar</label>
                                    <input type="text" disabled class="form-control" id="c1carrito">
                                </div>

                                
                                <div class="form-group">
                                    <label>**** Cupon Descuento Dinero ****</label>                       
                                </div>
                                <div class="form-group">
                                    <label>Aplico Cupon</label>
                                    <input type="text" disabled class="form-control" id="aplico2">
                                </div>
                                <div class="form-group">
                                    <label>Descuento que se hara $</label>
                                    <input type="text" disabled class="form-control" id="c2carrito">
                                </div>
                                <div class="form-group">
                                    <label>Aplico para $0.00 cargo de envio</label>
                                    <input type="text" disabled class="form-control" id="c2aplico">
                                </div>
                                <div class="form-group">
                                    <label>Total a pagar cliente (Sub Total + Envio)</label>
                                    <input type="text" disabled class="form-control" id="c2pagar">
                                </div>


                                <div class="form-group">
                                    <label>**** Cupon Descuento Porcentaje ****</label>                       
                                </div>
                                <div class="form-group">
                                    <label>Aplico Cupon</label>
                                    <input type="text" disabled class="form-control" id="aplico3">
                                </div>
                                <div class="form-group">
                                    <label>Descuento %</label>
                                    <input type="text" disabled class="form-control" id="c3descuento">
                                </div>
                                <div class="form-group">
                                    <label>Minimo a comprar en carrito para aplicar</label>
                                    <input type="text" disabled class="form-control" id="c3minimo">
                                </div>

                                <div class="form-group">
                                    <label>Total a pagar cliente (Sub Total + Envio)</label>
                                    <input type="text" disabled class="form-control" id="c3pagar">
                                </div>

                                <div class="form-group">
                                    <label>**** Cupon Producto Gratis ****</label>                       
                                </div>
                                <div class="form-group">
                                    <label>Aplico Cupon</label>
                                    <input type="text" disabled class="form-control" id="aplico4">
                                </div>                                
                                <div class="form-group">
                                    <label>Minimo a comprar en carrito para aplicar</label>
                                    <input type="text" disabled class="form-control" id="c4minimo">
                                </div>
                                <div class="form-group">
                                    <label>Producto que regalaba</label>
                                    <input type="text" disabled class="form-control" id="c4producto">
                                </div>

                                <div class="form-group">
                                    <label>**** Cupon Donacion ****</label>                       
                                </div>
                                <div class="form-group">
                                    <label>Aplico Cupon</label>
                                    <input type="text" disabled class="form-control" id="aplico5">
                                </div>    
                                <div class="form-group">
                                    <label>Institución</label>
                                    <input type="text" disabled class="form-control" id="c5institucion">
                                </div>                            
                                <div class="form-group">
                                    <label>Donación</label>
                                    <input type="text" disabled class="form-control" id="c5donacion">
                                </div>
                                <div class="form-group">
                                    <label>Total (Sub Total + Cargo envio + Donacion)</label>
                                    <input type="text" disabled class="form-control" id="c5total">
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

<!-- modal informacion sobre el motorista-->
<div class="modal fade" id="modalInfoMotorista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion Motorista</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-motorista">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                                                          
                                       
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" disabled class="form-control" id="mnombre">
                                </div>

                                <div class="form-group">
                                    <label>Identificador</label>
                                    <input type="text" disabled class="form-control" id="midentificador">
                                </div>

                                <div class="form-group">
                                    <label>Fecha agarro la orden</label>
                                    <input type="text" disabled class="form-control" id="magarro">
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
 
<!-- modal informacion sobre el tipo de cargo al envio-->
<div class="modal fade" id="modalInfoTipo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion Sobre el cargo de envio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-infotipo">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                                                          
                                       
                                <div class="form-group">
                                    <label>Precio zona servicio</label>
                                    <input type="text" disabled class="form-control" id="cpreciozona">
                                </div>

                                <div class="form-group">
                                    <label>Aplico Tipo Cargo 1 (Cargo envio de zona)</label>
                                    <input type="text" disabled class="form-control" id="ccargo1">
                                </div>
                               
                                <div class="form-group">
                                    <label>Aplico Tipo Cargo 2 (Mitad de precio)</label>
                                    <input type="text" disabled class="form-control" id="ccargo2">
                                </div>

                                <div class="form-group">
                                    <label>Costo Mitad Precio Zona</label>
                                    <input type="text" disabled class="form-control" id="cmitad">
                                </div>

                                <div class="form-group">
                                    <label>Aplico Tipo Cargo 3 (Envio gratis)</label>
                                    <input type="text" disabled class="form-control" id="ccargo3">
                                </div>

                                <div class="form-group">
                                    <label>Aplico Tipo Cargo 4</label>
                                    <input type="text" disabled class="form-control" id="ccargo4">
                                </div>

                                <div class="form-group">
                                    <label>Minimo de compra para envio gratis</label>
                                    <input type="text" disabled class="form-control" id="cminimo">
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

        var ruta = "{{ url('/admin/encargos/buscar/num/encargo') }}/"+orden;
        $('#tablaDatatable').load(ruta);   

        $('#modalBuscar').modal('hide');     
    }     

    function infocliente(id){
        document.getElementById("formulario-infocliente").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/buscar/encargo/infocliente',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalInfoCliente').modal('show');
                    var datos = response.data.orden;

                    datos.forEach(function(value, index) {
                       
                        // informacion del cliente       
                        $('#iddireccion').val(datos[index].id);                  
                        $('#nombrecliente').val(datos[index].nombre); 
                        $('#zonaidentificador').val(datos[index].identificador);
                        $('#nombrezona').val(datos[index].nombrezona);
                        $('#nombreencargo').val(datos[index].nombreencargo);
                        $('#direccion').val(datos[index].direccion);
                        $('#numerocasa').val(datos[index].numero_casa);
                        $('#puntoreferencia').val(datos[index].punto_referencia);
                      
                        $('#latitud').val(datos[index].latitud);
                        $('#longitud').val(datos[index].longitud);
                        $('#latitudreal').val(datos[index].latitud_real);
                        $('#longitudreal').val(datos[index].longitud_real);
                                         
                        $('#telefonoreal').val(datos[index].telefono);
                     
                    });

                }else{
                    toastr.error('No encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function infoorden(id){
        document.getElementById("formulario-infoorden").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/buscar/encargo/infocliente',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalInfoOrden').modal('show');
                    var datos = response.data.orden;

                    datos.forEach(function(value, index) {

                        var estado = "";
                        if(datos[index].revisado == 1){
                            estado = "Pendiente";
                        }else if(datos[index].revisado == 2){
                            estado = "En Proceso";
                        }else if(datos[index].revisado == 3){
                            estado = "En Entrega";
                        }else if(datos[index].revisado == 4){
                            estado = "Entregado";
                        }else if(datos[index].revisado == 5){
                            estado = "Cancelado";
                        }
                        
                        $('#estadoorden-info').val(estado);
                        $('#nombreencargo-info').val(datos[index].nombreencargo);
                        $('#idencargo-info').val(datos[index].idencargo);

                        $('#fechaorden-info').val(datos[index].fecha_orden);
                        $('#subtotal-info').val(datos[index].precio_subtotal);

                        $('#precioenvio-info').val(datos[index].precio_envio);
                        $('#total-info').val(datos[index].total);

                        $('#gananciamoto-info').val(datos[index].ganancia_motorista);
                        
                        if(datos[index].estado_0 == 0){
                            $("#estado0-info").prop("checked", false);
                        }else{
                            $("#estado0-info").prop("checked", true);
                            $('#fecha0-info').val(datos[index].fecha_0);                       
                        }

                        if(datos[index].estado_1 == 0){
                            $("#estado1-info").prop("checked", false);
                        }else{
                            $("#estado1-info").prop("checked", true);
                            $('#fecha1-info').val(datos[index].fecha_1);                       
                        }

                        if(datos[index].estado_2 == 0){
                            $("#estado2-info").prop("checked", false);
                        }else{
                            $("#estado2-info").prop("checked", true);
                            $('#fecha2-info').val(datos[index].fecha_2);                       
                        }

                        if(datos[index].estado_3 == 0){
                            $("#estado3-info").prop("checked", false);
                        }else{
                            $("#estado3-info").prop("checked", true);
                            $('#fecha3-info').val(datos[index].fecha_3);                       
                        }

                        if(datos[index].revisado == 5){ // fue cancelado
                            
                            $('#mensajecancelado-info').val(datos[index].mensaje_cancelado);

                            if(datos[index].estado_3 == 0){
                                $('#canceladopor-info').val("Propietario");
                            }else{
                                $('#canceladopor-info').val("Cliente");
                            }
                        }

                        $('#calificacion-info').val(datos[index].calificacion);    
                        $('#mensaje-info').val(datos[index].mensaje);    
                        
                                      
                    });                  

                }else{
                    toastr.error('No encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
    
  
    function infomotorista(id){ 
        document.getElementById("formulario-motorista").reset();

        spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id); // id orden_encargo

        axios.post('/admin/encargos/ordenes/ver-motorista-asignado', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);

                    if(response.data.success == 1){

                        $('#modalInfoMotorista').modal('show');
                        var datos = response.data.orden; 

                        datos.forEach(function(value, index) {
                            $('#mnombre').val(datos[index].nombre);
                            $('#midentificador').val(datos[index].identificador);
                            $('#magarro').val(datos[index].fecha_agarrada);      
                        });           

                    }else{
                        toastr.error('Sin motorista'); 
                    }

        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }
 
    // pasar id para ver producto de la orden
    function producto(id){
        window.location.href="{{ url('/admin/encargos/ordenes/productos-ver') }}/"+id;    
    }
 
    // latitud y longitud del puntero gps
    function mapa(){
        var id = document.getElementById('iddireccion').value;

        // comprobar que latitud y longitud no esten vacios
        var la = document.getElementById('latitud').value;
        var lo = document.getElementById('longitud').value;

        if(la === '' || lo === ''){
            toastr.error('Latitud o Longitud estan vacios'); 
            return;
        }

        window.location.href="{{ url('/admin/encargos/ordenes/direccion/mapa-gps') }}/"+id;
    }
 
    // latitud y longitud real donde guardo la direccion
    function mapa2(){
        var id = document.getElementById('iddireccion').value;

        var la = document.getElementById('latitudreal').value;
        var lo = document.getElementById('longitudreal').value;

        if(la === '' || lo === ''){
            toastr.error('Latitud o Longitud estan vacios'); 
            return;
        } 

        window.location.href="{{ url('/admin/encargos/ordenes/direccion/mapa-gps-real') }}/"+id;
    }
 
   </script>
  
 
 
 @stop