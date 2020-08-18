@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     
 @stop 
 
 <!--Pagina para editar el administrador que inicio sesion-->

 <section class="content-header">
       <div class="container-fluid">
         <div class="row mb-2">
           <div class="col-sm-6">
             <h1>Editar Usuario</h1>
           </div>
           <div class="col-sm-6">
             <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="#">Inicio</a></li>
               <li class="breadcrumb-item active">Editar Usuario</li>
             </ol>
           </div>
         </div>
       </div>
     </section>
 
     <section class="content">
       <div class="container-fluid">
         <form class="form-horizontal" id="form1">
         <div class="card card-info">
           <div class="card-header">
             <h3 class="card-title">Formulario</h3>
           </div>
           <div class="card-body">
             <div class="row">            
               <div class="col-md-6">
                    <div class="form-group">
                         <label>Nombre:</label>
                         <input type="text" maxlength="50" id="nombre" class="form-control" placeholder="Nombre" value="{{ $nombre }}">
                    </div>
                    <div class="form-group">
                         <label>Correo:</label>
                         <input type="text" maxlength="100" id="correo" class="form-control" placeholder="Correo" value="{{ $correo }}">
                    </div>                 
               </div>
             </div>
           </div>
            <div class="modal-footer justify-content-between">
                 <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
            </div>  
       </form>
       </div>
    </section>

    
    <section class="content">
       <div class="container-fluid">
         <form class="form-horizontal" id="form1">
         <div class="card card-info">
           <div class="card-header">
             <h3 class="card-title">Formulario 2</h3>
           </div>
           <div class="card-body">
             <div class="row">           
               <div class="col-md-6">
                    <div class="form-group">
                         <label>Contrasena:</label>
                         <input type="text" maxlength="20" id="password1" class="form-control" placeholder="Contrasena">
                    </div>
                    <div class="form-group">
                         <label>Repetir:</label>
                         <input type="text" maxlength="20" id="password2" class="form-control" placeholder="Contrasena repetida">
                    </div>          
               </div>
             </div>
           </div>
            <div class="modal-footer justify-content-between">
                 <button type="button" class="btn btn-primary" onclick="editar1()">Guardar</button>
            </div>  
       </form>
       </div>
    </section>
 
     
 @extends('backend.menus.inferior')
 
 @section('content-admin-js')
 
     <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>


 <script>

    function editar(){
       var nombre = document.getElementById('nombre').value; 
       var correo = document.getElementById('correo').value; 

       var retorno = validar(nombre, correo);
      
        if(retorno){
            var spinHandle = loadingOverlay().activate();
            let formData = new FormData();
                formData.append('nombre', nombre);
                formData.append('correo', correo);

            axios.post('/admin/editar-datos', formData)
                    .then(function (response) {
                        loadingOverlay().cancel(spinHandle);
                        respuesta(response);
                        }) 
                    .catch(function (error) {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error("Error de Servidor!");
                    }); 
                
        }   
    }

    function respuesta(response){
        if (response.data.success == 0) {
             toastr.error('Validacion incorrecta');
         } else if (response.data.success == 1) {
             toastr.success('actualizado'); 
         } 
         else {
             toastr.error('Error desconocido');
         }
    }

    function editar1(){
       var password1 = document.getElementById('password1').value; 
       var password2 = document.getElementById('password2').value; 

       var retorno = validar1(password1, password2);
      
        if(retorno){
            var spinHandle = loadingOverlay().activate();
            let formData = new FormData();
                formData.append('password', password1);

            axios.post('/admin/editar-password', formData)
                    .then(function (response) {
                        loadingOverlay().cancel(spinHandle);
                        respuesta(response);
                        }) 
                    .catch(function (error) {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error("Error de Servidor!");
                    }); 
                
        }   
    }

    function validar(nombre, correo){
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

        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(correo)){
            // valido
        }else{
            toastr.error("El correo es invalido");
            return false;
        }

         return true;
    }

    function validar1(password1, password2){
        if(password1 === ''){
            toastr.error("contraseña es requerido");
            return;
        }
        
        if(password1.length > 20){
            toastr.error("20 caracter máximo contraseña");
            return false;
        }

        if(password1.length < 8){
            toastr.error("8 caracter minimo contraseña");
            return false;
        }

        if(password2 === ''){
            toastr.error("contraseña repetida es requerido");
            return;
        }

        if(password2.length < 8){
            toastr.error("8 caracter minimo contraseña repetida");
            return false;
        }
        
        if(password2.length > 20){
            toastr.error("20 caracter máximo contraseña repetida");
            return false;
        }

        if(password1 !== password2){
            toastr.error("Contraseña no coincide...");
            return false;
        }

        return true;
    }

     
 </script>
 
 @stop
 