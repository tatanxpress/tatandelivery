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
          <div class="col-sm-3">
          <h1>Clientes</h1>
          </div>
          <div class="col-sm-2">
        
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
            <h3 class="card-title">Clientes Todos</h3>
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
    </div>
</section>


<!-- modal informacion -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Información usuario</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">        
            <div class="form-group">
                <label style="color:#191818">Código dispositivo</label>
                <br>
                <input id="id-editar" type="hidden">
                <input id="codigo" disabled class="form-control">
            </div>
            <div class="form-group" style="margin-left:20px">
                <label>Disponibilidad</label><br>
                <label class="switch" style="margin-top:10px">
                    <input type="checkbox" id="toggle-activo">
                    <div class="slider round">
                        <span class="on">Activar</span>
                        <span class="off">Desactivar</span>
                    </div>
                </label>
            </div> 
        </div>
          
        <div class="modal-footer float-right">
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
      var ruta = "{{ URL::to('admin/cliente/tablas/cliente-todos') }}";
      $('#tablaDatatable').load(ruta);
    });
 </script>

<script> 

    // modal nuevo tipo servicio
    function abrirModalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    function verDirecciones(id){
      window.location.href="{{ url('/admin/cliente/direcciones') }}/"+id;
    }
 
    // informacion del cliente
    function informacion(id){

        $('#id-editar').val("");
        $('#codigo').val("");

        spinHandle = loadingOverlay().activate();
        
        axios.post('/admin/cliente/informacion',{
        'id': id  
            }) 
            .then((response) => {	
                loadingOverlay().cancel(spinHandle);
               if(response.data.success == 1){
                
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.cliente.id);
                    $('#codigo').val(response.data.cliente.device_id);
                
                    if(response.data.cliente.activo == 0){
                        $("#toggle-activo").prop("checked", false);
                    }else{
                        $("#toggle-activo").prop("checked", true);
                    }                   
                }else{ 
                    toastr.error('Usuario no encontrado'); 
                }         
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    // editar cliente
    function editar(){
        var id = document.getElementById('id-editar').value;
        var toggleactivo = document.getElementById('toggle-activo').checked;

        var toggle = 0;
        if(toggleactivo){
            toggle = 1;
        }

        let me = this;
        let formData = new FormData();
        formData.append('id', id);
        formData.append('toggle', toggle);
       
        var spinHandle = loadingOverlay().activate();
 
        axios.post('/admin/cliente/editar', formData, {
        })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);                    
                verificarEditado(response);        
            })
            .catch((error) => {
                toastr.error('Error del servidor');
                loadingOverlay().cancel(spinHandle);
        });
    }

    // verificar editado
    function verificarEditado(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Cliente actualizado');
            var ruta = "{{ URL::to('admin/cliente/tablas/cliente-todos') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');
        } else if (response.data.success == 2) {
            toastr.error('Error al actualizar');
        } else {
            toastr.error('Error desconocido');
        }
    }

 
 

</script>
 
@endsection