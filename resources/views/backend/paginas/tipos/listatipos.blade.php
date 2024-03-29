@extends('backend.menus.superior') 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop

<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-3">
          <h1>Tipos</h1>
          </div>
          <div class="col-sm-2">
          <button type="button" onclick="abrirModalAgregar()" class="btn btn-info btn-sm">
          <i class="fas fa-pencil-alt"></i>
            Nuevo Tipo
        </button>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
            <h3 class="card-title">Tipos</h3>
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

<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Tipos</h4>
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
                                    <label>Nombre</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre tipo servicio">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="200" class="form-control" id="descripcion-nuevo" placeholder="Descripción tipo servicio">
                                </div>                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Tipos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre tipo servicio">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="200" class="form-control" id="descripcion-editar" placeholder="Descripción tipo servicio">
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

 <script type="text/javascript">
    $(document).ready(function(){
      var ruta = "{{ URL::to('admin/tipos/tablas/lista-tipos') }}";
      $('#tablaDatatable').load(ruta);
    });
 </script>

<script>

    // modal nuevo tipo servicio
    function abrirModalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    // agregar nuevo tipos
    function nuevo() {
        var nombre = document.getElementById('nombre-nuevo').value;
        var descripcion = document.getElementById('descripcion-nuevo').value;

        var retorno = validacion_nuevo(nombre);

        if (retorno) {
            
            let me = this;
            let formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);            

            var spinHandle = loadingOverlay().activate();

            axios.post('/admin/tipos/nuevo', formData, {
            })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);                    
                    verificar(response);
                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    loadingOverlay().cancel(spinHandle);
            });
        }
    }

    // verificar agregado nuevo
    function verificar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Tipos agregado');
            var ruta = "{{ URL::to('admin/tipos/tablas/lista-tipos') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');      
        } else if (response.data.success == 2) {
            toastr.error('Error al crear');
        } else {
            toastr.error('Error desconocido');
        }
    }

    // validar nuevo tipo servicio
    function validacion_nuevo(nombre){
     
        if (nombre === '') {
            toastr.error("Nombre es requerido");
            return false;
        }

        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

           
        return true;
    }

    // informacion tipo servicios
    function verInformacion(id){

        document.getElementById("formulario-editar").reset();   
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/tipos/informacion',{
        'id': id  
            })
            .then((response) => {	
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.tipo.id);
                    $('#nombre-editar').val(response.data.tipo.nombre);
                    $('#descripcion-editar').val(response.data.tipo.descripcion);
                  
                }else{
                    toastr.error('Tipo servicio no encontrado');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error del servidor');
        });
    }

    // editar tipo servicio
    function editar(){
            
        var id = document.getElementById('id-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;
      
        // validacion
        var retorno = validacion_editar(nombre);
        
        if(retorno){

            var spinHandle = loadingOverlay().activate();             
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);

            axios.post('/admin/tipos/editar-tipos', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                respuestaEditar(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error');             
            });
        }
    }

    // respuesta al editar 
    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Tipos actualizado');
            var ruta = "{{ URL::to('admin/tipos/tablas/lista-tipos') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');      
        } else if (response.data.success == 2) {
            toastr.error('Error al actualizar');
        } else if(response.data.success == 3){
            toastr.error('Tipos no encontrado');
        } 
        else {
            toastr.error('Error desconocido');
        }
    }

    // validacion al editar
    function validacion_editar(nombre){
     
        if (nombre === '') {
            toastr.error("Nombre es requerido");
            return false;
        }

        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }
      
        return true;
    }

</script>
 
@endsection