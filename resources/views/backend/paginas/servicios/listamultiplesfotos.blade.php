@extends('backend.menus.superior')
 
@section('content-admin-css')
<link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Fotos para Producto: {{ $nombre }}</h1>
          </div>    
          <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva Imagen
          </button>    

          <button type="button" style="margin-left=30px;" onclick="informacion()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Información
          </button>   
      </div> 
    </section>
    
  <!-- seccion frame -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">  
                <div class="card-header">
                    <h3 class="card-title">Tabla de Imagenes</h3>
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
                <h4 class="modal-title">Nueva Imagen</h4>
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
                                    <div>
                                        <label>Imagen</label>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
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

<!-- modal para borrar -->
<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar Imagen Extra</h4>
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
                                    <input type="hidden" id="id-borrar">
                                    <button type="button" onclick="borrar()" class="btn btn-danger">
                                            <i class="fas fa-pencil-alt"></i>
                                            Borrar Imagen Extra
                                    </button>    
                                 
                                </div>                    
                            </div>
                        </div>
                    </div>
                </form>
            </div> 
            
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>               
                   
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
                                    <label>Utiliza imagenes Extra</label>
                                    <br>
                                    <input type="checkbox" id="cbimagen">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEditada()">Actualizar</button>
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
      var ruta = "{{ url('/admin/productos/tabla/mas/fotografias') }}/"+id;
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
    
        // id producto
        id = {{ $id }};      
        var imagen = document.getElementById('imagen-nuevo');

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error('Imagen es requerida');
            return;
        }
      
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id); 
        formData.append('imagen', imagen.files[0]);            

        axios.post('/admin/productos/agregar/imagen-extra', formData, {
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

        if(response.data.success == 1){
            toastr.error('imagen no valida');
        } else if(response.data.success == 2){
            toastr.success('Actualizado');
            var id = {{ $id }};
            var ruta = "{{ url('/admin/productos/tabla/mas/fotografias') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');
        } else if(response.data.success == 3){
            toastr.error('error al guardar');
        } else if(response.data.success == 4){
            toastr.error('error al guardar imagen');
        } else{
            toastr.error('Error desconocido');
        }
    } 

   function modalBorrar(id){
    document.getElementById("formulario-borrar").reset();
    $('#modalBorrar').modal('show');
    $('#id-borrar').val(id);
   }

   function borrar(){

    var id = document.getElementById('id-borrar').value;

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();

    formData.append('id', id); 

    axios.post('/admin/productos/imagenes/extra-borrar', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                respuestaBorrar(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
   }

   function respuestaBorrar(response){

        if(response.data.success == 1){
            toastr.success('Eliminado');
            var id = {{ $id }};
            var ruta = "{{ url('/admin/productos/tabla/mas/fotografias') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalBorrar').modal('hide');
        }else{
            toastr.error('Error desconocido');
        }
    } 

    function informacion(){

        id = {{ $id }};

        document.getElementById("formulario-editar").reset();
  
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/productos/informacion',{
        'id': id
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

                if(response.data.success == 1){
                    $('#modalEditar').modal('show');

                    if(response.data.producto.utiliza_imagen_extra == 0){
                        $("#cbimagen").prop("checked", false);
                    }else{
                        $("#cbimagen").prop("checked", true);
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

    function guardarEditada(){
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        id = {{ $id }};
        var check = document.getElementById('cbimagen').checked;
        var check_1 = 0;
        if(check){
            check_1 = 1;
        }

        formData.append('id', id); 
        formData.append('check', check_1); 

        axios.post('/admin/productos/editar/imagen-entra', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    $('#modalEditar').modal('hide');
                    if(response.data.success == 1){
                        toastr.success('Actualizado'); 
                    }else if(response.data.success == 2){
                        toastr.error('No hay imagenes Extra'); 
                    }else{
                        toastr.error('Error'); 
                    }
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });  
    }


  </script>



@stop