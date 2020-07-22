@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    
@stop  

<section class="content">
    <div class="container-fluid">
        <form class="form-horizontal" id="form1">
            <div class="card card-info">
                    <div class="card-header">
                    <h3 class="card-title">Notificacion a propietarios y motoristas</h3>
                    </div>
                    
                <div class="card-body">
                    <div class="row">           
                        <div class="col-md-6">


                        <div class="form-group" style="width: 75%">
                            <label>Buscar por identificador de Servicio</label>
                            <input type="text" class="form-control" id="identificador" placeholder="Identificador Servicio">
                        </div> 

                        
                        <button type="button" onclick="buscar()" class="btn btn-success btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                                Buscar propietarios
                        </button>     
                            
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-success" onclick="modalMotorista()">Notificación a Motorista</button>
                </div>  
                
            </div>
        </form>
    </div>
</section>


<!-- seccion frame -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Tabla de propietarios</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="tablaDatatable">
                        </div>
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

<!-- modal motorista -->
<div class="modal fade" id="modalMotorista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Notificación a Motorista</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-motorista">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 

                                <div class="form-group">
                                    <label style="color:#191818">Motoristas</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-motorista">
                                                <option value="0" selected>Seleccionar</option>
                                            @foreach($motoristas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach   
                                        </select>
                                    </div> 
                                </div> 


                                <div class="form-group">
                                    <label>Título</label>
                                    <input type="text" maxlength="100" class="form-control" id="titulo-motorista" placeholder="Título">
                                </div>

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-motorista" placeholder="Descripción">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="enviarNotificacion()">Enviar</button>
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

 
  function modalMotorista(){
    document.getElementById("formulario-motorista").reset();
    $('#modalMotorista').modal('show');
  }

  function enviarNotificacion(){

    var id = document.getElementById('select-motorista').value;
    var titulo = document.getElementById('titulo-motorista').value;
    var descripcion = document.getElementById('descripcion-motorista').value;

    if(titulo === ''){
        toastr.error('Título es requerido');   
        return; 
    }

    if(descripcion === ''){
        toastr.error('Descripción es requerido');
        return;
    }

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();
    
    formData.append('id', id);
    formData.append('titulo', titulo);
    formData.append('descripcion', descripcion);
            
    axios.post('/admin/control/motorista/notificacion ', formData, {
    })
    .then((response) => {
        
        loadingOverlay().cancel(spinHandle);

        if(response.data.success == 1) {
            toastr.success('Enviado');
        } else if(response.data.success == 2){
            toastr.error('No se puede enviar, device 0000');
        } else if(response.data.success == 3){
            toastr.error('Motorista no encontrado');
        }
        else {
            toastr.error('Error desconocido');
        } 

        $('#modalMotorista').modal('hide');
    })
    .catch((error) => {
        loadingOverlay().cancel(spinHandle);
        toastr.error('Error');
    });

  }


 </script>

 
@stop