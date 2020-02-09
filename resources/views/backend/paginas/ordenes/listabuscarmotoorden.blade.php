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
                    Filtro para Datos Basicos
          </button>  

          <button type="button" style="margin-left:15px" onclick="reportePago()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte pago motorista
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
                                    <label>Total marcado gratis</label>
                                    <input type="text" disabled class="form-control" id="totalgratis">
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

<!-- modal vista filtro para orden prestado -->
<div class="modal fade" id="modalFiltroPrestado">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtrar datos, para ver ordenes motorista prestado</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtroprestado">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                            <div class="form-group">
                                    <label style="color:#191818">Motorista identificador</label>
                                    <br>
                                    <div>  

                                        <select class="form-control selectpicker" id="motoid-pre" data-live-search="true" required>   
                                            @foreach($moto as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-pre">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-pre">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtroPrestado()">Filtrar</button>
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
 
<!-- modal select buscar servicio para cobro de motorista prestado -->
<div class="modal fade" id="modalFiltroServicio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reporte de cobro por motorista prestado</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-servicio">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                <div class="form-group">                                   
                                    <input type="hidden" id="id-motorista">
                                    <input type="hidden" id="fecha1-cobro">
                                    <input type="hidden" id="fecha2-cobro">
                                </div>

                                <div class="form-group">

                                    <label style="color:#191818">Servicio</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-servicio">
                                            <option value="0" selected>Vacio</option>
                                        </select>
                                    </div>
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte()">Reporte</button>
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
                    $('#totalgratis').val(response.data.totalmarcagratis);
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

    // buscar ordenes donde se presto el motorista
    function filtroPrestado(){
        var idmoto = document.getElementById('motoid-pre').value;
        var fechadesde = document.getElementById('fechadesde-pre').value;
        var fechahasta = document.getElementById('fechahasta-pre').value;
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            var ruta = "{{ url('/admin/buscar2/moor') }}/"+idmoto+"/"+fechadesde+"/"+fechahasta;
            $('#tablaDatatable').load(ruta);
        }
    }

    // ver datos de registros de motorista prestado
    function filtroRegistro(){
        var idmoto = document.getElementById('motoid-registro').value;
        var fechadesde = document.getElementById('fechadesde-registro').value;
        var fechahasta = document.getElementById('fechahasta-registro').value;            
        
        $('#id-motorista').val(idmoto);
        $('#fecha1-cobro').val(fechadesde);
        $('#fecha2-cobro').val(fechahasta);

        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            spinHandle = loadingOverlay().activate();

            var formData = new FormData();
            formData.append('id', idmoto);
            formData.append('fecha1', fechadesde);
            formData.append('fecha2', fechahasta);
                
            axios.post('/admin/buscar3/orden/servicio',formData,{
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    console.log(response);
                if(response.data.success == 1){
                   // llenar id el select de los servicios a cobrar
                   
                   var tipo = document.getElementById("select-servicio");
                    // limpiar select
                    document.getElementById("select-servicio").options.length = 0;
                    if(response.data.agrupado.length == 0){                          
                        tipo.options[0] = new Option('Ninguna disponible', 0); 
                    }else{
                        $.each(response.data.agrupado, function( key, val ){  
                            tipo.options[key] = new Option(val.identificador, val.id);
                        });
                    }
                
                   $('#modalFiltroServicio').modal('show');

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

    // generar reporte de cobro a servicio por motorista prestado
    function reporte(){
        var idmoto = document.getElementById("id-motorista").value;
        var select = document.getElementById("select-servicio").value;
        var fecha1 = document.getElementById("fecha1-cobro").value;
        var fecha2 = document.getElementById("fecha2-cobro").value;

        window.open("{{ URL::to('admin/generar/reporte1') }}/" + idmoto + "/" + select + "/" + fecha1 + "/" + fecha2);
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
 
   </script>
  
 
 
 @stop