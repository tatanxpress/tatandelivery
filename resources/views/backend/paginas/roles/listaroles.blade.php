@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     
 @stop 
 
 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Roles y Permisos</h1>
           </div>    
           <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                 <i class="fas fa-pencil-alt"></i>
                     Nuevo Administrador
           </button>    
       </div>
     </section>
     
   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           <div class="card-header">
             <h3 class="card-title">Tabla de administradores</h3>
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
 
   
 <!-- modal nuevo-->
 <div class="modal fade" id="modalAgregar">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Nuevo propietario</h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form id="formulario-agregar">
                     <div class="card-body">
                         <div class="row">  
                             <div class="col-md-12">
                                 
                                <div class="form-group">
                                    <label style="color:#191818">Rol</label>
                                    <br>
                                    <div>
                                        <select id="selectrol" class="form-control" required>   
                                            @foreach($roles as $item)                                                
                                                <option value="{{$item->name}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 
 
                                 <div class="form-group">
                                     <label>Nombre</label>
                                     <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                 </div>                            
                                 <div class="form-group">
                                     <label>Contraseña</label>
                                     <input type="text" maxlength="20" class="form-control" id="password-nuevo" placeholder="Contraseña">
                                 </div>
                                 <div class="form-group">
                                     <label>Correo</label>
                                     <input type="text" maxlength="100" class="form-control" id="correo-nuevo" placeholder="Correo">
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

  <!-- modal editar-->
  <div class="modal fade" id="modalEditar">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">Editar</h4>
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
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select id="selectservicio-editar" class="form-control">   
                                       
                                        </select>
                                    </div> 
                                </div> 
 
                                 <div class="form-group">
                                     <label>Nombre</label>
                                     <input type="hidden" id="id-editar">
                                     <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre">
                                 </div>
                                 <div class="form-group">
                                     <label>Telefono</label>
                                     <input type="text" maxlength="20" class="form-control" id="telefono-editar" placeholder="Telefono">
                                 </div>
                                 <div class="form-group">
                                     <label>Correo</label>
                                     <input type="text" maxlength="100" class="form-control" id="correo-editar" placeholder="Correo">
                                 </div>
                                 <div class="form-group">
                                     <label>DUI</label>
                                     <input type="text" maxlength="25" class="form-control" id="dui-editar" placeholder="DUI">
                                 </div>

                                 <div class="form-group">
                                    <label>Activo</label>
                                    <input type="checkbox" id="activo-editar">
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
 
  <!-- incluir tabla --> 
   <script type="text/javascript">	 
     $(document).ready(function(){       
         var ruta = "{{ URL::to('admin/roles/tabla/lista') }}";
         $('#tablaDatatable').load(ruta);
     }); 
     
  </script>
 
   <script>

      
     function modalAgregar(){
         document.getElementById("formulario-agregar").reset();
         $('#modalAgregar').modal('show');
     }
 
     function nuevo(){
         var idrol = document.getElementById('selectrol').value;
         var nombre = document.getElementById('nombre-nuevo').value;
         var correo = document.getElementById('correo-nuevo').value; 
         var password = document.getElementById('password-nuevo').value;
 
         var retorno = validarNuevo(nombre, correo, password);
 
         if(retorno){
 
             var spinHandle = loadingOverlay().activate();
             var formData = new FormData();
             formData.append('rol', idrol);
             formData.append('nombre', nombre);
             formData.append('correo', correo);
             formData.append('password', password);
            
             axios.post('/admin/roles/nuevo', formData, { 
                     })
                     .then((response) => {
                         loadingOverlay().cancel(spinHandle);
                        // respuestaNuevo(response);
                        console.log(response);
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
             toastr.success('Agregado');           
            
             var ruta = "{{ url('/admin/propietarios/tabla/lista') }}";
             $('#tablaDatatable').load(ruta);
             $('#modalAgregar').modal('hide');     
         } else if(response.data.success == 2){
             toastr.error('No se pudo agregar');
         } else if(response.data.success == 3){
             toastr.error('El correo ya esta registrado');
         }
         else {
             toastr.error('Error desconocido');
         }
     } 
 
     function validarNuevo(nombre, correo, password){
         
         if(nombre === ''){
             toastr.error("nombre es requerido");
             return;
         }
         
         if(nombre.length > 50){
             toastr.error("50 caracter máximo nombre");
             return false;
         }
        
         if(correo === ''){
             toastr.error("correo es requerido");
             return;
         }
         
         if(correo.length > 100){
             toastr.error("100 caracter máximo correo");
             return false;
         }

         if(password === ''){
             toastr.error("password es requerido");
             return;
         }
         
         if(password.length > 20){
             toastr.error("20 caracter máximo password");
             return false;
         }
 
         return true;
     } 
 
     function informacion(id){
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/propietarios/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

              
                if(response.data.success == 1){
                    
                    var tipo = document.getElementById("selectservicio-editar");
                    // limpiar select
                    document.getElementById("selectservicio-editar").options.length = 0;
                
                    $.each(response.data.servicios, function( key, val ){  
                    if(response.data.propietario.servicios_id == val.id){
                            $('#selectservicio-editar').append('<option value="' +val.id +'" selected="selected">'+val.identificador+'</option>');
                    }else{
                            $('#selectservicio-editar').append('<option value="' +val.id +'">'+val.identificador+'</option>');
                    }
                    });
                
                    $('#id-editar').val(response.data.propietario.id);
                    $('#nombre-editar').val(response.data.propietario.nombre);
                    $('#correo-editar').val(response.data.propietario.correo);
                    $('#telefono-editar').val(response.data.propietario.telefono);
                    $('#dui-editar').val(response.data.propietario.dui);
                    if(response.data.propietario.activo == 0){
                        $("#activo-editar").prop("checked", false);
                    }else{
                        $("#activo-editar").prop("checked", true);
                    }

                    $('#modalEditar').modal('show');
             
                }else{
                    toastr.error('no encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
     
    function editar(){

        var id = document.getElementById('id-editar').value;
        var identificador = document.getElementById('selectservicio-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var telefono = document.getElementById('telefono-editar').value;
        var correo = document.getElementById('correo-editar').value; 
        var dui = document.getElementById('dui-editar').value;
        var activo = document.getElementById('activo-editar').checked;

        var retorno = validarNuevo(nombre, telefono, correo, dui);

        if(retorno){
            var activo_1 = 0;
            if(activo){
                activo_1 = 1;
            }

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('identificador', identificador);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('correo', correo);
            formData.append('dui', dui);
            formData.append('activo', activo_1);
        
            axios.post('/admin/propietarios/editar', formData, { 
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
         } else if (response.data.success == 1) {
            toastr.error('El correo ya esta registrado'); 
         } else if(response.data.success == 2){
            toastr.success('Actualizado');           
            
             var ruta = "{{ url('/admin/propietarios/tabla/lista') }}";
             $('#tablaDatatable').load(ruta);
             $('#modalEditar').modal('hide');  
         } else if(response.data.success == 3){
             toastr.error('registrado no encontrado');
         }
         else {
             toastr.error('Error desconocido');
         }
     } 
 
   </script>
  
 
 
 @stop