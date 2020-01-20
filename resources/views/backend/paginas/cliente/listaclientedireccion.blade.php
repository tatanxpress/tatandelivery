@extends('backend.menus.superior')
 
@section('content-admin-css')
<link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Direccion</h1>
          </div>        
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">  
          <div class="card-header">
            <h3 class="card-title">Tabla de Direcciones</h3>
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

  <div class="modal fade" id="modal-info">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Dirección</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">        
            <div class="form-group">
                <label style="color:#191818">Nombre</label>
                <br>
                <input id="nombre" disabled class="form-control"></label>
            </div>                        
            <div class="form-group">
                <label style="color:#191818">Dirección</label>
                <br>
                <input id="direccion" disabled class="form-control"></label>
            </div>     
            <div class="form-group">
                <label style="color:#191818"># Casa</label>
                <br>
                <input id="numero" disabled class="form-control"></label>
            </div>     
            <div class="form-group">
                <label style="color:#191818">Punto de referencia</label>
                <br>                
                <input id="referencia" disabled class="form-control"></label>
            </div>
            <div class="form-group">
                <label style="color:#191818">Teléfono</label>
                <br>
                <input id="telefono" disabled class="form-control"></label>
            </div>  
        </div>
          
        <div class="modal-footer float-right">
          <button class="btn btn-primary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>      
    </div>        
  </div>
	

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Cliente</h4>
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
                                    <label>Disponibilidad</label><br>
                                    <input type="hidden" id="id-editar">
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="toggle-activo">
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
                <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
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
      id = {{ $id }};
      var ruta = "{{ url('/admin/cliente/tablas/direccion/') }}/"+id;   
      $('#tablaDatatable').load(ruta);
    });    
 </script>

  <script>

    // direccion info
    function informacion(id){

      spinHandle = loadingOverlay().activate();
      axios.post('/admin/cliente/direcciones/informacion',{
        'id': id  
          })
          .then((response) => {	
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1){            
                $('#modal-info').modal('show');
                $('#nombre').val(response.data.direccion.nombre);
                $('#direccion').val(response.data.direccion.direccion);
                $('#numero').val(response.data.direccion.numero_casa);
                $('#referencia').val(response.data.direccion.punto_referencia);        
                $('#telefono').val(response.data.direccion.telefono);        
            }else{
                toastr.error('Direccion no encontrada'); 
            }
            
          })
          .catch((error) => { 
            loadingOverlay().cancel(spinHandle); 
            toastr.error('Error del servidor');    
      }); 
    }

    // ver punto en el mapa
    function verMapa(id){           
      window.location.href="{{ url('/admin/cliente/ubicacion') }}/"+id;
    }  

    

  </script>



@stop