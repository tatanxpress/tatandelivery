@extends('backend.menus.superior') 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/estiloToggle.css') }}" type="text/css" rel="stylesheet" /> 
@stop

<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
          <h1>Lista Tipo Servicios Filtrado</h1>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">  
            <div class="card-header">
                <h3 class="card-title">Tabla filtrada</h3>
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
 
<script src="{{ asset('js/backend/jquery-ui-drag.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/backend/datatables-drag.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>


<script type="text/javascript">	 
    $(document).ready(function(){
        idzona = {{ $idzona }};

      var ruta = "{{ url('/admin/tiposerviciozona/tabla') }}/"+idzona;
      $('#tablaDatatable').load(ruta);
    });
 </script>

@endsection