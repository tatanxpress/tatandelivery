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
           <button type="button" onclick="modalCambiar()" class="btn btn-success btn-sm" style="margin-top:15px">
                <i class="fas fa-pencil-alt"></i>
                    Cambiar configuracion
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
<div class="modal fade" id="modalCambiar" style="margin-top:15px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Estados</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Fecha del Token de Wompi</label>
                                    <input type="text" disabled class="form-control" id="fecha-token">
                                </div>

                                <div class="form-group">
                                    <label>Comisión</label>
                                    <input type="number" step="0.01" class="form-control" id="comision">
                                </div>

                                <div class="form-group" style="margin-left:20px">
                                    <label>Estado de ingresar Credi Puntos</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbcredito">
                                        <div class="slider round">
                                        <span class="on">Activado</span>
                                         <span class="off">Desactivado</span>
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
                                    <label>Estado Versiones de App Android</label><br>
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="boolVersiones">
                                        <div class="slider round">
                                            <span class="on">Activar</span>
                                            <span class="off">Desactivar</span>
                                        </div>
                                    </label>
                                </div>  

                                
                                <div class="form-group" style="margin-left:20px">
                                    <label>Estado Versiones de App Iphone</label><br>
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

                    $('#comision').val(response.data.info.comision);

                    if(response.data.info.activo_tarjeta == 0){
                        $("#cbcredito").prop("checked", true);
                    }else{
                        $("#cbcredito").prop("checked", false);
                    }
                  
                    $('#fecha-token').val(response.data.fecha);

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
        var comision = document.getElementById('comision').value;
        var cbcredito = document.getElementById('cbcredito').checked;

        var cbcredito_1 = 1;

        if(cbcredito){
            cbcredito_1 = 0;
        }
        var spinHandle = loadingOverlay().activate();             
        var formData = new FormData();
      
        formData.append('cbcredito', cbcredito_1);
        formData.append('comision', comision);

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

  
   

 
   </script>
  
 
 
 @stop