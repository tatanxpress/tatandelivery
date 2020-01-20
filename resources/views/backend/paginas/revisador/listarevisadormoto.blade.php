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
            <h1>Revisadores para motorista</h1>
          </div>    
          <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Revisador para motorista
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de revisadores motorista</h3>
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


<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar revisador a motorista</h4>
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
                                    <label style="color:#191818">Revisador identificador</label>
                                    <br>
                                    <div>
                                        <select id="revisador-select" class="form-control selectpicker" data-live-search="true" required>   
                                            @foreach($revisador as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label style="color:#191818">Motoristas identificador</label>
                                    <br>
                                    <div>
                                        <select id="motorista-select" class="form-control selectpicker" data-live-search="true" required>   
                                            @foreach($motorista as $item) 
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
                <h4 class="modal-title">Borrar registro</h4>
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
        var ruta = "{{ URL::to('admin/revisadormoto/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script> 

    function modalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    } 

    function modalBorrar(id){ 
        $('#id-editar').val(id);
        $('#modalBorrar').modal('show');
    }

    function nuevo(){
        var revisador = document.getElementById('revisador-select').value;
        var motorista = document.getElementById('motorista-select').value;
       
            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('revisador', revisador);
            formData.append('motorista', motorista);
       
            axios.post('/admin/revisadormoto/nuevo', formData, { 
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
            toastr.error('Este revisador ya agregado'); 
        } else if(response.data.success == 2){
            toastr.success('Agregado');           
           
            var ruta = "{{ url('/admin/revisadormoto/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');  
            
        } else if(response.data.success == 3){
            toastr.error('Error al agregar');
        } 
        else {
            toastr.error('Error desconocido');
        }
    } 

 
    function borrar(){
        var id = document.getElementById('id-editar').value;

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
      

        axios.post('/admin/revisadormoto/borrar', formData, { 
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

    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
            toastr.success('Borrado');
           
            var ruta = "{{ url('/admin/revisadormoto/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalBorrar').modal('hide');
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

 

  </script>
 


@stop