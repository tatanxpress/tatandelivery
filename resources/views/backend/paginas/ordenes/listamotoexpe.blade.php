@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop  

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Lista motorista calificaciones (ultimas 100)</h1>
          </div>
          <button type="button" onclick="modal1()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Calificacion Global
          </button>   
      </div>
    </section>  
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de motorista calificacion</h3>
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


  <!-- modal motorista -->
<div class="modal fade" id="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Motorista</h4>
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
                                    <label style="color:#191818">Identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="motorista">   
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
                <button type="button" class="btn btn-primary" onclick="buscar()">Buscar</button>                
            </div>          
        </div>        
    </div>      
</div>

<!-- calificacion -->
<div class="modal fade" id="modalCalificacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Calificacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-calificacion">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Sumatoria de calificaciones</label>
                                    <input type="text" disabled maxlength="50" class="form-control" id="sumatoria">
                                </div>

                                <div class="form-group">
                                    <label>Contador (Total de Completadas)</label>
                                    <input type="text" disabled maxlength="50" class="form-control" id="contador">
                                </div>

                                <div class="form-group">
                                    <label>Calificacion global</label>
                                    <input type="text" disabled maxlength="50" class="form-control" id="calificacion">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

        var ruta = "{{ url('/admin/motoexpe/tabla/lista') }}";
      $('#tablaDatatable').load(ruta);
    }); 
     
 </script>

  <script>

    function modal1(){
      $('#modal').modal('show');
    }

    function buscar(){
      var id = document.getElementById('motorista').value;

      var spinHandle = loadingOverlay().activate();
      var formData = new FormData();
      
      formData.append('id', id);      
      
      axios.post('/admin/moto/calficacion-global', formData, {
      })
      .then((response) => {
          loadingOverlay().cancel(spinHandle);

          if(response.data.success == 1) {
            $('#modal').modal('hide');
            document.getElementById("formulario-calificacion").reset();
            $('#modalCalificacion').modal('show');
            $('#sumatoria').val(response.data.sumatoria);
            $('#contador').val(response.data.contador);
            $('#calificacion').val(response.data.calificacion);
          }else if(response.data.success == 2) {
              toastr.error('Ninguna orden aun');
          }
          else {
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