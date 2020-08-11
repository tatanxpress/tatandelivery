@extends('backend.menus.superior')
 
@section('content-admin-css')
<link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Video para Producto: {{ $nombre }}</h1>
          </div>    
          <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva Video
          </button>  

          <button type="button" style="margin-left:30px" onclick="modalInformacion()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Información
          </button>  

           <button type="button" style="margin-left:30px" onclick="modalBorrar()" class="btn btn-danger btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Borrar Video
          </button>      


      </div> 
    </section>
    
  <!-- seccion frame -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">  
                <div class="card-header">
                    <h3 class="card-title">Tabla de Video Unico</h3>
                </div>
                
                <video autoplay="true" loop="true" controls>
                    <source src="{{ url('storage/productos/'.$video) }}">
                </video>


            </div>
        </div>
	</section>

<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Video</h4>
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
                                        <label>Video</label>
                                        <p>Formato MP4</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="video-nuevo" accept="video/mp4"/>
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
                <h4 class="modal-title">Borrar Video</h4>
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
                                            Borrar Video
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
                                    <label>Utiliza Video</label>
                                    <br>
                                    <input type="checkbox" id="cbvideo">
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

    <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>
    
 <!-- incluir tabla --> 
 

  <script>
     
    // modal nuevo
    function abrirModalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    function nuevo(){
    
        // id producto
        id = {{ $id }};      
        var video = document.getElementById('video-nuevo');

        if(video.files && video.files[0]){
            if (!video.files[0].type.match('video/mp4')){      
                toastr.error('Formato de Video: .mp4');
                return false;       
            } 
        }else{
            toastr.error('Video es requerida');
            return;
        }
      
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id); 
        formData.append('video', video.files[0]);            

        axios.post('/admin/productos/agregar/video', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    $('#modalAgregar').modal('hide');
                    respuestaNuevo(response);
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });            
    }

    function respuestaNuevo(response){

        if(response.data.success == 1){
            toastr.error('Nombre de video ya existe');
        } else if(response.data.success == 2){
            toastr.success('Agregado');
            location.reload();
        } else if(response.data.success == 3){
            toastr.error('error al guardar');
        } else{
            toastr.error('Error desconocido');
        }
    } 

   function modalBorrar(){
    document.getElementById("formulario-borrar").reset();
    $('#modalBorrar').modal('show');
   }

   function borrar(){

    var id = {{ $id }}; 

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();

    formData.append('id', id); 

    axios.post('/admin/productos/video/borrar', formData, {
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
            $('#modalBorrar').modal('hide');
            location.reload();
        }else{
            toastr.error('Error desconocido');
        }
    } 


    function modalInformacion(){

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

                    if(response.data.producto.utiliza_video == 0){
                        $("#cbvideo").prop("checked", false);
                    }else{
                        $("#cbvideo").prop("checked", true);
                    }

                }else{
                    toastr.error('Producto no encontrado'); 
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
        var check = document.getElementById('cbvideo').checked;
        var check_1 = 0;
        if(check){
            check_1 = 1;
        }

        formData.append('id', id); 
        formData.append('check', check_1); 

        axios.post('/admin/productos/editar/video', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    $('#modalEditar').modal('hide');
                    
                    if(response.data.success == 1){
                        toastr.error('No hay URL'); 
                    }else if(response.data.success == 2){
                        toastr.success('Actualizado'); 
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