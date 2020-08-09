@extends('backend.menus.superior') 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    
@stop

<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
          <h1>Tipo de Servicios para cambio de posición para todas las zonas</h1>
          </div>
          <div style="margin-top:15px; margin-left:25px">
          <label>Si esta activo, se ordenaran posiciones</label>
              <input style="margin-left:25px" type="checkbox" id="check-posicion">
          </div>
        </div> 
      </div>
</section>


<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Tipo Servicios</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="tablaDatatable">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@extends('backend.menus.inferior')
@section('content-admin-js')	

<script src="{{ asset('js/backend/jquery-ui-drag.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/backend/datatables-drag.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>

 <script type="text/javascript">
    $(document).ready(function(){
      var ruta = "{{ URL::to('admin/tiposerviciozona/tablas/tablatiposervicioglobal') }}";
      $('#tablaDatatable').load(ruta);
    });

 </script>

<script> 

function ordenarPosiciones(order){
    var check = document.getElementById('check-posicion').checked;

    if(check){
        var spinHandle = loadingOverlay().activate();

        let formData = new FormData();
        formData.append('[order]', order);

        axios.post('/admin/tiposerviciozona/ordenar-globalmente',{ 
            'order': order,
            })
            .then((response) => {
            loadingOverlay().cancel(spinHandle);

                toastr.success('Actualizado');
            })
            .catch((error) => {           
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }
}

</script>
 
@endsection