@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/bootstrap-select.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop  

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Ordenes Encargo para: {{ $nombre }}</h1>
          </div> 
      </div> 
    </section>
     
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de Encargos</h3>
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


<!-- modal info -->
<div class="modal fade" id="modalInfo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-info">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 

                                <div class="form-group">
                                    <label>Nombre Cliente</label>
                                    <input type="hidden" id="iddireccion">
                                    <input type="text" disabled class="form-control" id="nombrecliente">
                                </div>

                                <div class="form-group">
                                    <label>Zona identificador</label>
                                    <input type="text" disabled class="form-control" id="zonaidentificador">
                                </div> 

                                <div class="form-group">
                                    <label>Nombre Zona</label>
                                    <input type="text" disabled class="form-control" id="nombrezona">
                                </div>

                                <div class="form-group">
                                    <label>Direccion</label>
                                    <input type="text" disabled class="form-control" id="direccion">
                                </div>

                                <div class="form-group">
                                    <label>Numero de casa</label>
                                    <input type="text" disabled class="form-control" id="numerocasa">
                                </div>

                                <div class="form-group">
                                    <label>Punto de referencia</label>
                                    <input type="text" disabled class="form-control" id="puntoreferencia">
                                </div>

                                
                                <div class="form-group">
                                    <label>Telefono Registrado en la App</label>
                                    <input type="text" disabled class="form-control" id="telefonoreal">
                                </div>

                                <div class="form-group">
                                    <label>Latitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="latitud">
                                </div>

                                <div class="form-group">
                                    <label>Longitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="longitud">
                                </div>
                                <button type="button" onclick="mapa()" class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                        Mapa
                                </button>  

                                </br>
                                <div class="form-group">
                                </br>
                                    <label>Latitud real, tomada donde se registro</label>
                                    <input type="text" disabled class="form-control" id="latitudreal">
                                </div>

                                <div class="form-group">
                                    <label>Longitud real, tomada donde se registro</label>
                                    <input type="text" disabled class="form-control" id="longitudreal">
                                </div>

                                <button type="button" onclick="mapa2()" class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                        Mapa
                                </button> 
                                </br>
                                </br> 
                                <div class="form-group">
                                    <label>Dirección revisada</label>
                                    <input type="text" disabled class="form-control" id="revisado">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>          
        </div>        
    </div>      
</div>
 
<!-- modal cancelar -->
<div class="modal fade" id="modalCancelar">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <div class="modal-header">
                <h4 class="modal-title">Cancelar Encargo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-cancelar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                                
                                <div class="form-group">
                                    <input type="hidden" id="idcancelar">
                                </div>

                                <div class="form-group">
                                    <label>Mensaje</label>
                                    <input type="text" maxlength="200" class="form-control" id="mensaje" placeholder="Mensaje de cancelamiento">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="cancelar()">Cancelar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal motorista -->
<div class="modal fade" id="modalMotorista">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Motorista</h4>
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
                                    <label>Nombre</label>
                                    <input type="text" disabled class="form-control" id="nombre-motorista">
                                </div>

                                <div class="form-group">
                                    <label>Identificador</label>
                                    <input type="text" disabled class="form-control" id="identificador-motorista">
                                </div>

                                <div class="form-group">
                                    <label>Fecha agarro el encargo</label>
                                    <input type="text" disabled class="form-control" id="fecha-motorista">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal confirmar -->
<div class="modal fade" id="modalConfirmar">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <div class="modal-header">
                <h4 class="modal-title">Confirmar Encargo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-confirmar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                                
                                <div class="form-group">
                                    <input type="hidden" id="idconfirmar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="confirmar()">Confirmar</button>
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
    <script src="{{ asset('js/backend/bootstrap-select.min.js') }}" type="text/javascript"></script>

 <!-- incluir tabla --> 
  <script type="text/javascript">	 
    $(document).ready(function(){

        id = {{ $id }}; // id 
        
        var ruta = "{{ url('/admin/encargos/tabla/ordenes-lista') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>
 
<script>

    function modalInformacion(id){
        document.getElementById("formulario-info").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/ordenes/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                
                if(response.data.success == 1){

                    $('#modalInfo').modal('show');

                    $('#iddireccion').val(response.data.info.id); 
                    
                    $('#nombrecliente').val(response.data.info.nombre); 
                    $('#zonaidentificador').val(response.data.info.identificador); 
                    $('#nombrezona').val(response.data.info.nombrezona); 
                    $('#direccion').val(response.data.info.direccion); 
                    $('#numerocasa').val(response.data.info.numero_casa); 
                    $('#puntoreferencia').val(response.data.info.punto_referencia); 

                    $('#telefonoreal').val(response.data.telefono); 
                    $('#latitud').val(response.data.info.latitud); 
                    $('#longitud').val(response.data.info.longitud); 
                    $('#latitudreal').val(response.data.info.latitudreal); 
                    $('#longitudreal').val(response.data.info.longitudreal); 

                    if(response.data.info.revisado == 0){
                        $('#revisado').val("No revisado"); 
                    }else{
                        $('#revisado').val("Si revisada"); 
                    }
                
                }else{
                    toastr.error('Error de validacion'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    // latitud y longitud del puntero gps
    function mapa(){
        var id = document.getElementById('iddireccion').value;

        // comprobar que latitud y longitud no esten vacios
        var la = document.getElementById('latitud').value;
        var lo = document.getElementById('longitud').value;

        if(la === '' || lo === ''){
            toastr.error('Latitud o Longitud estan vacios'); 
            return;
        }

        window.location.href="{{ url('/admin/encargos/ordenes/direccion/mapa-gps') }}/"+id;
    }

    // latitud y longitud real donde guardo la direccion
    function mapa2(){
        var id = document.getElementById('iddireccion').value;

        var la = document.getElementById('latitudreal').value;
        var lo = document.getElementById('longitudreal').value;

        if(la === '' || lo === ''){
            toastr.error('Latitud o Longitud estan vacios'); 
            return;
        }

        window.location.href="{{ url('/admin/encargos/ordenes/direccion/mapa-gps2') }}/"+id;
    }

    function productos(id){

        window.location.href="{{ url('/admin/encargos/ordenes/productos-ver') }}/"+id;    
    }
    

    function modalCancelar(id){
        $('#idcancelar').val(id);
        $('#modalCancelar').modal('show');
    } 

    function cancelar(){
        var id = document.getElementById('idcancelar').value;
        var mensaje = document.getElementById('mensaje').value;

        if(mensaje.length > 200){
            toastr.error("200 caracter máximo para el mensaje");
            return false;
        }

        spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id); // id orden_encargo
        formData.append('mensaje', mensaje); // id orden_encargo

        axios.post('/admin/encargos/ordenes/cancelamiento', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);              
                    respuestaCancelar(response);  
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }

    function respuestaCancelar(response){
        if(response.data.success == 1){
            toastr.success('Cancelado');  
            id = {{ $id }}; // id del encargo
            
            var ruta = "{{ url('/admin/encargos/tabla/ordenes-lista') }}/"+id;
            $('#tablaDatatable').load(ruta);

            $('#modalCancelar').modal('hide');  
            document.getElementById("formulario-cancelar").reset();   
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    function modalMotorista(idorden){
        document.getElementById("formulario-motorista").reset();

        spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', idorden); // id orden_encargo

        axios.post('/admin/encargos/ordenes/ver-motorista-asignado', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);

                    if(response.data.success == 1){

                        $('#modalMotorista').modal('show');
                        var datos = response.data.orden; 

                        datos.forEach(function(value, index) {
                            $('#nombre-motorista').val(datos[index].nombre);
                            $('#identificador-motorista').val(datos[index].identificador);
                            $('#fecha-motorista').val(datos[index].fecha_agarrada);      
                        });           

                    }else{
                        toastr.error('Sin motorista'); 
                    }

        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }

    function modalConfirmar(id){
        $('#idconfirmar').val(id);
        $('#modalConfirmar').modal('show');
    }

    function confirmar(){
        var id = document.getElementById('idconfirmar').value;

        spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id); // id orden_encargo

        axios.post('/admin/encargos/ordenes/confirmar', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);              
                    respuestaConfirmar(response);
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }

    function respuestaConfirmar(response){
        if(response.data.success == 1){
            toastr.success('Confirmado, y notificacion enviada');  
            id = {{ $id }}; // id del encargo
            
            var ruta = "{{ url('/admin/encargos/tabla/ordenes-lista') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalConfirmar').modal('hide');   
            document.getElementById("formulario-confirmar").reset();              
        }else if(response.data.success == 2){
            toastr.success('Confirmado, pero notificacion no enviada');  
            id = {{ $id }}; // id del encargo
            
            var ruta = "{{ url('/admin/encargos/tabla/ordenes-lista') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalConfirmar').modal('hide');   
            document.getElementById("formulario-confirmar").reset();
            
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    
  </script>
 


@stop