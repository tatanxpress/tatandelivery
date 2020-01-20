@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Revisadores</h1>
          </div>    
          <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Revisador
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de revisadores</h3>
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


<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo revisador</h4>
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
                                    <label>Identificador</label>
                                    <input type="text" maxlength="50" class="form-control" id="identificador-nuevo" placeholder="Identificador unico">
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Direccion</label>
                                    <input type="text" maxlength="800" class="form-control" id="direccion-nuevo" placeholder="Direccion">
                                </div>
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" placeholder="Telefono">
                                </div>
                                <div class="form-group">
                                    <label>Latitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="latitud-nuevo" placeholder="Latitud">
                                </div>
                                <div class="form-group">
                                    <label>Longitud</label>
                                    <input type="text" maxlength="50" class="form-control" id="longitud-nuevo" placeholder="Longitud">
                                </div>
                           
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="text" disabled class="form-control" placeholder="12345678">
                                </div>
                                                          
                                <div class="form-group">
                                    <label>Activo</label>
                                    <input type="checkbox" id="activo-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Codigo para verificar orden</label>
                                    <input type="text" maxlength="10" class="form-control" id="codigo-nuevo" placeholder="Codigo">
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
                <h4 class="modal-title">Editar revisador</h4>
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

                                <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre">
                            </div>
                            <div class="form-group">
                                <label>Direccion</label>
                                <input type="text" maxlength="800" class="form-control" id="direccion-editar" placeholder="Direccion">
                            </div>
                            <div class="form-group">
                                <label>Telefono</label>
                                <input type="text" maxlength="20" class="form-control" id="telefono-editar" placeholder="Telefono">
                            </div>
                            <div class="form-group">
                                <label>Latitud</label>
                                <input type="text" maxlength="50" class="form-control" id="latitud-editar" placeholder="Latitud">
                            </div>
                            <div class="form-group">
                                <label>Longitud</label>
                                <input type="text" maxlength="50" class="form-control" id="longitud-editar" placeholder="Longitud">
                            </div>
                                              
                            <div class="form-group">
                                <label>Activo</label>
                                <input type="checkbox" id="activo-editar">
                            </div>

                            <div class="form-group">
                                <label>Disponible</label>
                                <input type="checkbox" id="disponible-editar">
                            </div>
                            <div class="form-group">
                                <label>Codigo para verificar orden</label>
                                <input type="text" maxlength="10" class="form-control" id="codigo-editar" placeholder="Codigo">
                            </div>

                            <div class="form-group">
                                <label>Fecha registrado</label>
                                <input type="text" disabled class="form-control" id="fecha-editar">
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

<!-- modal reseteo -->
<div class="modal fade" id="modalReseteo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Resetear contraseña a: 12345678</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-reseteo">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                            <div class="form-group">
                               
                                <input type="hidden" id="id-reseteo">

                            </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="reseteo()">Resetear</button>
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
        var ruta = "{{ URL::to('admin/revisador/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script> 

    function modalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    } 

    function modalReseteo(id){
        document.getElementById("formulario-reseteo").reset();

        $('#id-reseteo').val(id);
        $('#modalReseteo').modal('show');
    } 

    function nuevo(){
        var identi = document.getElementById('identificador-nuevo').value;
        var nombre = document.getElementById('nombre-nuevo').value;
        var direccion = document.getElementById('direccion-nuevo').value;
        var telefono = document.getElementById('telefono-nuevo').value;
        var latitud = document.getElementById('latitud-nuevo').value;
        var longitud = document.getElementById('longitud-nuevo').value;
        var codigo = document.getElementById('codigo-nuevo').value;
        var cbactivo = document.getElementById('activo-nuevo').checked;
        

        var retorno = validarNuevo(identi, nombre, direccion, telefono, latitud, longitud, codigo);

        if(retorno){

            var cbactivo_1 = 0;
            if(cbactivo){
                cbactivo_1 = 1;
            }

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('identi', identi);
            formData.append('nombre', nombre);
            formData.append('direccion', direccion);
            formData.append('telefono', telefono);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('codigo', codigo);
            formData.append('cbactivo', cbactivo_1);


            axios.post('/admin/revisador/nuevo', formData, { 
                    })
                    .then((response) => {
                        loadingOverlay().cancel(spinHandle);
                        respuestaNuevo(response);
                       
                    })
                    .catch((error) => {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error('Error');
                    });
        }
    }

    function respuestaNuevo(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.error('identificador ya registrado'); 
        } else if(response.data.success == 2){
            toastr.success('Agregado');           
           
            var ruta = "{{ url('/admin/revisador/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');  
            
        } else if(response.data.success == 3){
            toastr.error('Error al agregar');
        } 
        else {
            toastr.error('Error desconocido');
        }
    } 

    function validarNuevo(identi, nombre, direccion, telefono, latitud, longitud, codigo){

        if(identi === ''){
            toastr.error("identificador es requerido");
            return;
        }
        
        if(identi.length > 50){
            toastr.error("50 caracter máximo identificador");
            return false;
        }
       
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if(direccion === ''){
            toastr.error("direccion es requerido");
            return;
        }
        
        if(direccion.length > 800){
            toastr.error("800 caracter máximo direccion");
            return false;
        }

        if(telefono === ''){
            toastr.error("telefono es requerido");
            return;
        }
        
        if(telefono.length > 20){
            toastr.error("20 caracter máximo telefono");
            return false;
        }

        if(latitud === ''){
            toastr.error("latitud es requerido");
            return;
        }
        
        if(latitud.length > 50){
            toastr.error("50 caracter máximo latitud");
            return false;
        }

        if(longitud === ''){
            toastr.error("longitud es requerido");
            return;
        }
        
        if(longitud.length > 50){
            toastr.error("50 caracter máximo longitud");
            return false;
        }

        if(codigo === ''){
            toastr.error("codigo es requerido");
            return;
        }
        
        if(codigo.length > 10){
            toastr.error("10 caracter máximo codigo");
            return false;
        }

        return true;
    }

 
    function informacion(id){
        spinHandle = loadingOverlay().activate();
        document.getElementById("formulario-editar").reset();

        axios.post('/admin/revisador/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.revisador.id);
                    $('#nombre-editar').val(response.data.revisador.nombre);
                    $('#direccion-editar').val(response.data.revisador.direccion);
                    $('#telefono-editar').val(response.data.revisador.telefono);
                    $('#latitud-editar').val(response.data.revisador.latitud);
                    $('#longitud-editar').val(response.data.revisador.longitud);
                    $('#codigo-editar').val(response.data.revisador.codigo);
                    $('#fecha-editar').val(response.data.revisador.fecha);

                    if(response.data.revisador.activo == 0){
                        $("#activo-editar").prop("checked", false);
                    }else{
                        $("#activo-editar").prop("checked", true);
                    }

                    if(response.data.revisador.disponible == 0){
                        $("#disponible-editar").prop("checked", false);
                    }else{
                        $("#disponible-editar").prop("checked", true);
                    }

                }else{
                    toastr.error("ID no encontrado");
                }

            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function reseteo(){
        var id = document.getElementById('id-reseteo').value;

        var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);

        axios.post('/admin/revisador/reseteo', formData, { 
                    })
                    .then((response) => {
                        loadingOverlay().cancel(spinHandle);
                        respuestaReseteo(response);
                    })
                    .catch((error) => {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error('Error');
                    });
    }

    function respuestaReseteo(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
            toastr.success('Actualizado');
        }
        else {
            toastr.error('Error desconocido');
        }
    } 
    
    function editar(){
        var id = document.getElementById('id-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var direccion = document.getElementById('direccion-editar').value;
        var telefono = document.getElementById('telefono-editar').value;
        var latitud = document.getElementById('latitud-editar').value;
        var longitud = document.getElementById('longitud-editar').value;
        var codigo = document.getElementById('codigo-editar').value;
        var cbactivo = document.getElementById('activo-editar').checked;
        var cbdisponible = document.getElementById('disponible-editar').checked;

        var retorno = validarEditar(nombre, direccion, telefono, latitud, longitud, codigo);

        if(retorno){

            var cbactivo_1 = 0;
            var cbdisponible_1 = 0;
            if(cbactivo){
                cbactivo_1 = 1;
            }

            if(cbdisponible){
                cbdisponible_1 = 1;
            }

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('direccion', direccion);
            formData.append('telefono', telefono);
            formData.append('latitud', latitud);
            formData.append('longitud', longitud);
            formData.append('codigo', codigo);
            formData.append('cbactivo', cbactivo_1);
            formData.append('cbdisponible', cbdisponible_1);

            axios.post('/admin/revisador/editar', formData, { 
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

    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
            toastr.success('Actualizado');
            
            var ruta = "{{ url('/admin/revisador/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');
            
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

    
    function validarEditar(nombre, direccion, telefono, latitud, longitud, codigo){
    
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if(direccion === ''){
            toastr.error("direccion es requerido");
            return;
        }
        
        if(direccion.length > 800){
            toastr.error("800 caracter máximo direccion");
            return false;
        }

        if(telefono === ''){
            toastr.error("telefono es requerido");
            return;
        }
        
        if(telefono.length > 20){
            toastr.error("20 caracter máximo telefono");
            return false;
        }

        if(latitud === ''){
            toastr.error("latitud es requerido");
            return;
        }
        
        if(latitud.length > 50){
            toastr.error("50 caracter máximo latitud");
            return false;
        }

        if(longitud === ''){
            toastr.error("longitud es requerido");
            return;
        }
        
        if(longitud.length > 50){
            toastr.error("50 caracter máximo longitud");
            return false;
        }

        if(codigo === ''){
            toastr.error("codigo es requerido");
            return;
        }
        
        if(codigo.length > 10){
            toastr.error("10 caracter máximo codigo");
            return false;
        }

        return true;
  }
  

  </script>
 


@stop