@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    
@stop  

    <section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Ordenes Hoy {{ $fecha }}</h1>
            <label id="total-hoy">Total Hoy $</label>
          </div>  

          <button type="button" onclick="recargar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                  Recargar
          </button>  

          <div class="form-group" style="width: 25%">
              <label>Cronometro</label>
              <label id="contador"></label>
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


  <!-- modal iniciar orden del cliente -->
<div class="modal fade" id="modalIniciar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Contestar orden</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-iniciar">
                    <div class="card-body">
                        <div class="row">  
                          
                            <div class="form-group">
                                <input type="hidden" id="id-editar">
                            </div>
                         
                        </div>
                    </div>  
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardar" onclick="iniciar()">Iniciar</button>
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
        var ruta = "{{ URL::to('admin/control/tabla/ordeneshoy') }}";
        $('#tablaDatatable').load(ruta);

        var total = {{$total}}; 
      
        document.getElementById('total-hoy').innerHTML = 'Total Hoy $'+total;

      countdown();
    });  
    
 </script>

 <script>

  function recargar(){
    var ruta = "{{ url('/admin/control/tabla/ordeneshoy') }}";
    $('#tablaDatatable').load(ruta);  

    // traer total de dinero de ordenes completadas
    axios.post('/admin/control/total/de/ventas-hoy', {
      'id':0
      })
      .then((response) => {
          if(response.data.success == 1){

            document.getElementById('total-hoy').innerHTML = 'Total Hoy $'+response.data.total;
          }
          else{
              toastr.error('Error al obtener Total $');
          }
      })
      .catch((error) => {
          toastr.error('Error');
      });
  }

  function countdown() {
    var seconds = 60;
    function tick() {
        var counter = document.getElementById("contador");
        seconds--;
        counter.innerHTML = "0:" + (seconds < 10 ? "0" : "") + String(seconds);
        if( seconds > 0 ) {
            setTimeout(tick, 1000);
        } else {
            recargar();
            countdown();
        }
    } 
    tick();
  }


  // iniciar una orden que el cliente no contesta
  function modalIniciar(id){
        $('#modalIniciar').modal('show');
        $('#id-editar').val(id);
  }

  function iniciar(){
    var id = document.getElementById('id-editar').value; // id de orden
    
    spinHandle = loadingOverlay().activate();

    var formData = new FormData();
    formData.append('ordenid', id);
                        
    axios.post('/api/usuario/proceso/orden/estado-3',formData,{
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);
            toastr.success('Realizado'); 
            $('#modalIniciar').modal('hide');
           
            recargar();
    })
    .catch((error) => {
        loadingOverlay().cancel(spinHandle); 
        toastr.error('Error del servidor');    
    });  

  }

  // vista de productos
  function verProductos(id){

    window.open("{{ URL::to('admin/productos/orden/nueva') }}/" + id);

  }

 </script>

 
@stop