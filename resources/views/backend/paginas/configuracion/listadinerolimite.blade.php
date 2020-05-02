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
         
       </div>
     </section> 
     
   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           <div class="card-header">
             <h3 class="card-title" id="texto">Limite actual es: {{ $limite }}</h3>
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

<!-- modal actualizar -->
<div class="modal fade" id="modalCambiar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cambiar limite de orden</h4>
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
                                    <input type="number" step="0.01" class="form-control" id="orden" required>
                                </div>


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
                    $('#orden').val(response.data.info.limite);

                    if(response.data.info.ver_cupones == 0){
                        $("#cupones").prop("checked", false);
                    }else{
                        $("#cupones").prop("checked", true);
                    }

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
        var dinero = document.getElementById('orden').value;
        var cupones = document.getElementById('cupones').checked;

        if(dinero === ''){
            toastr.error('Dinero limite es requerido'); 
            return;
        }

        if(dinero > 99999.99){
            toastr.error('Limite es: 9,999.99'); 
            return;
        }

        var cupones_1 = 0;

        if(cupones){
            cupones_1 = 1;
        }

        var spinHandle = loadingOverlay().activate();             
        var formData = new FormData();
        formData.append('dinero', dinero);
        formData.append('cupones', cupones_1);

        axios.post('/admin/dinero/limite/actualizar', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1){

                $('#modalCambiar').modal('hide');
               
                document.getElementById('texto').innerHTML = "Limite actual es: $"+ dinero;

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