@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Promocion producto de {{ $nombre }}</h1>
            <p>Identificador servicio: {{ $identificador }}</p>
          </div>   
          <button type="button" onclick="verProducto()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
              Agregar productos
          </button>
          
          <button type="button" onclick="recargar()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
              Recargar
          </button>  
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de productos agregados</h3>
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


  <!-- modal borrar-->
<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar producto</h4>
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
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="borrar()">Borrar</button>
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
      idpro = {{ $idpro }}; 
      
      var ruta = "{{ url('/admin/promo/tablas') }}/"+idpro;
      $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script>

    function modalBorrar(id){
      $('#id-borrar').val(id);      
      $('#modalBorrar').modal('show');
    }
 
    // borrar producto promocion
    function borrar(){

      var id = document.getElementById('id-borrar').value;
      var spinHandle = loadingOverlay().activate();

      axios.post('/admin/productopromocion/borrar',{
        'id': id
            })
            .then((response) => {
              loadingOverlay().cancel(spinHandle);


                if(response.data.success == 1){

                  toastr.success('Producto borrado');                
                  $('#modalBorrar').modal('hide'); 
                  recargar();

                }else{  
                    toastr.error('Tipo servicio zona no encontrado'); 
                }       
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });   
    }

    function verProducto(){
      idservicio = {{ $idservicio }};   
      idpromo = {{ $idpro }}; 

      window.location.href="{{ url('/admin/promo/producto') }}/"+idservicio+"/"+idpromo; 
    }

    function recargar(){
      idpro = {{ $idpro }}; 
      
      var ruta = "{{ url('/admin/promo/tablas') }}/"+idpro;
      $('#tablaDatatable').load(ruta);
    }

  </script>



@stop