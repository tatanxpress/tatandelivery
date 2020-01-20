@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Productos del servicio: {{ $nombre }}</h1>
            <p>Identificador de: {{ $identificador }}</p>
          </div>  
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de productos para agregar</h3>
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

    <!-- modal agregar producto-->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-agregar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                <div class="form-group">
                                  <input type="hidden" id="id-producto">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="nuevo()">Agregar</button>
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
      
      idservicio = {{ $idservicio }}; 
      
      var ruta = "{{ url('/admin/pr/producto/tablas') }}/"+idservicio;
      $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script>

    function modalAgregar(id){
      $('#id-producto').val(id);      
      $('#modalAgregar').modal('show');
    }
 
    function nuevo(){
      var idproducto = document.getElementById('id-producto').value;
      var idpromo = {{ $idpromo }};

      var formData = new FormData();
      formData.append('idproducto', idproducto);
      formData.append('idpromo', idpromo);
      
      var spinHandle = loadingOverlay().activate();

      axios.post('/admin/promo/producto/nuevo', formData, {
        
            })
            .then((response) => {
              loadingOverlay().cancel(spinHandle);

             respuesta(response);
                    
 

            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        }); 
    }

    function respuesta(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.error('Este producto ya esta agregado');           
            
        } else if(response.data.success == 2){
            toastr.success('Producto agregado');
            $('#modalAgregar').modal('hide');
        } 
        else {
            toastr.error('Error desconocido');
        }
    }

  </script>



@stop