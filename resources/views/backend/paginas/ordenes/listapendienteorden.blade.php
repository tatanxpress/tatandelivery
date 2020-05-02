@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     
 @stop  
 
 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Ordenes pendiente de motorista</h1>
           </div>
       </div> 
     </section>
      
   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           <div class="card-header">
             <h3 class="card-title">Tabla de ordenes pendiente motorista</h3>
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
  


 <!-- modal ocultar -->
<div class="modal fade" id="modalOcultar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ocultar registro</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-ocultar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="hidden" id="id-ocultar">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-info"  data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="ocultar()">Ocultar</button>

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
         var ruta = "{{ URL::to('admin/ordenpendite/tabla/lista') }}";
         $('#tablaDatatable').load(ruta);
     }); 
     
  </script>
 
   <script>

    function modalOcultar(id){
        document.getElementById("formulario-ocultar").reset();
        $('#modalOcultar').modal('show');
        $('#id-ocultar').val(id);
    }
 
     // ubicacion del cliente para la orden
     function mapa(id){        
         window.location.href="{{ url('/admin/ordenes/ubicacion/') }}/"+id;
     }
  
     // pasar id para ver producto de la orden
     function producto(id){
         window.location.href="{{ url('/admin/ordenes/listaproducto') }}/"+id;
     }

     function ocultar(){
        var id = document.getElementById('id-ocultar').value;
    
        spinHandle = loadingOverlay().activate();

        var formData = new FormData();
        formData.append('id', id);
                            
        axios.post('/admin/ocultar/ordenpendiente',formData,{
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
 
            if(response.data.success == 1){
                $('#modalOcultar').modal('show');
                toastr.success('Ocultado'); 

                var ruta = "{{ url('/admin/ordenpendite/tabla/lista') }}";
                $('#tablaDatatable').load(ruta);
         
            }else{
                toastr.error('ID no encontrado'); 
            }
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle); 
            toastr.error('Error del servidor');    
        });        
     }
 
   </script>
  
 
 
 @stop