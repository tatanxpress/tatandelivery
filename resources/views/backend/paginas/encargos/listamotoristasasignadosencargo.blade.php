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
            <h1>Motoristas para Encargo: {{ $nombre }}</h1>
          </div>     
          <button type="button" onclick="modalNegocio()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva Motorista
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de Motoristas Asignados</h3>
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


<!-- modal borrar -->
<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar Registro</h4>
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
                                    <input type="hidden" id="idborrar">
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

<!-- modal nuevo -->
<div class="modal fade" id="modalNuevo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Asignacion</h4>
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
                                    <label style="color:#191818">Motoristas</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-motorista">
                                                <option value="0" selected>Seleccionar</option>
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
                <button type="button" class="btn btn-primary" onclick="nuevoNegocio()">Asignar</button>
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
        id = {{ $id }};
        
        var ruta = "{{ url('/admin/encargos/asignar/tabla/motorista-encargo') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

<script>


    function modalNegocio(){
        $('#modalNuevo').modal('show');
    }

    function modalBorrar(id){
        $('#idborrar').val(id);
        $('#modalBorrar').modal('show');
    }

    function borrar(id){
        var id = document.getElementById('idborrar').value;

        spinHandle = loadingOverlay().activate();
        axios.post('/admin/encargos/ordenes/motorista-asignando-encargo-borrar',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                respuestaBorrar(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function respuestaBorrar(response){
        if(response.data.success == 1){
            toastr.success('Borrado'); 
            id = {{ $id }}; // id del encargo
            
            var ruta = "{{ url('/admin/encargos/asignar/tabla/motorista-encargo') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalBorrar').modal('hide');     
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }
    
    
    function respuestaEditar(response){
        
        if(response.data.success == 1){

            toastr.success('Guardado');
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/tabla/negocio-categorias') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide'); 
        }
        else{
            toastr.error('Error desconocido');
        }
    }
    
    function nuevoNegocio(){
        var id = {{ $id }}; // id del encargo
        var id2 = document.getElementById('select-motorista').value;

        if(id2 === '' || id2 === '0'){
            toastr.error("id motorista es requerido");
            return;
        }
     
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);
        formData.append('id2', id2); 

        axios.post('/admin/encargos/ordenes/motorista-asignando-encargo', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    respuesta(response);                        
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
    }

    function respuesta(response){
        if(response.data.success == 1){
            toastr.error('Motorista ya asignado'); 
        }else if(response.data.success == 2){
            toastr.success('Guardado'); 
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/asignar/tabla/motorista-encargo') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalNuevo').modal('hide'); 
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    
  </script>
 


@stop