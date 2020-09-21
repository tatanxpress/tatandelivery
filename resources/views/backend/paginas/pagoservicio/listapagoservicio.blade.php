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
         
          <button type="button" onclick="modalReporte()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Generar reporte de ordenes Completadas
          </button>  
 
          <button type="button" onclick="modalReporte3()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte orden cancelada
          </button> 

          <button type="button" onclick="modalReporte5()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte Productos Vendidos
          </button> 

          <button type="button" onclick="modalReporte6()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte Tipo cargo de envio que se aplico
          </button> 
          
          </br>
          </br>
          <button type="button" onclick="modalReporte7()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Reporte Encargos Finalizados
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

<!-- modal buscar NO USO 20/09/2020 -->
<div class="modal fade" id="modalBuscar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar ordenes COMPLETADAS del servicio</h4>
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

                                <div class="form-group">
                                    <label style="color:#191818">Tipo Cupon</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="tipocupon" required>                                                                                         
                                            <option value="0">Ninguno</option>
                                            <option value="1">Todos</option>
                                            <option value="2">Envio Gratis</option>
                                            <option value="3">Descuento Dinero</option>
                                            <option value="4">Descuento Porcentaje</option>
                                            <option value="5">Producto Gratis</option>        
                                            <option value="6">Donacion</option>                        
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

                                <div class="form-group">
                                    <label style="color:#191818">Tipo de Busqueda</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="tipocupon1" required>                                                                                         
                                            <option value="0">Todos</option>                                          
                                            <option value="1">Envio Gratis</option>
                                            <option value="2">Descuento Dinero</option>
                                            <option value="3">Descuento Porcentaje</option>
                                            <option value="4">Producto Gratis</option>
                                            <option value="5">Donación</option>
                                            <option value="6">Solo pagadas a Propietarios</option>
                                            <option value="7">No pagadas a Propietarios</option>
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
                <button type="button" class="btn btn-primary" onclick="reporte()">Completo</button>
                <button type="button" class="btn btn-primary" onclick="reporteTablas()">Solo Tablas</button>
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


<!-- modal reporte5 -->
<div class="modal fade" id="modalReporte5">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reporte para productos vendidos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reporte5">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid-reporte5" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reporte5">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reporte5">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte5()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal reporte6 -->
<div class="modal fade" id="modalReporte6">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reporte Tipo de cargo aplicado</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reporte6">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid-reporte6" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reporte6">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reporte6">
                                </div>

                                 
                                <div class="form-group">
                                    <label style="color:#191818">Tipo cargo de envio</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="tipocargo-select" required>                                                                                         
                                            <option value="1">Cargo de envio de zona servicio</option>
                                            <option value="2">Cargo de envio a mitad de precio</option>
                                            <option value="3">Cargo de envio gratis de zona servicio</option>
                                            <option value="4">Cargo de envio supero minimo de compra</option>                                
                                        </select>
                                    </div>  
                                </div>

                                <div class="form-group">
                                    <label>Revuelto Tipo cargo de envio</label>
                                    <br>
                                    <input type="checkbox" id="revuelto">
                                </div>

                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte6()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal reporte7 -->
<div class="modal fade" id="modalReporte7">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reporte Encargos Finalizados</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reporte7">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="servicioid-reporte7" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-reporte7">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-reporte7">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reporte7()">Buscar</button>
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

    // buscar ordenes completadas
    function modalBuscar(){
        document.getElementById("formulario-buscar").reset();
        $('#modalBuscar').modal('show');
    }

    // modal de generar reporte de ordenes completadas
    function modalReporte(){
        document.getElementById("formulario-reporte").reset();
        $('#modalReporte').modal('show');
    }

    // modal de ordenes canceladas
    function modalReporte3(){
        document.getElementById("formulario-reporte3").reset();
        $('#modalReporte3').modal('show');
    }

    // modal de productos vendidos
    function modalReporte5(){
        document.getElementById("formulario-reporte5").reset();
        $('#modalReporte5').modal('show');
    }

    // reporte de tipo de cargo de envio que se hizo
    function modalReporte6(){
        document.getElementById("formulario-reporte6").reset();
        $('#modalReporte6').modal('show');
    }

    // reporte de ordenes encargo finalizados
    function modalReporte7(){
        document.getElementById("formulario-reporte7").reset();
        $('#modalReporte7').modal('show');
    }

    // lista de ordenes completadas NO USADO 20/09/2020
    function buscar(){
        var servicioid = document.getElementById('servicioid').value;
        var fechadesde = document.getElementById('fechadesde').value;
        var fechahasta = document.getElementById('fechahasta').value;            
        var cupon = document.getElementById('tipocupon').value;

        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            var ruta = "{{ url('/admin/buscarservicio') }}/"+servicioid+"/"+fechadesde+"/"+fechahasta+"/"+cupon;
            $('#tablaDatatable').load(ruta); 
        }
    }
 
    // reporte de ordenes completadas
    function reporte(){
        var servicioid = document.getElementById('servicioid-reporte').value;
        var fechadesde = document.getElementById('fechadesde-reporte').value;
        var fechahasta = document.getElementById('fechahasta-reporte').value;     
        var cupon = document.getElementById('tipocupon1').value;
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){
            window.open("{{ URL::to('admin/generar/reporte3') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta + "/" + cupon);
        } 
    }

    function reporteTablas(){
        var servicioid = document.getElementById('servicioid-reporte').value;
        var fechadesde = document.getElementById('fechadesde-reporte').value;
        var fechahasta = document.getElementById('fechahasta-reporte').value;     
        var cupon = document.getElementById('tipocupon1').value;
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){
            window.open("{{ URL::to('admin/generar/reporte3-tablas') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta + "/" + cupon);
        }
    }

    // reporte de ordenes canceladas
    function reporte3(){
        var servicioid = document.getElementById('servicioid-reporte3').value;
        var fechadesde = document.getElementById('fechadesde-reporte3').value;
        var fechahasta = document.getElementById('fechahasta-reporte3').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/generar/reporte5') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta);

        }
    }    

    // reporte de productos vendidos
    function reporte5(){
        var servicioid = document.getElementById('servicioid-reporte5').value;
        var fechadesde = document.getElementById('fechadesde-reporte5').value;
        var fechahasta = document.getElementById('fechahasta-reporte5').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/generar/reporte7') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta);

        }
    }

    // reporte de tipos de cargo de envio
    function reporte6(){
        var servicioid = document.getElementById('servicioid-reporte6').value;
        var fechadesde = document.getElementById('fechadesde-reporte6').value;
        var fechahasta = document.getElementById('fechahasta-reporte6').value;  
        var tipocargo = document.getElementById('tipocargo-select').value;  
        var revuelto = document.getElementById('revuelto').checked;

        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            // ordenes del servicio, revueltas con tipo cargo de envio
            if(revuelto){                
                if(revuelto){
                    window.open("{{ URL::to('admin/generar/tipocargo') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta + "/" + 0);
                }
            }else{
                // precio de zona servicio
                if(tipocargo == 1){
                    window.open("{{ URL::to('admin/generar/tipocargo') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta + "/" + 1);

                }else if(tipocargo == 2){ // precio de a mitad de precio
                    window.open("{{ URL::to('admin/generar/tipocargo') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta + "/" + 2);

                }else if(tipocargo == 3){ // precio envio gratis por zona servicio
                    window.open("{{ URL::to('admin/generar/tipocargo') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta + "/" + 3);

                }else if(tipocargo == 4){ // precio supero minimo de compra envio gratis
                    window.open("{{ URL::to('admin/generar/tipocargo') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta + "/" + 4);

                }
            }
        }
        
        


        


          
        
        

        if(retorno){

           
        }
    }

    // reporte de ordenes encargo
    function reporte7(){
        var servicioid = document.getElementById('servicioid-reporte7').value;
        var fechadesde = document.getElementById('fechadesde-reporte7').value;
        var fechahasta = document.getElementById('fechahasta-reporte7').value;            
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/generar/reporte-encargo') }}/" + servicioid + "/" +  fechadesde + "/" + fechahasta);

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
    
  
  
  
   </script>
  
 
 
 @stop