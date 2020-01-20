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
          <h1>Lista de Zonas</h1>
          </div>
          <div class="col-sm-2">
          <button type="button" onclick="abrirModalAgregar()" class="btn btn-info btn-sm">
          <i class="fas fa-pencil-alt"></i>
            Nueva Zona
        </button>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
            <h3 class="card-title">Zonas</h3>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-actualizar">
                                    <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre zona">
                                </div>
                                <div class="form-group">
                                    <label>Descripción corta</label>
                                    <input type="text" maxlength="200" class="form-control" id="descripcion-nuevo" placeholder="Descripción breve de la zona">
                                </div>
                                <div class="form-group">
                                    <label>Identificador</label>
                                    <input type="text" maxlength="10" class="form-control" id="identificador-nuevo" placeholder="Identificador">
                                </div>
                                <div class="form-group">
                                    <label>Hora abierto</label>
                                    <input type="time" class="form-control" id="horaabierto-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Hora cerrado</label>
                                    <input type="time" class="form-control" id="horacerrado-nuevo">
                                </div>                  
                            </div>
                            <div class="col-md-6">                             
                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="latitud-nuevo" placeholder="Ingresar Latitud">
                                </div>
                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="longitud-nuevo" placeholder="Ingresar Longitud">
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre zona">
                                </div>
                                <div class="form-group">
                                    <label>Descripción corta</label>
                                    <input type="text" maxlength="200" class="form-control" id="descripcion-editar" placeholder="Descripción breve de la zona">
                                </div>
                                <div class="form-group">
                                    <label>Identificador</label>
                                    <input type="text" maxlength="10" class="form-control" id="identificador-editar" placeholder="Identificador">
                                </div>
                                <div class="form-group">
                                    <label>Hora abierto</label>
                                    <input type="time" class="form-control" id="horaabierto-editar">
                                </div>
                                <div class="form-group">
                                    <label>Hora cerrado</label>
                                    <input type="time" class="form-control" id="horacerrado-editar">
                                </div>                  
                            </div>
                            <div class="col-md-6">                             
                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="latitud-editar" placeholder="Ingresar Latitud">
                                </div>
                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="longitud-editar" placeholder="Ingresar Longitud">
                                </div>
                                <div class="form-group" style="margin-left:20px">
                                    <label>Zona Problema de envío</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-problema">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>  
                                <div class="form-group" style="margin-left:20px">
                                    <label>Disponibilidad Zona</label><br>
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
                <button type="button" class="btn btn-primary" id="btnGuardar" onclick="editar()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal poligono -->
<div class="modal fade" id="modalPoligono">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo poligono</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-poligono">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Borrar poligonos</label>
                                    <br>
                                    <button type="button" onclick="modalBorrar()" class="btn btn-danger btn-sm">                                       
                                            Borrar
                                    </button>                                    
                                </div>
                                <br>
                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="hidden" id="id-poligono">
                                    <input type="text" maxlength="50" class="form-control" id="latitud-poligono" placeholder="Latitud">
                                </div>
                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="longitud-poligono" placeholder="Longitud">
                                </div>         
                            </div>                         
                        </div>
                    </div>  
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="poligonos()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal borrar poligono -->
<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar Poligono</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>             
                    <button class="btn btn-danger" id="btnBorrar" type="button" onclick="borrarPoligono()">Borrar</button>
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
      var ruta = "{{ URL::to('admin/zona/tablas/zona') }}";
      $('#tablaDatatable').load(ruta);
    });
 </script>

 <script>

    function abrirModalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    // informacion zona
    function verInformacion(id){

        document.getElementById("formulario-editar").reset();   
        spinHandle = loadingOverlay().activate();
        
        axios.post('/admin/zona/informacion-zona',{
        'id': id  
            }) 
            .then((response) => {	
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.zona.id);
                    $('#nombre-editar').val(response.data.zona.nombre);
                    $('#identificador-editar').val(response.data.zona.identificador);
                    $('#descripcion-editar').val(response.data.zona.descripcion);
                    $('#horaabierto-editar').val(response.data.zona.hora_abierto_delivery);
                    $('#horacerrado-editar').val(response.data.zona.hora_cerrado_delivery);
                    $('#latitud-editar').val(response.data.zona.latitud);
                    $('#longitud-editar').val(response.data.zona.longitud);
                    if(response.data.zona.saturacion == 0){
                        $("#toggle-problema").prop("checked", false);
                    }else{
                        $("#toggle-problema").prop("checked", true);
                    }

                    if(response.data.zona.activo == 0){
                        $("#toggle-activo").prop("checked", false);
                    }else{
                        $("#toggle-activo").prop("checked", true);
                    }
                }else{ 
                    toastr.error('Zona no encontrada'); 
                }          
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    // nueva zona
    function nuevo() {
        var nombre = document.getElementById('nombre-nuevo').value;
        var descripcion = document.getElementById('descripcion-nuevo').value;
        var identificador = document.getElementById('identificador-nuevo').value;
        var horaabierto = document.getElementById('horaabierto-nuevo').value;
        var horacerrado = document.getElementById('horacerrado-nuevo').value;
        var latitud = document.getElementById('latitud-nuevo').value;
        var longitud = document.getElementById('longitud-nuevo').value;
                
        var retorno = validacion_nuevo(nombre, descripcion, horaabierto, horacerrado, latitud, longitud, identificador);

        if (retorno) {
           
            let me = this;
            let formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('horaabierto', horaabierto);
            formData.append('horacerrado', horacerrado);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('identificador', identificador);

            var spinHandle = loadingOverlay().activate();

            axios.post('/admin/zona/nueva-zona', formData, {
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

    // verificar nuevo ingreso
    function verificar(response) {
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Zona Agregada');
            var ruta = "{{ URL::to('admin/zona/tablas/zona') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');   
        } else if (response.data.success == 2) {
            toastr.error('Error al guardar zona');
        } else if(response.data.success == 3){
            toastr.error('El identificador ya existe');
        }
        else {
            toastr.error('Error desconocido');
        }
    }

    // validar nueva zona
    function validacion_nuevo(nombre, descripcion, horaabierto, horacerrado, latitud, longitud, identificador){
     
        if (nombre === '') {
            toastr.error("Nombre es requerido");
            return false;
        }

        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if (descripcion === '') {
            toastr.error("Descripción es requerido");
            return false;
        }

        if(descripcion.length > 200){
            toastr.error("200 caracter máximo descripción");
            return false;
        }
    
        if (horaabierto === '') {
            toastr.error("Horario abierto es requerido");
            return false;
        }

        if (horacerrado === '') {
            toastr.error("Horario cerrado es requerido");
            return false;
        }

        if(latitud === ''){
            toastr.error("Latitud es requerido");
            return false;
        }

        if(latitud.length > 50){
            toastr.error("50 caracter máximo latitud");
            return false;
        }

        if(longitud === ''){
            toastr.error("Longitud es requerida");
            return false;
        }

        if(longitud.length > 50){
            toastr.error("50 caracter máximo longitud");
            return false;
        }

        if(identificador === ''){
            toastr.error("Identificador es requerido");
            return false;
        }

        if(identificador.length > 10){
            toastr.error("10 caracter máximo identificador");
            return false;
        }
     
     return true;
    }

    // editar zona
    function editar() {     
        var id = document.getElementById('id-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;
        var identificador = document.getElementById('identificador-editar').value;
        var horaabierto = document.getElementById('horaabierto-editar').value;
        var horacerrado = document.getElementById('horacerrado-editar').value;
        var latitud = document.getElementById('latitud-editar').value;
        var longitud = document.getElementById('longitud-editar').value;
        var toggleproblema = document.getElementById('toggle-problema').checked;
        var toggleactivo = document.getElementById('toggle-activo').checked;
                
        var retorno = validacion_editar(nombre, descripcion, horaabierto, horacerrado, latitud, longitud, identificador);

        if (retorno) {
            
            var togglep = 0;
            var togglea = 0;
            if(toggleproblema){
                togglep = 1;
            }

            if(toggleactivo){
                togglea = 1;
            }

            let me = this;
            let formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('identificador', identificador);
            formData.append('horaabierto', horaabierto);
            formData.append('horacerrado', horacerrado);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('togglep', togglep);
            formData.append('togglea', togglea);

            var spinHandle = loadingOverlay().activate();

            axios.post('/admin/zona/editar-zona', formData, {
            })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);                    
                    verificar_editar(response);
                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    loadingOverlay().cancel(spinHandle);
            });
        }
    }

    // verificar nuevo ingreso
    function verificar_editar(response) {
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Zona actualizada');
            var ruta = "{{ URL::to('admin/zona/tablas/zona') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');      
        } else if (response.data.success == 2) {
            toastr.error('Error al actualizar la zona');
        } else if(response.data.success == 3){
            toastr.error('El identificador ya existe');
        } else {
            toastr.error('Error desconocido');
        }
    }

    // validacion
    function validacion_editar(nombre, descripcion, horaabierto, horacerrado, latitud, longitud, identificador){
     
        if (nombre === '') {
            toastr.error("Nombre es requerido");
            return false;
        }

        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if (descripcion === '') {
            toastr.error("Descripción es requerido");
            return false;
        }

        if(descripcion.length > 200){
            toastr.error("200 caracter máximo descripción");
            return false;
        }
    
        if (horaabierto === '') {
            toastr.error("Horario abierto es requerido");
            return false;
        }

        if (horacerrado === '') {
            toastr.error("Horario cerrado es requerido");
            return false;
        }

        if(latitud === ''){
            toastr.error("Latitud es requerido");
            return false;
        }

        if(latitud.length > 50){
            toastr.error("50 caracter máximo latitud");
            return false;
        }

        if(longitud === ''){
            toastr.error("Longitud es requerida");
            return false;
        }

        if(longitud.length > 50){
            toastr.error("50 caracter máximo longitud");
            return false;
        }

        if(identificador === ''){
            toastr.error("Identificador es requerida");
            return false;
        }

        if(identificador.length > 10){
            toastr.error("10 caracter máximo identificador");
            return false;
        }
        
        return true;
    }

    // modal agregar poligonos
    function agregarPoligonos(id){
        document.getElementById("formulario-poligono").reset();
        $('#id-poligono').val(id);
        $('#modalPoligono').modal('show');
    }

    // agregar poligonos
    function poligonos(){
        var id = document.getElementById('id-poligono').value;
        var latitud = document.getElementById('latitud-poligono').value;
        var longitud = document.getElementById('longitud-poligono').value;

        var retorno = validacion_poligono(latitud, longitud);

        if(retorno){
            let me = this;
            let formData = new FormData();
            formData.append('id', id);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
           
            var spinHandle = loadingOverlay().activate();

            axios.post('/admin/zona/nuevo-poligono', formData, {
            })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);                    
                    verificar_poligono(response);
                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    loadingOverlay().cancel(spinHandle);
            });
        }
    }

    // verificar poligono guardado
    function verificar_poligono(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Poligono agregado');
            $('#latitud-poligono').val('');
            $('#longitud-poligono').val('');
        } else if (response.data.success == 2) {
            toastr.error('Error al guardar poligono');
        } else  if (response.data.success == 3) {
            toastr.error('Zona no encontrada');
        }else{
            toastr.error('Error desconocido');
        }
    }

    // validar poligono a ingresar
    function validacion_poligono(latitud, longitud){
        if (latitud === '') {
            toastr.error("Latitud es requerido");
            return false;
        }

        if(latitud.length > 50){
            toastr.error("50 caracter máximo latitud");
            return false;
        }

        if (longitud === '') {
            toastr.error("Longitud es requerido");
            return false;
        }

        if(longitud.length > 50){
            toastr.error("50 caracter máximo longitud");
            return false;
        }

        return true;
    }

    // modal borrar poligono
    function modalBorrar(){
        $('#modalBorrar').modal('show');
    }

    // borrar poligono de la zona
    function borrarPoligono(){
        var id = document.getElementById('id-poligono').value;       
        var spinHandle = loadingOverlay().activate();
        axios.post('/admin/zona/borrar-poligono',{
        'id': id  
        }) 
        .then((response) => {	
                loadingOverlay().cancel(spinHandle);                    
                verificar_borrado(response);
            })
            .catch((error) => {
                toastr.error('Error del servidor');
                loadingOverlay().cancel(spinHandle);
        });        
    }

    // verificar borrado del poligono
    function verificar_borrado(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Poligono borrados');
            $('#modalBorrar').modal('hide'); 
        } else if (response.data.success == 2) {
            toastr.error('Zona no encontrada');
        }else{
            toastr.error('Error desconocido');
        }
    }

    // ver mapa poligono
    function verPoligono(id){
        window.location.href="{{ url('/admin/zona/ver-poligonos') }}/"+id;
    }

 </script>


@endsection