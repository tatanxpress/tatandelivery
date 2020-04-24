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
          <h1>Tipo de servicios</h1>
          </div>
          <div style="margin-top:15px;">
            <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Tipo Servicio
            </button> 

            <button type="button" style="margin-left" onclick="abrirModalFiltro()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro para posiciones
            </button>
            
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

<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Tipo Servicio</h4>
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
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div> 
                                        <select class="form-control" id="select-identificador" onchange="buscarServicios(this)">   
                                                <option value="0" selected>Seleccionar</option>                                         
                                            @foreach($identificador as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label style="color:#191818">Servicio</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-servicio">
                                            <option value="0" selected>Vacio</option>
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
                <h4 class="modal-title">Editar Tipo Servicio</h4>
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
                                    <label>Disponibilidad</label><br>
                                    <input type="hidden" id="id-editar">
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


<!-- modal filtro para cambiar posiciones -->
<div class="modal fade" id="modalFiltro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro para cambiar posiciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-filtro">   
                                            @foreach($identificador as $item)                                                
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
                <button type="button" class="btn btn-primary" onclick="filtrar()">Filtrar</button>
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
      var ruta = "{{ URL::to('admin/tiposerviciozona/tablas/lista-tipo-servicio-zona') }}";
      $('#tablaDatatable').load(ruta);
    });

 </script>

<script> 

    // filtros
    function abrirModalFiltro(){
        $('#modalFiltro').modal('show');
    }

    // buscar servicios, segun cambio del select
    function buscarServicios(sel){

        if(sel.value != 0){               
            var spinHandle = loadingOverlay().activate();
            axios.post('/admin/tiposerviciozona/buscar/servicio',{
            'id': sel.value 
            }) 
            .then((response) => {	
                    loadingOverlay().cancel(spinHandle);                    
                    if (response.data.success == 1) {
                        var tipo = document.getElementById("select-servicio");
                        // limpiar select
                        document.getElementById("select-servicio").options.length = 0;
                        if(response.data.tiposervicio.length == 0){                          
                            tipo.options[0] = new Option('Ninguna disponible', 0); 
                        }else{
                            $.each(response.data.tiposervicio, function( key, val ){  
                                tipo.options[key] = new Option(val.nombre, val.id);
                            });
                        }
                    }else{
                        toastr.error('Error de retorno');
                    }
                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    loadingOverlay().cancel(spinHandle);
            });    
        } 
    }

    // modal nuevo tipo servicio zona
    function abrirModalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    // agregar nuevo tipo servicio zona
    function nuevo(){ 

        var identificador = document.getElementById("select-identificador").value; //zona
        var servicio = document.getElementById("select-servicio").value; // servicio
                
        var retorno = validacionNuevo(identificador, servicio);

        if (retorno) { 
             
            let me = this;
            let formData = new FormData();
            formData.append('identificador', identificador);
            formData.append('servicio', servicio);

            var spinHandle = loadingOverlay().activate();

            axios.post('/admin/tiposerviciozona/nuevo', formData, {
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

    function filtrar(){
        var identificador = document.getElementById("select-filtro").value;
        
        window.location.href="{{ url('/admin/tiposerviciozona') }}/"+identificador;
    }
    

    // verificar agregado nuevo
    function verificar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
            toastr.error('Este servicio ya esta agregado');        
        } else if (response.data.success == 2) {
            toastr.success('Tipo Servicio Zona agregado');
            var ruta = "{{ URL::to('admin/tiposerviciozona/tablas/lista-tipo-servicio-zona') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');      
        } else if (response.data.success == 3) {
            toastr.error('Error al crear');
        } else {
            toastr.error('Error desconocido');
        }
    }
 
    // validar nuevo tipo servicio zona
    function validacionNuevo(identificador, servicio){
     
        if (identificador == 0) {
            toastr.error("Seleccionar identificador");
            return false;
        }

        if(servicio == 0){
            toastr.error("Seleccionar servicio");
            return false;
        }

        return true;
    }

    // informacion tipo servicios zona
    function verInformacion(id){
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/tiposerviciozona/informacion',{
        'id': id
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                   
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.tipo.id);
                    if(response.data.tipo.activo == 0){
                        $("#toggle-activo").prop("checked", false);
                    }else{
                        $("#toggle-activo").prop("checked", true);
                    }                  
                }else{  
                    toastr.error('Tipo servicio zona no encontrado'); 
                }          
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
 
    // editar tipo servicio zona
    function editar(){
            
        var id = document.getElementById('id-editar').value;
        var toggleactivo = document.getElementById('toggle-activo').checked;

        var toggle = 0;
        if(toggleactivo){
            toggle = 1;
        }
    
        var spinHandle = loadingOverlay().activate();             
        var formData = new FormData();
        formData.append('id', id);
        formData.append('toggle', toggle);

        axios.post('/admin/tiposerviciozona/editar-tipo', formData, {
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

    // respuesta al editar 
    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Tipo Servicio Zona actualizado');
            var ruta = "{{ URL::to('admin/tiposerviciozona/tablas/lista-tipo-servicio-zona') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');      
        } else if (response.data.success == 2) {
            toastr.error('Tipo servicio zona no encontrado');
        }
        else {
            toastr.error('Error desconocido');
        }
    }

</script>
 
@endsection