@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     
 @stop  
 
   <section class="content-header">
     <div class="container-fluid">
         <div class="col-sm-12">
           <h1>Credi Puntos (Verificados)</h1>
         </div>   
         <button type="button" onclick="modal()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Agregar Credito Manual
            </button>         
     </div> 
   </section> 
      
   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           <div class="card-header">
             <h3 class="card-title">Tabla</h3>
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
                <h4 class="modal-title">Agregar Credi Puntos</h4>
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
                                    <label>Área + Número Unido</label>
                                    <input type="text" class="form-control" id="numero" placeholder="Área + Teléfono">
                                </div>

                                <button type="button" class="btn btn-primary" onclick="verificarNombre()">Verificar Cliente</button>

                                </br>
                                <div class="form-group">
                                    <label>Credi Puntos a Agregar</label>
                                    <input type="number" value="0.00" step="0.01" min="0" max="100" class="form-control" id="credito-nuevo" placeholder="Credi Puntos">
                                </div>

                                <div class="form-group">
                                    <label>Nota</label>
                                    <input type="text" max="200" class="form-control" id="nota-nuevo" placeholder="Nota">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardar()">Guardar</button>
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
         var ruta = "{{ URL::to('admin/registro/tabla/credipuntos') }}";
         $('#tablaDatatable').load(ruta);
     });  
 

     function modal(){
    document.getElementById("formulario-nuevo").reset();
    $('#modalAgregar').modal('show');
  }

  function verificarNombre(){

    var numero = document.getElementById('numero').value;        

    if(numero === ''){
        toastr.error('Número es requerido');
    }

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();
    formData.append('numero', numero);
    
    axios.post('/admin/buscar/cliente/areanumero', formData, { 
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);
            console.log(response);
            if(response.data.success == 1){
                var mensaje = "Nombre: " + response.data.nombre;
                toastr.success(mensaje);
            }else{
                toastr.error('No encontrado');
            }
        }) 
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
  }

  function guardar(){

    var numero = document.getElementById('numero').value;        
    var credito = document.getElementById('credito-nuevo').value;
    var nota = document.getElementById('nota-nuevo').value;

    if(numero === ''){
        toastr.error('Número es requerido');
    }

    if(credito === ''){
        toastr.error('Credito es requerido');
    }

    if(nota === ''){
        toastr.error('Nota es requerido');
    }

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();
    formData.append('numero', numero);
    formData.append('credito', credito);
    formData.append('nota', nota);
    
    axios.post('/admin/agregar/credito/manual', formData, { 
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);
            revisar(response);
        }) 
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
  }

  function revisar(response){
    if (response.data.success == 0) {
        toastr.error('Validacion incorrecta');
    } else if (response.data.success == 1) {
        toastr.success('Agregado'); 
        var ruta = "{{ url('/admin/registro/tabla/credipuntos') }}";
        $('#tablaDatatable').load(ruta);
        $('#modalAgregar').modal('hide');  
    } else if(response.data.success == 2){
        toastr.error('Error al guardar');
    }
    else { 
        toastr.error('Error desconocido');
    }
}
     
  </script>
 
 
  
 @stop