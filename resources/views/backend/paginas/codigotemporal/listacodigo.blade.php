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
          <h1>Código Temporal</h1>
          </div>
          <div style="margin-top:15px; margin-left:15px">
            <button type="button" onclick="verInformacion()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Activar/Inactivar SMS
            </button>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Código Temporales</h3>
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

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">API TWILIO SMS</h4>
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
                                    <label>Si esta activo, NO UTILIZA API TWILIO</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-activo">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div> 
                            </div> 
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
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

 <script type="text/javascript">
    $(document).ready(function(){
      var ruta = "{{ URL::to('admin/codigotemporal/tabla/codigo') }}";
      $('#tablaDatatable').load(ruta);
    });

 </script>

<script> 

    // ver disponibilidad de envio sms
    /*function verInformacion(){
        spinHandle = loadingOverlay().activate();
        
        axios.post('/admin/activosms/informacion',{
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                   
                    $('#modalEditar').modal('show');
                    if(response.data.dato.activo == 0){
                        $("#toggle-activo").prop("checked", false);
                    }else{
                        $("#toggle-activo").prop("checked", true);
                    }                  
                }else{  
                    toastr.error('Activo SMS no encontrado'); 
                }          
            })
            .catch((error) => {
                console.log(error);
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }*/
 
    // editar tipo activo sms api
   /* function editar(){
            
        var toggleactivo = document.getElementById('toggle-activo').checked;

        var toggle = 0;
        if(toggleactivo){
            toggle = 1;
        }
    
        var spinHandle = loadingOverlay().activate();             
        var formData = new FormData();
        formData.append('toggle', toggle);

        axios.post('/admin/activosms/editar', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);
            respuestaEditar(response);
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle); 
            toastr.error('Error');             
        });        
    }*/

    // respuesta al editar 
    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Actualizado');
            $('#modalEditar').modal('hide');
        } 
        else {
            toastr.error('Error desconocido');
        }
    }

</script>
 
@endsection