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



 </script>

 
@stop