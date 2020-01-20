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
             <h1>Zona publicidad</h1>
           </div>    
           <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                 <i class="fas fa-pencil-alt"></i>
                     Nueva zona publicidad
           </button>  
           <button type="button" style="margin-left" onclick="abrirModalFiltro()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro para posiciones
            </button>  
       </div>
     </section>
     
   <!-- seccion frame --> 
   <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">  
                <div class="card-header">
                    <h3 class="card-title">Tabla de zona publicidad</h3>
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
                 <h4 class="modal-title">Nueva categoria</h4>
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
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="selectzona-filtro" data-live-search="true" required>   
                                            @foreach($zonas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label style="color:#191818">Publicidad activa identificador</label>
                                    <br>
                                    <div> 
                                        <select class="form-control selectpicker" id="selectservicio-filtro" data-live-search="true" required>   
                                                <option value="0">Seleccionar</option>
                                            @foreach($publicidad as $item)                               
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
                 <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
             </div>          
         </div>        
     </div>      
 </div>
  
 <!-- modal borrar -->
 <div class="modal fade" id="modalBorrar">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Borrar publicacion</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form id="formulario-borrar">
                     <div class="card-body">
                         <div class="row">  
                             <div class="col-md-12">
                                <div class="form-group">
                                    <input type="hidden" id="id-editar">
                                </div>
                                <label>Borrar esta publicación</label> 
                             </div>
                         </div>
                     </div>
                 </form>
             </div>
             <div class="modal-footer justify-content-between">
                 <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                 <button type="button" class="btn btn-danger" onclick="borrar()">Borrar</button>
             </div>          
         </div>        
     </div>      
 </div>
 
<!-- modal filtro -->
<div class="modal fade" id="modalFiltro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal filtro para posiciones</h4>
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
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="selectzona-e" data-live-search="true" required>   
                                            @foreach($zonas as $item)                                                
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
                <button type="button" class="btn btn-primary" onclick="filtrar()">Filtrar</button>
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
      
       var ruta = "{{ URL::to('admin/zonapublicidad/tabla/lista') }}";
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
       var idzona = document.getElementById('selectzona-filtro').value;
       var idpubli = document.getElementById('selectservicio-filtro').value;

       if(idpubli === '0'){
         toastr.error("Seleccionar una publicidad");
         return;
       }
 
       var spinHandle = loadingOverlay().activate();
       var formData = new FormData();
       formData.append('idzona', idzona);
       formData.append('idpubli', idpubli);
 
       axios.post('/admin/zonapublicidad/nuevo', formData, {
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
 
     function respuestaNuevo(response){
       if (response.data.success == 0) {
             toastr.error('Validacion incorrecta');
         } else if (response.data.success == 1) {
            toastr.error('Esta publicacion ya esta ingresada');
            
         } else if(response.data.success == 2){
             
             toastr.success('Categoria agregada');
            
             var ruta = "{{ URL::to('admin/zonapublicidad/tabla/lista') }}";
             $('#tablaDatatable').load(ruta);
             $('#modalAgregar').modal('hide');     
         }
         else {
             toastr.error('Error desconocido');
         }
     } 
     
     function modalBorrar(id){
        $('#id-editar').val(id);
        $('#modalBorrar').modal('show');
     }

     function borrar(){
        var id = document.getElementById('id-editar').value;

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
    
        axios.post('/admin/zonapublicidad/borrar', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    if(response.data.success == 1){
                        toastr.success('borrado');
            
                        var ruta = "{{ URL::to('admin/zonapublicidad/tabla/lista') }}";
                        $('#tablaDatatable').load(ruta);
                        $('#modalBorrar').modal('hide');   
                    }else{
                        toastr.error('Error desconocido'); 
                    } 
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
     }
 
     function producto(id){
         window.location.href="{{ url('/admin/productos/') }}/"+id;
     }

     // filtros
    function abrirModalFiltro(){
        $('#modalFiltro').modal('show'); 
    }

    function filtrar(){
        var idzona = document.getElementById('selectzona-e').value;
                 
        window.location.href="{{ url('/admin/zonapublicidad') }}/"+idzona;
    }
 
   </script>
 
 
 
 @stop