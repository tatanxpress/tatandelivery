@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Administradores para revision de ordenes sin motorista</h1>
          </div>
          
           <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Admin revisador
          </button>       
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de admin revisadores</h3>
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
                                    <label>Nombre</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                </div>
                               
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" placeholder="Telefono">
                                </div>
                                <div class="form-group">
                                    <label>Contrasena</label>
                                    <input type="text" maxlength="20" class="form-control" id="password-nuevo" placeholder="Contrasena">
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
                                <input type="hidden" id="id-editar">
                            </div>

                            <div class="form-group">
                                <label>Activo | Inactivo</label>
                                <input type="checkbox" id="activo-editar">
                            </div>

                            <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre">
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
        var ruta = "{{ URL::to('admin/adminrevisador/tabla/lista') }}";
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
        var nombre = document.getElementById('nombre-nuevo').value;
        var telefono = document.getElementById('telefono-nuevo').value;
        var password = document.getElementById('password-nuevo').value;

        var retorno = validarNuevo(nombre, telefono, password);

        if(retorno){

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('password', password);
            
            axios.post('/admin/adminrevisador/nuevo', formData, { 
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
        }  else if(response.data.success == 2){
            toastr.error('Telefono ya existe');
        }  else if(response.data.success == 3){
            toastr.success('Agregado');           
           
            var ruta = "{{ url('/admin/adminrevisador/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');  
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

    function validarNuevo(nombre, telefono, password){

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
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
        
        if(password === ''){
            toastr.error("password es requerido");
            return;
        }

        if(password.length < 8){
            toastr.error("8 caracter minimo para password");
            return false;
        }
        
        if(password.length > 20){
            toastr.error("20 caracter máximo para password");
            return false;
        }

        return true;
    }

 
    function informacion(id){
        spinHandle = loadingOverlay().activate();
        document.getElementById("formulario-editar").reset();

        axios.post('/admin/adminrevisador/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.admin.id);
                    $('#nombre-editar').val(response.data.admin.nombre);
                   
                    if(response.data.admin.activo == 0){
                        $("#activo-editar").prop("checked", false);
                    }else{
                        $("#activo-editar").prop("checked", true);
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
    
    function editar(){
        var id = document.getElementById('id-editar').value;
        var activo = document.getElementById('activo-editar').checked;
        var nombre = document.getElementById('nombre-editar').value;

        var activo_1 = 0;
        if(activo){
            activo_1 = 1;
        }


        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }   
       
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('activo', activo_1);      
        formData.append('nombre', nombre);     
        axios.post('/admin/adminrevisador/editar', formData, { 
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

    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
            toastr.options.progressBar = true;
                      
            var ruta = "{{ url('/admin/adminrevisador/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');
            
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

    function reseteo(){
        var id = document.getElementById('id-reseteo').value;

        var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);

        axios.post('/admin/adminrevisador/reseteo', formData, { 
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

  </script>
 


@stop