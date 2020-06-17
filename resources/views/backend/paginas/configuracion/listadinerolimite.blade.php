@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     <link href="{{ asset('css/backend/estiloToggle.css') }}" type="text/css" rel="stylesheet" /> 
 @stop  
 
 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Configuraciones</h1>
           </div>  
           <button type="button" onclick="modalCambiar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Cambiar configuracion
          </button>  
          <button type="button" onclick="modalVersion()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Versiones App
          </button>  
         
       </div>
     </section> 
     
   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           
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

<!-- modal actualizar -->
<div class="modal fade" id="modalCambiar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Estado de cupones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group" style="margin-left:20px">
                                    <label>Mostrar Cupones en aplicacion</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cupones">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>  

                                
                                <div class="form-group" style="margin-left:20px">
                                    <label>Estado Envio SMS</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="sms">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>  

                                <div class="form-group">
                                    <label>Correo de informacion para Envio SMS</label>
                                    <input type="text" maxlength="100" class="form-control" id="correo" placeholder="Correo">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="cambiar()">Actualizar</button>
            </div>          
        </div>        
    </div>      
</div>


<!-- modal version -->
<div class="modal fade" id="modalVersion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Versiones de App</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-version">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group" style="margin-left:20px">
                                    <label>Estado Versiones de App</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="boolVersiones">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>  

                                
                                <div class="form-group" style="margin-left:20px">
                                    <label>Estado Versiones de App</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="boolVersionesIphone">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>  

                                <div class="form-group">
                                    <label>Version Android</label>
                                    <input type="text" maxlength="25" class="form-control" id="android" placeholder="Android version">
                                </div>

                                <div class="form-group">
                                    <label>Version Iphone</label>
                                    <input type="text" maxlength="25" class="form-control" id="iphone" placeholder="Iphone Version">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="cambiarVersion()">Actualizar</button>
            </div>          
        </div>        
    </div>      
</div>
                 
 
 @extends('backend.menus.inferior')
 
 @section('content-admin-js')
 
     <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>
 
   <script>

    function modalCambiar(){
        document.getElementById("formulario").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/dinero/limite/informacion',{        
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalCambiar').modal('show');

                    if(response.data.info.ver_cupones == 0){
                        $("#cupones").prop("checked", false);
                    }else{
                        $("#cupones").prop("checked", true);
                    }

                    if(response.data.info.activo_sms == 0){
                        $("#sms").prop("checked", false);
                    }else{
                        $("#sms").prop("checked", true);
                    }

                    $('#correo').val(response.data.info.correo);

                }else{
                    toastr.error('No encontrado'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function modalVersion(){
        document.getElementById("formulario-version").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/informacion/de/aplicacion',{        
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
             
                if(response.data.success == 1){

                    $('#modalVersion').modal('show');

                    if(response.data.info.activo == 0){
                        $("#boolVersiones").prop("checked", false);
                    }else{
                        $("#boolVersiones").prop("checked", true);
                    }

                    if(response.data.info.activo_iphone == 0){
                        $("#boolVersionesIphone").prop("checked", false);
                    }else{
                        $("#boolVersionesIphone").prop("checked", true);
                    }

                    $('#iphone').val(response.data.info.iphone);
                    $('#android').val(response.data.info.android);

                }else{
                    toastr.error('No encontrado'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
 
    function cambiar(){      
        var cupones = document.getElementById('cupones').checked;
        var sms = document.getElementById('sms').checked;
        var correo = document.getElementById('correo').value;

        var cupones_1 = 0;
        var sms_1 = 0;

        if(cupones){
            cupones_1 = 1;
        }
        if(sms){
            sms_1 = 1;
        }

        if(correo === ''){
            toastr.error('Correo es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();             
        var formData = new FormData();
        formData.append('cupones', cupones_1);
        formData.append('sms', sms_1);
        formData.append('correo', correo);

        axios.post('/admin/dinero/limite/actualizar', formData, {
        }) 
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1){

                $('#modalCambiar').modal('hide');
                toastr.success('Actualizado'); 

            }else{
                toastr.error('No encontrado'); 
            }
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle); 
            toastr.error('Error');             
        });       
    }

    function cambiarVersion(){
        var boolVersion = document.getElementById('boolVersiones').checked;
        var boolVersionIphone = document.getElementById('boolVersionesIphone').checked;
        var android = document.getElementById('android').value;
        var iphone = document.getElementById('iphone').value;

        var versiones_1 = 0;
        var versiones_iphone = 0;

        if(boolVersion){
            versiones_1 = 1;
        }

        if(boolVersionIphone){
            versiones_iphone = 1;
        }
        
        if(android === ''){
            toastr.error('Android version es requerido');
            return;
        }

        if(iphone === ''){
            toastr.error('Iphone version es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();             
        var formData = new FormData();
        formData.append('version', versiones_1);
        formData.append('versioniphone', versiones_iphone);
        
        formData.append('android', android);
        formData.append('iphone', iphone);

        axios.post('/admin/actualizar/versiones/app', formData, {
        }) 
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1){

                $('#modalVersion').modal('hide');
                toastr.success('Actualizado'); 

            }else{
                toastr.error('No encontrado'); 
            }
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle); 
            toastr.error('Error');             
        }); 
    }

   

 
   </script>
  
 
 
 @stop