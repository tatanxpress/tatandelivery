@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    
@stop  

    <section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Notificacion a Clientes</h1>
          </div>  

          <button type="button" onclick="modal()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar Zonas
          </button>          

      </div>
    </section>
     
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de ordenes</h3>
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

    <!-- modal notificacion -->
<div class="modal fade" id="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Enviar Notificación</h4>
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
                                    <label>ID Dispositivo</label>
                                    <input type="text" disabled class="form-control" id="device">
                                </div>

                                <div class="form-group">
                                    <label>Titulo</label>
                                    <input type="text" maxlength="30" class="form-control" id="titulo" placeholder="Titulo notificacion">
                                </div>   

                                <div class="form-group">
                                    <label>Mensaje</label>
                                    <input type="text" maxlength="50" class="form-control" id="mensaje" placeholder="Mensaje notificacion">
                                </div>  
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="enviar()">Enviar</button>                
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


 <script>

  function buscar(){
   
    var id = document.getElementById('identificador').value;

    if(id === ''){
        toastr.error('Identificador es requerido'); 
    }

    var ruta = "{{ url('/admin/control/tabla/notipropi') }}/"+id;
    $('#tablaDatatable').load(ruta); 

  }

  function modalNoti(id){
   
    document.getElementById("formulario").reset();
   
    spinHandle = loadingOverlay().activate();
        axios.post('/admin/control/propi/device',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#device').val(response.data.device);    
                    $('#modal').modal('show');
                }else{
                    toastr.error("ID no encontrado");
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
  }

  function enviar(){
    var device = document.getElementById('device').value;
    var titulo = document.getElementById('titulo').value;
    var mensaje = document.getElementById('mensaje').value;

    if(device === ''){
        toastr.error('Device Id es requerido'); 
        return;
    }

    if(titulo === ''){
        toastr.error('Titulo es requerido'); 
        return;
    }

    if(mensaje === ''){
        toastr.error('Mensaje es requerido'); 
        return;
    }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('device', device);
        formData.append('titulo', titulo);
        formData.append('mensaje', mensaje);
               
        axios.post('/admin/control/propi/notificacion ', formData, {
        })
        .then((response) => {
            
            loadingOverlay().cancel(spinHandle);
            $('#modal').modal('hide');
            if(response.data.success == 1) {               
                toastr.success('Enviado Correctamente');                
            }
            else {
                toastr.error('Error desconocido');
            } 

            loadingOverlay().cancel(spinHandle);
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });

  }

 </script>

 
@stop