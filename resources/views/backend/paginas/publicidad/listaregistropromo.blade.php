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
             <h1>Registros de ingresos de Publicidad o Promociones</h1>
           </div>    
           <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                 <i class="fas fa-pencil-alt"></i>
                     Nueva registro
           </button>  
           <button type="button" style="margin-left" onclick="abrirModalFiltro()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro por servicios
            </button>  

            <button type="button" style="margin-left" onclick="abrirModalFiltro2()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Registros a vencer
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
            </div>
        </div>
     </section>
 
 <!-- modal nuevo -->
 <div class="modal fade" id="modalAgregar">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Nueva registro</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form id="formulario-nuevo">
                     <div class="card-body">
                         <div class="row">  
                             <div class="col-md-12">
                               
                                <div class="form-group">
                                    <label style="color:#191818">Siempre mover el select, sino tomara ID 1</label>
                                    <label style="color:#191818">Servicio identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="selectservicio-nuevo" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>  
                                
                                <div class="form-group">
                                    <label>Fecha desde:</label>
                                    <input type="date" class="form-control" id="fecha1-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta:</label>
                                    <input type="date" class="form-control" id="fecha2-nuevo">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Tipo de registro</label>
                                    <br>
                                    <div>
                                        <select class="form-control" name="" id="selecttipo-nuevo" required>   
                                                <option value="0">Promocion</option>
                                                <option value="1">Publicidad</option>
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label>Ingreso $:</label>
                                    <input type="number" step="0.01" class="form-control" id="pago-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-nuevo">
                                </div>

                             </div> 
                         </div>
                     </div>
                 </form>
             </div>
             <div class="modal-footer justify-content-between">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                 <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
             </div>          
         </div>        
     </div>      
 </div>
  

<!-- modal filtro -->
<div class="modal fade" id="modalFiltro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal filtro por servicio</h4>
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
                                    <label style="color:#191818">Servicio identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="selectservicio-filtro" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label style="color:#191818">Tipo de registro</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selecttipo-filtro" required>   
                                                <option value="0">Promocion</option>
                                                <option value="1">Publicidad</option>
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
                <button type="button" class="btn btn-primary" onclick="filtrar()">Filtrar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar -->
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
                                    <label>Fecha desde:</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="date" class="form-control" id="fecha1-editar">
                                </div>

                                <div class="form-group">
                                    <label>Fecha hasta:</label>
                                    <input type="date" class="form-control" id="fecha2-editar">
                                </div>

                                <div class="form-group">
                                    <label>Ingreso $:</label>
                                    <input type="number" step="0.01" class="form-control" id="pago-editar">
                                </div>

                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-editar">
                                </div>

                            </div> 
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>



<!-- modal filtro para ver registro a vencer -->
<div class="modal fade" id="modalFiltro2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal registro fecha vencimiento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro2">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                         
                              
                            <div class="form-group">
                                    <label>Fecha:</label>
                                    <input type="date" class="form-control" id="fecha">
                                </div>

                            </div>
                        </div>
                    </div> 
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtrar2()">Filtrar</button>
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
      
       var ruta = "{{ URL::to('admin/registropromo/tabla/lista') }}";
       $('#tablaDatatable').load(ruta);
     });   
  </script>
 
   <script>
  
     
     // modal nuevo
     function abrirModalAgregar(){
         document.getElementById("formulario-nuevo").reset();
         $('#modalAgregar').modal('show');
     }
 
    function nuevo(){
        var idservicio = document.getElementById('selectservicio-nuevo').value;
        var tipo = document.getElementById('selecttipo-nuevo').value;
        var fecha1 = document.getElementById('fecha1-nuevo').value;
        var fecha2 = document.getElementById('fecha2-nuevo').value;
        var pago = document.getElementById('pago-nuevo').value;
        var descripcion = document.getElementById('descripcion-nuevo').value;

        var retorno = validacion(fecha1, fecha2, pago);

        if(retorno){
            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('idservicio', idservicio);
            formData.append('tipo', tipo);
            formData.append('fecha1', fecha1);
            formData.append('fecha2', fecha2);
            formData.append('pago', pago);
            formData.append('descripcion', descripcion);

            axios.post('/admin/registropromo/nuevo', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    respuestaNuevo(response);
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
        }
    }


    function validacion(fecha1, fecha2, pago){
        
        if(fecha1 === ''){
            toastr.error("Fecha 1 es requerido");
            return;
        }

        if(fecha2 === ''){
            toastr.error("Fecha 2 es requerido");
            return;
        }

        if(pago === ''){
            toastr.error("Ingreso pago es requerido");
            return;
        }

        return true;
    }
 
    function respuestaNuevo(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Registro agregado');
        
            var ruta = "{{ URL::to('admin/registropromo/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');    
         
        }else {
            toastr.error('Error desconocido');
        }
    }

    function informacion(id){

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
    
        axios.post('/admin/registropromo/informacion', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    if(response.data.success == 1){

                        $('#modalEditar').modal('show');
                        $('#id-editar').val(response.data.registro.id);
                        $('#fecha1-editar').val(response.data.registro.fecha1);
                        $('#fecha2-editar').val(response.data.registro.fecha2);
                        $('#pago-editar').val(response.data.registro.pago);
                        $('#descripcion-editar').val(response.data.registro.descripcion);

                    }else{
                        toastr.error('ID no encontrado'); 
                    } 
                }) 
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });

     }
 
     function editar(){
        var id = document.getElementById('id-editar').value;
        var fecha1 = document.getElementById('fecha1-editar').value;
        var fecha2 = document.getElementById('fecha2-editar').value;
        var pago = document.getElementById('pago-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;

        var retorno = validacion(fecha1, fecha2, pago);

        if(retorno){
            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha1', fecha1);
            formData.append('fecha2', fecha2);
            formData.append('pago', pago);
            formData.append('descripcion', descripcion);

            axios.post('/admin/registropromo/editar', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    respuestaEditar(response);
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
        }
     }

    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Registro actualizado');
        
            var ruta = "{{ URL::to('admin/registropromo/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');    
         
        }else {
            toastr.error('Error desconocido');
        }
    }

     // filtros
    function abrirModalFiltro(){
        $('#modalFiltro').modal('show'); 
    }

    function abrirModalFiltro2(){
        $('#modalFiltro2').modal('show'); 
    }

    function filtrar(){
        var idservicio = document.getElementById('selectservicio-filtro').value;
        var tipo = document.getElementById('selecttipo-filtro').value;
                 
        window.open("{{ URL::to('admin/registropromo/reporte') }}/" + idservicio + "/" + tipo);

    }

    // registro de vencimiento por fecha
    function filtrar2(){
        var fecha = document.getElementById('fecha').value;
        
        
        window.open("{{ URL::to('admin/registropromo/reporte2') }}/" + fecha);
    }
 
   </script>
 
 
 
 @stop