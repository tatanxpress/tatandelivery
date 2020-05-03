@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Lista de Instituciones</h1>
          </div>             
          <button type="button" onclick="modalInstitucion()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva institución
          </button> 
      </div>
    </section>
    
  <!-- seccion frame --> 
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de instituciones</h3>
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



<!-- modal crear institucion -->
<div class="modal fade" id="modalNuevo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crear Institucion</h4>
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
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre institucion">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevaInstitucion()">Guardar</button>                
            </div>          
        </div>        
    </div>      
</div>
 

<!-- modal editar  -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Institucion</h4>
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
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-institucion">
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre institucion">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarInformacion()">Guardar</button>                
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

 <!-- incluir tabla --> 
  <script type="text/javascript">	 
    $(document).ready(function(){
        var ruta = "{{ URL::to('admin/cupones/tabla/instituciones') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script> 

  <script>  

    // modal
    function modalInstitucion(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalNuevo').modal('show');
    } 

    // nueva institucion 
    function nuevaInstitucion(){
        var nombre = document.getElementById('nombre-nuevo').value;
    
        // validaciones
                      
        if(nombre === ''){
            toastr.error('Nombre es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('nombre', nombre);
             
        
        axios.post('/admin/cupones/nuevo/institucion ', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1) {
                toastr.success('Agregado'); 
                var ruta = "{{ URL::to('admin/cupones/tabla/instituciones') }}";
                $('#tablaDatatable').load(ruta);         
                $('#modalNuevo').modal('hide');
            
            }else {
                toastr.error('Error desconocido');
            }
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }    

    // informacion
    function informacion(id){
        document.getElementById("formulario-editar").reset();

        spinHandle = loadingOverlay().activate();
        axios.post('/admin/cupones/info/institucion',{
        'id': id 
            })
            .then((response) => {
                
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-institucion').val(response.data.info.id);
                    $('#nombre-editar').val(response.data.info.nombre);
                }else{
                    toastr.error("ID no encontrado");
                }
            }) 
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });        
    }
   

    // editar informacion de un cupon
    function editarInformacion(){
        var id = document.getElementById('id-institucion').value;
        var nombre = document.getElementById('nombre-editar').value;
        
        // validaciones
                      
        if(nombre === ''){
            toastr.error('Nombre es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('id', id);
        formData.append('nombre', nombre);   
                
        axios.post('/admin/cupones/editar/institucion', formData, {
        })
        .then((response) => {
            
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1) {
                toastr.success('Actualizado'); 
                var ruta = "{{ URL::to('admin/cupones/tabla/instituciones') }}";
                $('#tablaDatatable').load(ruta);  
                $('#modalEditar').modal('hide');            
            }else {
                toastr.error('Error desconocido');
            }
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }

  </script>
 


@stop