@extends('backend.menus.superior')
 
@section('content-admin-css')
<link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Categorias</h1>
          </div>    
          <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva categoria
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
                                  <label>Nombre</label>
                                  <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre categoria">
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

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar categoria</h4>
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
                                  <label>Servicio nombre</label>
                                  <input type="text" disabled class="form-control" id="servicio-editar">
                              </div>
                              
                              <div class="form-group">
                                  <label>Nombre categoria</label>
                                  <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre categoria">
                              </div>

                              <div class="form-group">
                                  <label>Disponibilidad</label>
                                  <input type="checkbox" id="cbactivo-editar">
                              </div>

                              <div class="form-group">
                                  <label>Fecha ingreso</label>
                                  <input type="text" disabled class="form-control" id="fecha-editar">
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

@extends('backend.menus.inferior')

@section('content-admin-js')	

    <script src="{{ asset('js/backend/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/backend/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>

 
 <!-- incluir tabla --> 
  <script type="text/javascript">	 
    $(document).ready(function(){       
      id = {{ $id }};
      $('#id-editar').val(id);
      var ruta = "{{ url('/admin/categorias/tablas') }}/"+id;
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
      var id = {{ $id }};
      var nombre = document.getElementById('nombre-nuevo').value;

      if(nombre === ''){
        toastr.error("nombre es requerido");
        return;
      }
      
      if(nombre.length > 50){
        toastr.error("50 caracter máximo nombre");
        return false;
      }

      var spinHandle = loadingOverlay().activate();
      var formData = new FormData();
      formData.append('id', id);
      formData.append('nombre', nombre);

      axios.post('/admin/categorias/nuevo', formData, {
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
            toastr.success('Categoria agregada');
            
            id = {{ $id }};
            var ruta = "{{ url('/admin/categorias/tablas') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');     
        } else if(response.data.success == 2){
            toastr.error('categoria no agregada');
        }
        else {
            toastr.error('Error desconocido');
        }
    } 
    
    function informacion(id){
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/categorias/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.categoria.id);
                    $('#servicio-editar').val(response.data.categoria.nombreServicio);
                    $('#nombre-editar').val(response.data.categoria.nombre);
                    $('#fecha-editar').val(response.data.categoria.fecha);
                    
                    if(response.data.categoria.activo == 0){
                        $("#cbactivo-editar").prop("checked", false);
                    }else{
                        $("#cbactivo-editar").prop("checked", true);
                    } 

                }else{
                    toastr.error('Categoria no encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
    
    function editar(){
      var id = document.getElementById('id-editar').value;
      var nombre = document.getElementById('nombre-editar').value;
      var toggleactivo = document.getElementById('cbactivo-editar').checked;
      
      if(nombre === ''){
        toastr.error("nombre es requerido");
        return;
      }
      
      if(nombre.length > 50){
        toastr.error("50 caracter máximo nombre");
        return false;
      }

      var toggle = 0;
        if(toggleactivo){
            toggle = 1;
        }

      var spinHandle = loadingOverlay().activate();
      var formData = new FormData();
      formData.append('id', id);
      formData.append('toggle', toggle);
      formData.append('nombre', nombre);

      axios.post('/admin/categorias/editar', formData, {
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
      } else if (response.data.success == 1) {
          toastr.success('Categoria actualizada');

          id = {{ $id }};
          var ruta = "{{ url('/admin/categorias/tablas') }}/"+id;
          $('#tablaDatatable').load(ruta);
          $('#modalEditar').modal('hide');
      } else if (response.data.success == 2) {
          toastr.error('Categoria no encontrada');
      }
      else {
          toastr.error('Error desconocido');
      }
    }

    function producto(id){
        window.location.href="{{ url('/admin/productos/') }}/"+id;
    }

  </script>



@stop