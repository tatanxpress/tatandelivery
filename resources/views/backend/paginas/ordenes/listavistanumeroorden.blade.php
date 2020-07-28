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
                                    <input type="hidden" id="id-orden">
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
                                    <label>Nombre Zona</label>
                                    <input type="text" disabled class="form-control" id="nombrezona">
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
                                    <label>Pidio desde</label>
                                    <input type="text" disabled class="form-control" id="versionapp">
                                </div>

                                <div class="form-group">
                                    <label>Latitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="latitud">
                                </div>

                                <div class="form-group">
                                    <label>Longitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="longitud">
                                </div>

                             

                                <button type="button" onclick="mapa()" class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                        Mapa
                                </button>  

                                <div class="form-group">
                                    <label>Latitud Real</label>
                                    <input type="text" disabled class="form-control" id="latitudreal">
                                </div>

                                <div class="form-group">
                                    <label>Longitud real</label>
                                    <input type="text" disabled class="form-control" id="longitudreal">
                                </div>

                               

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

<!-- modal editar latitud y longitud de esta orden-->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <input type="hidden" id="id-orden-editar">
                                </div>
                                
                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="latitud-editar">
                                </div>

                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="longitud-editar">
                                </div>

                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input type="text" maxlength="400" class="form-control" id="direccion-editar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Editar</button>
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
                                    <label>Nota orden</label>
                                    <input type="text" disabled class="form-control" id="notaorden">
                                </div>

                                <div class="form-group">
                                    <label>Cambio vuelto</label>
                                    <input type="text" disabled class="form-control" id="cambiovuelto">
                                </div>

                                <div class="form-group">
                                    <label>Estado 2 (propietario respondio con tiempo de espera)</label>
                                    <br>
                                    <input type="checkbox" id="estado2" disabled style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 2</label>
                                    <input type="text" disabled class="form-control" id="fecha2">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo dado por el propietario</label>
                                    <input type="text" disabled class="form-control" id="minutosespera">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo extra de la zona</label>
                                    <input type="text" disabled class="form-control" id="minutosextra">
                                </div>

                                <div class="form-group">
                                    <label>Cliente espera en total (Minutos)</label>
                                    <input type="text" disabled class="form-control" id="minutostotal">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 3 (cliente responde, que esperara tiempo)</label>
                                    <br>
                                    <input type="checkbox" id="estado3" disabled style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 3</label>
                                    <input type="text" disabled class="form-control" id="fecha3">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 4 (propietario inicia la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado4" disabled  style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 4</label>
                                    <input type="text" disabled class="form-control" id="fecha4">
                                </div>

                                <div class="form-group">
                                    <label>Hora Estimada Entrega al Cliente (hora2 + tiempo extra)</label>
                                    <input type="text" disabled class="form-control" id="estimada">
                                </div>

                                 <!-- -->

                                 <div class="form-group">
                                    <label>Estado 5 (propietario finalizo de preparar la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado5" disabled  style="width: 20px; height: 20px;">
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
                                    <input type="checkbox" id="estado6" disabled style="width: 20px; height: 20px;"> 
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
                                    <input type="checkbox" id="estado7" disabled style="width: 20px; height: 20px;">
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
                                    <input type="checkbox" id="estado8" disabled style="width: 20px; height: 20px;">
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

                                <div class="form-group">
                                    <label>Cancelado por</label>
                                    <input type="text" disabled class="form-control" id="canceladapor">
                                </div>

                                <div class="form-group">
                                    <label>Cancelado orden (Cancelacion desde panel del control)</label>
                                    <input type="text" disabled class="form-control" id="ordencancelada">
                                </div>

                                <div class="form-group">
                                    <label>Copia precio Zona para este servicio</label>
                                    <input type="text" disabled class="form-control" id="preciozona">
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
                <form id="formulario-infomotorista">
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

    // editar latitud y longitud de esta orden
    function modalEditar(id){
        document.getElementById("formulario-editar").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/buscar/orden/infocliente',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalEditar').modal('show');
                    var datos = response.data.orden;

                    datos.forEach(function(value, index) {
                       
                        // informacion del cliente        
                        $('#id-orden-editar').val(datos[index].id);
                      
                        $('#latitud-editar').val(datos[index].latitud);
                        $('#longitud-editar').val(datos[index].longitud);
                        $('#direccion-editar').val(datos[index].direccion);           
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

    function editar(){
        var id = document.getElementById('id-orden-editar').value;
        var latitud = document.getElementById('latitud-editar').value;
        var longitud = document.getElementById('longitud-editar').value;
        var direccion = document.getElementById('direccion-editar').value;

        if(latitud === ''){
            toastr.error('Latitud es requerida'); 
            return;
        }

        if(latitud.length > 50){
            toastr.error('Latitud maximo 50 caracteres'); 
            return;
        }

        if(longitud === ''){
            toastr.error('Longitud es requerida'); 
            return;
        }

        if(longitud.length > 50){
            toastr.error('Longitud maximo 50 caracteres'); 
            return;
        } 

        if(direccion === ''){
            toastr.error('Direccion es requerida'); 
            return;
        }

        if(direccion.length > 400){
            toastr.error('Direccion maximo 400 caracteres'); 
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('latitud', latitud);
        formData.append('longitud', longitud);    
        formData.append('direccion', direccion);      

        axios.post('/admin/editar/orden/punto-gps', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                $('#modalEditar').modal('hide');
                if(response.data.success == 1){
                    toastr.success('Actualizado');
                    
                }else{
                    toastr.error('Error al editar');  
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });        
    }
 
    function buscar(){
        var orden = document.getElementById('orden').value;

        if(orden === ''){
            toastr.error('# Orden es Requerida'); 
            return;
        }

        var ruta = "{{ url('/admin/buscar/num/orden') }}/"+orden;
        $('#tablaDatatable').load(ruta);   

        $('#modalBuscar').modal('hide');     
    }   

    function infocliente(id){
        document.getElementById("formulario-infocliente").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/buscar/orden/infocliente',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalInfoCliente').modal('show');
                    var datos = response.data.orden;

                    datos.forEach(function(value, index) {
                       
                        // informacion del cliente        
                        $('#id-orden').val(datos[index].id);
                        
                        $('#nombrecliente').val(datos[index].nombre);
                        $('#zonaidentificador').val(datos[index].identificador);
                        $('#nombrezona').val(datos[index].nombrezona);
                        $('#direccion').val(datos[index].direccion);
                        $('#numerocasa').val(datos[index].numero_casa);
                        $('#puntoreferencia').val(datos[index].punto_referencia);

                        if(datos[index].movil_ordeno == 1){ 
                            $('#versionapp').val("Iphone");
                        }else if(datos[index].movil_ordeno == 2){
                            $('#versionapp').val("Android");
                        }else{
                            $('#versionapp').val("No ha actualizado");
                        }
                      
                        $('#latitud').val(datos[index].latitud);
                        $('#longitud').val(datos[index].longitud);
                        $('#latitudreal').val(datos[index].latitud_real);
                        $('#longitudreal').val(datos[index].longitud_real);
                        $('#copiaenvio').val(datos[index].copia_envio);                      
                        $('#telefonoreal').val(datos[index].phone);
                     
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
       
        axios.post('/admin/buscar/orden/infoorden',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalInfoOrden').modal('show');
                    var datos = response.data.orden;

                    datos.forEach(function(value, index) {

                        $('#notaorden').val(datos[index].nota_orden);
                        $('#cambiovuelto').val(datos[index].cambio);
                        $('#minutosespera').val(datos[index].hora_2);
                        $('#minutosextra').val(datos[index].copia_tiempo_orden);
                        $('#minutostotal').val(datos[index].minutostotal);

                        if(datos[index].cancelado_extra == 1){
                            $('#ordencancelada').val("Si");
                        }else{
                            $('#ordencancelada').val("No");
                        }   

                        if(datos[index].estado_2 == 0){
                            $("#estado2").prop("checked", false);
                        }else{
                            $("#estado2").prop("checked", true);
                            $('#fecha2').val(datos[index].fecha_2);                       
                        }

                        if(datos[index].estado_3 == 0){
                            $("#estado3").prop("checked", false);
                        }else{
                            $("#estado3").prop("checked", true);
                            $('#fecha3').val(datos[index].fecha_3);                       
                        }

                        if(datos[index].estado_4 == 0){
                            $("#estado4").prop("checked", false);
                        }else{
                            $("#estado4").prop("checked", true);
                            $('#fecha4').val(datos[index].fecha_4);    
                            $('#estimada').val(datos[index].estimada);                                            
                        }

                        if(datos[index].estado_5 == 0){
                            $("#estado5").prop("checked", false);
                        }else{
                            $("#estado5").prop("checked", true);
                            $('#fecha5').val(datos[index].fecha_5);                       
                        }

                        if(datos[index].estado_6 == 0){
                            $("#estado6").prop("checked", false);
                        }else{
                            $("#estado6").prop("checked", true);
                            $('#fecha6').val(datos[index].fecha_6);                       
                        }

                        if(datos[index].estado_7 == 0){
                            $("#estado7").prop("checked", false);
                        }else{
                            $("#estado7").prop("checked", true);
                            $('#fecha7').val(datos[index].fecha_7);                       
                        }

                        if(datos[index].estado_8 == 0){
                            $("#estado8").prop("checked", false);
                        }else{
                            $("#estado8").prop("checked", true);
                            $('#fecha8').val(datos[index].fecha_8);   
                            $('#mensaje8').val(datos[index].mensaje_8);                                 
                        } 

                        
                                         
                        $('#canceladapor').val(datos[index].canceladopor); 

                        if(datos[index].cancelado_extra == 1){
                            $('#ordencancelada').val("CANCELADO");
                            $('#mensaje8').val(datos[index].mensaje_8);
                        }
                        


                        $('#preciozona').val(datos[index].copia_envio); 
                        $('#gananciamotorista').val(datos[index].ganancia_motorista);       

                              
                                 
                                      
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
    
    function infotipo(id){
        document.getElementById("formulario-infotipo").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/buscar/orden/infotipocargo',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalInfoTipo').modal('show');
                                  
                    $('#cpreciozona').val(response.data.precio);
                    if(response.data.tipo == 1){
                        $('#ccargo1').val("Si");
                    }else if(response.data.tipo == 2){
                        $('#ccargo2').val("Si");
                        $('#cmitad').val(response.data.mitad);
                    }else if(response.data.tipo == 3){
                        $('#ccargo3').val("Si");
                    }else if(response.data.tipo == 4){
                        $('#ccargo4').val("Si");
                        $('#cminimo').val(response.data.mingratis);
                    }
                }else{
                    toastr.error('No encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function infocargo(id){
        document.getElementById("formulario-infocargo").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/buscar/orden/infocargo',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalInfoCargo').modal('show');
                    var datos = response.data.orden;

                    datos.forEach(function(value, index) {

                        $('#subtotal').val(datos[index].precio_total);
                        $('#precioenvio').val(datos[index].precio_envio);
                        $('#aplico').val(datos[index].aplico);

                        if(datos[index].tipo_cargo == 1){
                            $('#tipocargo').val("Precio de: Zona");                            
                        }else if(datos[index].tipo_cargo == 2){
                            $('#tipocargo').val("Precio de: Mitad de precio");                            
                        }else if(datos[index].tipo_cargo == 3){
                            $('#tipocargo').val("Precio de: Envio Gratis");                            
                        }else if(datos[index].tipo_cargo == 4){
                            $('#tipocargo').val("Precio de: Minimo de compra para envio $0.00");                            
                        }   
                                      
                        //que tipo de cupon aplico
                        if(datos[index].tipocupon == 1){
                            $('#aplico1').val("Si");
                            $('#c1carrito').val(datos[index].dinerocarrito);
                        }  
                        else if(datos[index].tipocupon == 2){
                            $('#aplico2').val("Si");
                            $('#c2carrito').val(datos[index].dinerocarrito);
                            if(datos[index].aplicoenvio == 0){
                                $('#c2aplico').val("No");
                            }else{
                                $('#c2aplico').val("Si");
                            }
                            $('#c2pagar').val(datos[index].pagara);
                        }
                        else if(datos[index].tipocupon == 3){
                            $('#aplico3').val("Si");

                            $('#c3minimo').val(datos[index].dinerocarrito);
                            $('#c3descuento').val(datos[index].porcentaje);
                            $('#c3pagar').val(datos[index].pagara);
                            
                        }
                        else if(datos[index].tipocupon == 4){
                            $('#aplico4').val("Si");
                            $('#c4minimo').val(datos[index].dinerocarrito);
                            $('#c4producto').val(datos[index].producto);
                        }    
                        else if(datos[index].tipocupon == 5){
                            $('#aplico5').val("Si");
                            $('#c5institucion').val(datos[index].institucion);                            
                            $('#c5donacion').val(datos[index].dinero);
                            $('#c5total').val(datos[index].total);
                        }      
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
        document.getElementById("formulario-infomotorista").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/buscar/orden/infomotorista',{
        'id': id 
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
                toastr.error('Error del servidor');    
        });
    }
 
    // pasar id para ver producto de la orden
    function producto(id){
        window.location.href="{{ url('/admin/ordenes/listaproducto') }}/"+id;
    }

    // latitud y longitud del puntero gps
    function mapa(){
        var id = document.getElementById('id-orden').value;

        // comprobar que latitud y longitud no esten vacios
        var la = document.getElementById('latitud').value;
        var lo = document.getElementById('longitud').value;

        if(la === '' || lo === ''){
            toastr.error('Latitud o Longitud estan vacios'); 
            return;
        }

        window.location.href="{{ url('/admin/mapa/orden/cliente/direccion') }}/"+id;
    }

    // latitud y longitud real donde guardo la direccion
    function mapa2(){
        var id = document.getElementById('id-orden').value;

        var la = document.getElementById('latitudreal').value;
        var lo = document.getElementById('longitudreal').value;

        if(la === '' || lo === ''){
            toastr.error('Latitud o Longitud estan vacios'); 
            return;
        }  

        window.location.href="{{ url('/admin/mapa/orden/cliente/direccion-real') }}/"+id;
    }
 
 
   </script>
  
 
 
 @stop