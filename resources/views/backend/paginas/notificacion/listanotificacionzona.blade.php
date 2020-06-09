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

          <button type="button" onclick="modal1()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Notificación por Zonas
          </button>     

           <button type="button" onclick="modal2()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Notificación individual
          </button>       

      </div>
    </section>

    <!-- modal notificacion por zona -->
    <div class="modal fade" id="modal1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Enviar Notificación por zonas</h4>
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
                                        <label>Titulo</label>
                                        <input type="text" maxlength="30" class="form-control" id="titulo" placeholder="Titulo notificacion">
                                    </div>   

                                    <div class="form-group">
                                        <label>Mensaje</label>
                                        <input type="text" maxlength="50" class="form-control" id="mensaje" placeholder="Mensaje notificacion">
                                    </div> 

                                    <div class="form-group">
                                        <label>Clave Administrador</label>
                                        <input type="text" maxlength="200" class="form-control" id="clave" placeholder="Clave">
                                    </div>  

                                    <div class="form-group">
                                        <label style="color:#191818">Zonas identificador</label>
                                        <br>
                                        <div>
                                            <select class="form-control" id="zonas-nuevo" multiple="multiple" >   
                                                @foreach($zonas as $item)                                                
                                                    <option value="{{$item->id}}">{{$item->identificador}}</option>
                                                @endforeach                                         
                                            </select>
                                        </div>  
                                    </div>  

                                    <div class="form-group">
                                        <label>Clientes que llegara la notificacion</label>
                                        <button type="button" class="btn btn-primary" onclick="buscar()">Buscar</button>
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

       <!-- modal notificacion individual -->
    <div class="modal fade" id="modal2">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Enviar Notificacion individual</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formulario1">
                        <div class="card-body">
                            <div class="row">  
                                <div class="col-md-12">
                                    
                                    <div class="form-group">
                                        <label>Titulo</label>
                                        <input type="text" maxlength="30" class="form-control" id="titulo1" placeholder="Titulo notificacion">
                                    </div>   

                                    <div class="form-group">
                                        <label>Mensaje</label>
                                        <input type="text" maxlength="200" class="form-control" id="mensaje1" placeholder="Mensaje notificacion">
                                    </div> 

                                    <div class="form-group">
                                        <label>Numero del cliente registrado</label>
                                        <input type="text" maxlength="50" class="form-control" id="numero" placeholder="Numero cliente">
                                    </div>  

                                    <div class="form-group">
                                        <label>Buscar cliente</label>
                                        <button type="button" class="btn btn-primary" onclick="buscar2()">Buscar</button>
                                    </div>
                                
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="enviar1()">Enviar</button>                
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

  function modal1(){
    document.getElementById("formulario").reset();
    $('#modal1').modal('show');
  }

  function modal2(){
    document.getElementById("formulario1").reset();
    $('#modal2').modal('show');
  }

  // cuantos clientes les llegara la notificacion
  function buscar(){
    var zonas = $('#zonas-nuevo').val(); 

    if(zonas.length == null || zonas.length == 0){
        toastr.error('Seleccionar mínimo 1 zona');
        return;
    }

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();
    
    for (var i = 0; i < zonas.length; i++) {
        formData.append('idzonas[]', zonas[i]);
    }                
    
    axios.post('/admin/control/buscar/clienteszona', formData, {
    })
    .then((response) => {
        
        loadingOverlay().cancel(spinHandle);
       
        if(response.data.success == 1) {

            var mensaje = "Encontrados " + response.data.info;
            toastr.success(mensaje);
        }
        else {
         toastr.error('Error desconocido');
        }
    })
    .catch((error) => {
        loadingOverlay().cancel(spinHandle);
        toastr.error('Error');
    }); 

  }

 // buscara cliente por su numero registrado
  function buscar2(){
    var numero = document.getElementById('numero').value;
    if(numero === ''){
        toastr.error('Numero es requerido'); 
        return;
    }

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();
    
    formData.append('numero', numero);
    
    axios.post('/admin/control/buscar/cliente', formData, {
    })
    .then((response) => {
       
        loadingOverlay().cancel(spinHandle);
       
        if(response.data.success == 1) {
            var nombre = "Nombre: " + response.data.nombre;
            toastr.success(nombre);
        }
        else {
         toastr.error('No encontrado');
        }
    })
    .catch((error) => {
        loadingOverlay().cancel(spinHandle);
        toastr.error('Error');
    }); 

  }

  // enviar notificacion individual
  function enviar1(){
      
    var titulo = document.getElementById('titulo1').value;
    var mensaje = document.getElementById('mensaje1').value;
    var numero = document.getElementById('numero').value;

    if(titulo === ''){
        toastr.error('Titulo es requerido'); 
        return;
    }

    if(mensaje === ''){
        toastr.error('Mensaje es requerido'); 
        return;
    }

    if(numero === ''){
        toastr.error('Numero es requerido'); 
        return;
    }


    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();
    
    formData.append('titulo', titulo);
    formData.append('mensaje', mensaje);
    formData.append('numero', numero);

    axios.post('/admin/control/enviarnoti/clienteunico', formData, {
    })
    .then((response) => {

        loadingOverlay().cancel(spinHandle);
       
        if(response.data.success == 1) {
            toastr.success("Enviado");
        }else if(response.data.success == 2){
            toastr.error('Cliente no activo');
        }else if(response.data.success == 3){
            toastr.error('ID es 0000, no enviado');
        }else if(response.data.success == 4){
            toastr.error('Cliente no encontrado');
        }
        else {
         toastr.error('Error desconocido');
        }
    })
    .catch((error) => {
        loadingOverlay().cancel(spinHandle);
        toastr.error('Error');
    }); 
  }


  // enviar notificacion al cliente 
  function enviar(){

    var titulo = document.getElementById('titulo').value;
    var mensaje = document.getElementById('mensaje').value;
    var clave = document.getElementById('clave').value;

    if(titulo === ''){
        toastr.error('Titulo es requerido'); 
        return;
    }

    if(mensaje === ''){
        toastr.error('Mensaje es requerido'); 
        return;
    }

    if(clave === ''){
        toastr.error('Clave es requerido'); 
        return;
    }

    var zonas = $('#zonas-nuevo').val(); 

    if(zonas.length == null || zonas.length == 0){
        toastr.error('Seleccionar mínimo 1 zona');
        return;
    }

    var spinHandle = loadingOverlay().activate();
    var formData = new FormData();
    
    for (var i = 0; i < zonas.length; i++) {
        formData.append('idzonas[]', zonas[i]);
    }

    formData.append('titulo', titulo);
    formData.append('mensaje', mensaje);
    formData.append('clave', clave);
    
    axios.post('/admin/control/enviarnoti/clienteszona', formData, {
    })
    .then((response) => {
        console.log(response);
        loadingOverlay().cancel(spinHandle);
       
        if(response.data.success == 1) {

            toastr.success("Enviado");
        }else if(response.data.success == 2){
            toastr.error('Clave incorrecta');
        }
        else {
         toastr.error('Error desconocido');
        }
    })
    .catch((error) => {
        loadingOverlay().cancel(spinHandle);
        toastr.error('Error');
    }); 

  }

 </script>

 
@stop