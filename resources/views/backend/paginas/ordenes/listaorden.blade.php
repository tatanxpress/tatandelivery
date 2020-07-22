@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    
@stop  

  <section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
          <h1>Ultimas 100 Ordenes</h1>
        </div>  
    </div> 
  </section> 
     
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de ordenes</h3>
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

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cambiar motorista a la orden</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                            <div class="row">

                            <div class="form-group">                                
                                <input type="hidden" id="id-editar">
                            </div>
                               
                              <div class="form-group">
                                  <label style="color:#191818">Motorista</label>
                                  <br>
                                  <div>
                                      <select class="form-control" id="select-motorista">                                     
                                      </select>
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

    <script src="{{ asset('js/backend/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/backend/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>

 <!-- incluir tabla --> 
  <script type="text/javascript">	 
    $(document).ready(function(){       
        var ruta = "{{ URL::to('admin/ordenes/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    });  

    // buscar asignacion de motorista de esta orden
    function verModal(id){

      document.getElementById("formulario-editar").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/ordenes/buscar-su-motorista',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

              
                if(response.data.success == 1){

                    $('#modalEditar').modal('show');
                    $('#id-editar').val(id);                    

                    var tipo = document.getElementById("select-motorista");
                    // limpiar select
                    document.getElementById("select-motorista").options.length = 0;
                
                    $.each(response.data.motoristas, function( key, val ){  
                       if(response.data.idmoto == val.id){
                            $('#select-motorista').append('<option value="' +val.id +'" selected="selected">'+val.identificador+'</option>');
                       }else{
                            $('#select-motorista').append('<option value="' +val.id +'">'+val.identificador+'</option>');
                       }
                    });

                } else if(response.data.success == 2){
                  toastr.error('Sin motorista');
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

  function editar(){     

      var id = document.getElementById('id-editar').value;
      var motorista = document.getElementById('select-motorista').value;

      var spinHandle = loadingOverlay().activate();
      var formData = new FormData();
      formData.append('id', id);
      formData.append('motorista', motorista);

      axios.post('/admin/ordenes/cambiar-su-motorista', formData, {
      })
      .then((response) => {
          loadingOverlay().cancel(spinHandle);

          if(response.data.success == 1){
              toastr.success('Actualizado');


              var ruta = "{{ URL::to('admin/ordenes/tabla/lista') }}";
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
    
 </script>


 
@stop