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
            <h1>Categorias para: {{ $nombre }}</h1>
          </div>     
          <button type="button" onclick="modalNegocio()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Categoria
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de Categorias</h3>
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
<div class="modal fade" id="modalNuevo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Categoria</h4>
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
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre" required>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevoNegocio()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar encargo -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Encargo</h4>
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
                                    <input type="hidden" id="id-editar">
                                </div>
                             
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre" required>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarNegocio()">Guardar</button>
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
        
        var ruta = "{{ url('/admin/encargos/tabla/negocio-categorias') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

<script>

$(function() {
  $('.selectpicker').selectpicker();
});

    function modalNegocio(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalNuevo').modal('show');
    }

    function informacion(id){
        document.getElementById("formulario-editar").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/negocios/categorias-informacion',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                if(response.data.success == 1){

                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.categoria.id);
                    $('#nombre-editar').val(response.data.categoria.nombre);                    
                }
                else{
                    toastr.error('Error de validacion'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function editarNegocio(){

        var id = document.getElementById('id-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
     
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('nombre', nombre);
        
        axios.post('/admin/encargos/negocios/categorias-editar', formData, {
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
        var id = {{ $id }};
        var nombre = document.getElementById('nombre-nuevo').value;

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }
     
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);
        formData.append('nombre', nombre); 

        axios.post('/admin/encargos/negocios/nueva-categoria', formData, { 
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
            toastr.success('Guardado'); 
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/tabla/negocio-categorias') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalNuevo').modal('hide'); 
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    function productos(id){
        window.location.href="{{ url('/admin/encargos/negocios/categorias-productos') }}/"+id;
    }


  </script>
 


@stop