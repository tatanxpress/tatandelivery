@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     
 @stop 


 <section class="content-header">
       <div class="container-fluid">
         <div class="row mb-2">
           <div class="col-sm-6">
             <h1>Notificaciones Global</h1>
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
                         <label>Titulo:</label>
                         <input type="text" maxlength="25" id="titulo" class="form-control" placeholder="Titulo de mensaje">
                    </div>
                         <label>Mensaje:</label>
                         <input type="text" maxlength="25" id="mensaje" class="form-control" placeholder="Mensaje">
                    </div> 

                    <div class="form-group">
                        <label>Marcar Para Enviar</label>
                        <input type="checkbox" id="marcador">
                    </div>

               </div>
             </div>
           </div>
            <div class="modal-footer justify-content-between">
                 <button type="button" class="btn btn-primary" onclick="enviar()">Enviar</button>
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

    function enviar(){
       var titulo = document.getElementById('titulo').value; 
       var mensaje = document.getElementById('mensaje').value; 
       var marcador = document.getElementById('marcador').checked;

       var retorno = validar(titulo, mensaje);
      
        if(!marcador){
            toastr.error('Marcar El Marcador');
            return;
        }

        if(retorno){

            toastr.success('actualizado'); 

            return;

            var spinHandle = loadingOverlay().activate();
            let formData = new FormData();
                formData.append('titulo', titulo);
                formData.append('mensaje', mensaje);

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

    function validar(titulo, mensaje){
        if(titulo === ''){
            toastr.error("titulo es requerido");
            return;
        }
        
        if(titulo.length > 25){
            toastr.error("25 caracter máximo titulo");
            return false;
        }

        if(mensaje === ''){
            toastr.error("mensaje es requerido");
            return;
        }
        
        if(mensaje.length > 25){
            toastr.error("25 caracter máximo mensaje");
            return false;
        }

        return true;
    }

    function validar1(password1, password2){
        if(password1 === ''){
            toastr.error("password1 es requerido");
            return;
        }
        
        if(password1.length > 20){
            toastr.error("20 caracter máximo password1");
            return false;
        }

        if(password1.length < 8){
            toastr.error("8 caracter minimo password1");
            return false;
        }

        if(password2 === ''){
            toastr.error("password2 es requerido");
            return;
        }

        if(password2.length < 8){
            toastr.error("8 caracter minimo password2");
            return false;
        }
        
        if(password2.length > 20){
            toastr.error("20 caracter máximo password2");
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
 