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
            <h1>Nuevo Negocio</h1>
          </div>     
          <button type="button" onclick="modalNegocio()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Negocio
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de Negocios</h3>
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
                <h4 class="modal-title">Nuevo Negocio</h4>
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
                                    <label>Identificador unico</label>
                                    <input type="text" maxlength="100" class="form-control" id="identificador-nuevo" placeholder="Identificador unico" required>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre" required>
                                </div>

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="200" class="form-control" id="descripcion-nuevo" placeholder="Descripción">
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
                                    <input type="hidden" id="idnegocio-editar">
                                </div>

                                <div class="form-group">
                                    <label>Identificador unico</label>
                                    <input type="text" maxlength="100" class="form-control" id="identificador-editar" placeholder="Identificador unico" required>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre" required>
                                </div>

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="200" class="form-control" id="descripcion-editar" placeholder="Descripcion">
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
        var ruta = "{{ URL::to('admin/encargos/tabla/lista-negocios') }}";
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
       
        axios.post('/admin/encargos/negocios-informacion',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                if(response.data.success == 1){

                    $('#modalEditar').modal('show');
                    $('#idnegocio-editar').val(response.data.negocio.id);
                    $('#identificador-editar').val(response.data.negocio.identificador);
                    $('#nombre-editar').val(response.data.negocio.nombre);                    
                    $('#descripcion-editar').val(response.data.negocio.descripcion);

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

        var id = document.getElementById('idnegocio-editar').value;
        var identificador = document.getElementById('identificador-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;
     
        var retorno = validarEditar(identificador, nombre, descripcion);

        if(retorno){

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('identificador', identificador);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            
            axios.post('/admin/encargos/editar-negocios', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

                if(response.data.success == 1){
                    toastr.error('Identificador ya existe');
                }else if(response.data.success == 2){

                    toastr.success('Guardado');
                    var ruta = "{{ url('/admin/encargos/tabla/lista-negocios') }}";
                    $('#tablaDatatable').load(ruta);
                    $('#modalEditar').modal('hide'); 
                }
                else{
                    toastr.error('Error desconocido');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
        }       
    }

    function validarEditar(identificador, nombre, descripcion){

        if(identificador === ''){
            toastr.error("identificador es requerido");
            return;
        }
        
        if(identificador.length > 100){
            toastr.error("100 caracter máximo identificador");
            return false;
        }

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }
        
        if(descripcion === ''){
            // no hacer nada
        }else{
            if(descripcion.length > 200){
                toastr.error("200 caracter máximo descripcion");
                return false;
            }
        }
        
       
        return true;
    }
    
    function nuevoNegocio(){
        var identificador = document.getElementById('identificador-nuevo').value;
        var nombre = document.getElementById('nombre-nuevo').value;
        var descripcion = document.getElementById('descripcion-nuevo').value;
     
        var retorno = validarNuevo(identificador, nombre, descripcion);
        
        if(retorno){
            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();

            formData.append('identificador', identificador);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
          
            axios.post('/admin/encargos/nuevo-negocio', formData, { 
                    })
                    .then((response) => {
                        loadingOverlay().cancel(spinHandle);
                       
                        if(response.data.success == 1){
                            toastr.error('Identificador ya existe');
                        }else if(response.data.success == 2){
                            toastr.success('Guardado'); 

                            var ruta = "{{ url('/admin/encargos/tabla/lista-negocios') }}";
                            $('#tablaDatatable').load(ruta);
                            $('#modalNuevo').modal('hide'); 
                        }
                        else{
                            toastr.error('Error al guardar'); 
                        }

                    })
                    .catch((error) => {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error('Error');
                    });
        }

    }

    function validarNuevo(identificador, nombre, descripcion){

        if(identificador === ''){
            toastr.error("identificador es requerido");
            return;
        }
        
        if(identificador.length > 100){
            toastr.error("100 caracter máximo identificador");
            return false;
        }

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }
        
        if(descripcion === ''){
            // no hacer nada
        }else{
            if(descripcion.length > 200){
                toastr.error("200 caracter máximo descripcion");
                return false;
            }
        }
     

        return true;
    }

    function categorias(id){
        window.location.href="{{ url('/admin/encargos/negocios/categorias') }}/"+id;
    }
  </script>
 


@stop