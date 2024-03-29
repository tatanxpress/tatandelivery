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
            <h1>Motoristas Asignados</h1>
          </div>    
          <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Asignación
          </button>   

           <button type="button" onclick="vistaBorrar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Borrar Todo
          </button>  

           <button type="button" onclick="vistaGlobal()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Asignación Global
          </button>    
      </div> 
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de motoristas asignados</h3>
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



<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Motorista</h4>
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
                                    <label style="color:#191818">Servicio identificador</label>
                                    <br>
                                    <div>  

                                        <select class="form-control selectpicker" id="servicio" data-live-search="true" required>   
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label style="color:#191818">Motoristas identificador</label>
                                    <br>
                                    <div>
                                    <select class="form-control selectpicker" id="motorista" data-live-search="true" required>   
                                            @foreach($motoristas as $item)                                                
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

<!-- modal nuevo -->
<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar asignacion</h4>
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
 
<!-- borrar todo los registros -->
<div class="modal fade" id="modalVista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar todas las asignaciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-vista">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="borrarTodo()">Borrar</button>
            </div>          
        </div>        
    </div>      
</div>


<div class="modal fade" id="modalGlobal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Asignación Global</h4>
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
                                    <label style="color:#191818">Motoristas identificador</label>
                                    <br>
                                    <div>
                                    <select class="form-control" id="moto" required>   
                                            @foreach($motoristas as $item)                                                
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
                <button type="button" class="btn btn-primary" onclick="nuevoGlobal()">Guardar</button>
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
        var ruta = "{{ URL::to('admin/motoristasservicio/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script>

    function modalBorrar(id){
        $('#id-editar').val(id);
        $('#modalBorrar').modal('show');
    }

    function modalAgregar(){
        $('#modalAgregar').modal('show');
    }

    function vistaBorrar(){
        $('#modalVista').modal('show');
    }

    function vistaGlobal(){
        $('#modalGlobal').modal('show');
    }
  
    function nuevo(){
        var motorista = document.getElementById('motorista').value;
        var servicio = document.getElementById('servicio').value;       

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('motorista', motorista);
        formData.append('servicio', servicio);
       
        axios.post('/admin/motoristasservicio/nuevo', formData, { 
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
            toastr.error('Esta asignacion ya existe'); 
        } else if(response.data.success == 2){
            toastr.success('Agregado');           
           
            var ruta = "{{ url('/admin/motoristasservicio/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');  
            
        } else if(response.data.success == 3){
            toastr.error('Error al agregar');
        } 
        else {
            toastr.error('Error desconocido');
        }
    } 


    function nuevoGlobal(){
        var motorista = document.getElementById('moto').value;

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('motorista', motorista);
       
        axios.post('/admin/motoristasservicio/nuevo-global', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    respuestaGlobal(response);
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
    }

 
    function respuestaGlobal(response){

        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Completado');
            var ruta = "{{ url('/admin/motoristasservicio/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalGlobal').modal('hide');  
        } 
        else {
            toastr.error('Error desconocido');
        }
    }
  
    function borrar(){
        var id = document.getElementById('id-editar').value;
        var spinHandle = loadingOverlay().activate();
        axios.post('/admin/motoristasservicio/borrar',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    toastr.success('Borrado');
           
                    var ruta = "{{ url('/admin/motoristasservicio/tabla/lista') }}";
                    $('#tablaDatatable').load(ruta);
                    $('#modalBorrar').modal('hide');  

                }else{
                    toastr.error("ID no encontrado");
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
  
   // function borrar todo
   function borrarTodo(){

    var spinHandle = loadingOverlay().activate();
    axios.post('/admin/motoristasservicio/borrartodo',{
        'id': 0 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    toastr.success('Borrado Todo');
           
                    var ruta = "{{ url('/admin/motoristasservicio/tabla/lista') }}";
                    $('#tablaDatatable').load(ruta);
                    $('#modalVista').modal('hide');
 
                }else{
                    toastr.error("ID no encontrado");
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
   }
  

  </script>
 


@stop