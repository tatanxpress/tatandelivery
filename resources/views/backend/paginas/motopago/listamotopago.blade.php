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
             <h1>Lista de pagos a motorista</h1>
           </div>  
           <button type="button" onclick="modalRegistro()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo registro
          </button> 

           <button type="button" onclick="modalBuscar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar total pagado
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

<!-- modal registro -->
<div class="modal fade" id="modalRegistro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Registro de pago</h4>
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

                                <div class="form-group">
                                    <label>Pago</label>
                                    <input type="number" step="0.1" class="form-control" id="pago">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardar()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

    <!-- modal buscar -->
<div class="modal fade" id="modalBuscar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar total pagado</h4>
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
                                        <select class="form-control selectpicker" id="motorista-identificador" data-live-search="true" required>   
                                            @foreach($moto as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
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
 
 <!-- modal total -->
 <div class="modal fade" id="modalTotal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Total generado este servicio, solo estado 5</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-total">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Total pagado $</label>
                                    <input type="text" disabled class="form-control" id="total">
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

 <!-- incluir tabla --> 
 <script type="text/javascript">	 
    $(document).ready(function(){       
        var ruta = "{{ URL::to('admin/motopago/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

   <script>

    function modalRegistro(){
        document.getElementById("formulario-registro").reset();
        $('#modalRegistro').modal('show');
    }

    function modalBuscar(){
        $('#modalBuscar').modal('show');
    }

    function buscar(){
        var id = document.getElementById('motorista-identificador').value;  

        spinHandle = loadingOverlay().activate();

        var formData = new FormData();
        formData.append('id', id);
            
        axios.post('/admin/motopago/pago/ver',formData,{
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
           
                respuesta2(response);
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle); 
            toastr.error('Error del servidor');    
        });
    }

    function respuesta2(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
              
            $('#total').val(response.data.total);
            $('#modalTotal').modal('show');
        } else if(response.data.success == 2){
            toastr.error('id no encontrado');
        }
        else {
            toastr.error('Error desconocido');
        }       
    } 

    // nuevo registro
    function guardar(){
        var idmoto = document.getElementById('motoid').value;
        var fechadesde = document.getElementById('fechadesde').value;
        var fechahasta = document.getElementById('fechahasta').value;
        var pago = document.getElementById('pago').value;
        
        var retorno = validarNuevo(fechadesde, fechahasta, pago);

        if(retorno){

            spinHandle = loadingOverlay().activate();

            var formData = new FormData();
            formData.append('id', idmoto);
            formData.append('fecha1', fechadesde);
            formData.append('fecha2', fechahasta);
            formData.append('pago', pago);
                 
            axios.post('/admin/registro/pago/motorista',formData,{
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                   
                    respuesta(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
            });
        }
    }

    function respuesta(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
            toastr.success('Registrado');           
           
            var ruta = "{{ url('/admin/motopago/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalRegistro').modal('hide');  
            
        } else if(response.data.success == 2){
            toastr.error('Error al guardar');
        }
        else {
            toastr.error('Error desconocido');
        }       
    }

    function validarNuevo(fechadesde, fechahasta, pago){

        if(fechadesde === ''){
            toastr.error("fecha desde es requerido");
            return;
        } 

        if(fechahasta === ''){
            toastr.error("fecha hasta desde es requerido");
            return;
        }

        if(pago === ''){
            toastr.error("pago es requerido");
            return;
        }

        return true;
    }

  
 
   </script>
  
 
 
 @stop