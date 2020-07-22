@extends('backend.menus.superior')
 
 @section('content-admin-css')
     <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
     <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
     <link href="{{ asset('css/backend/estiloToggle.css') }}" type="text/css" rel="stylesheet" /> 
 @stop  
 
<style>


.info {background-color: #2196F3;} /* Blue */
</style>
 
 <section class="content-header">
       <div class="container-fluid">
           <div class="col-sm-12">
             <h1>Buscador de Clientes</h1>
           </div>  
           <button type="button" onclick="modalBuscar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar por número
          </button>  
         
       </div>
     </section>
     
   <!-- seccion frame -->
   <section class="content">
     <div class="container-fluid">
       <div class="card card-primary">
           <div class="card-header">
             <h3 class="card-title">Tabla de registros</h3>
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

<!-- modal buscar -->
<div class="modal fade" id="modalBuscar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscador</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-buscar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Teléfono cliente</label>
                                    <input type="text" maxlength="20" class="form-control" id="tel-cliente">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="buscarCliente()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div> 



<!-- modal informacion -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Información usuario</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">        
            <div class="form-group">
                <label style="color:#191818">Código dispositivo</label>
                <br>
                <input id="id-editar" type="hidden">
                <input id="codigo" disabled class="form-control">
            </div>
            
            <div class="form-group" style="margin-left:20px">
                <label>Disponibilidad</label><br>
                <label class="switch" style="margin-top:10px">
                    <input type="checkbox" id="toggle-activo">
                    <div class="slider round">
                        <span class="on">Activar</span>
                        <span class="off">Desactivar</span>
                    </div>
                </label>
            </div>
 
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" maxlength="100" class="form-control" id="nombre-editar">
            </div>   

            <div class="form-group">
                <label>Correo</label>
                <input type="text" maxlength="100" class="form-control" id="correo-editar">
            </div>   

            <div class="form-group">
                <label>Código recuperación</label>
                <input type="text" maxlength="10" class="form-control" id="codigo-editar">
            </div>  
            

        </div>
          
        <div class="modal-footer float-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
        </div>
      </div>      
    </div>        
  </div>


<!-- modal historial -->
<div class="modal fade" id="modalHistorial">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Historial Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-historial">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 

                                <div class="form-group">
                                    <label>Total Ordenes</label>
                                    <input type="text" disabled class="form-control" id="t-ordenes">
                                </div>
                               
                                <div class="form-group">
                                    <label>Total Completadas</label>
                                    <input type="text" disabled class="form-control" id="t-completadas">
                                </div>
                                
                                <div class="form-group">
                                    <label>Total Canceladas por propietario</label>
                                    <input type="text" disabled class="form-control" id="t-cancelada-propi">
                                </div>
                                
                                <div class="form-group">
                                    <label>Total Canceladas por cliente</label>
                                    <input type="text" disabled class="form-control" id="t-canceladas-cliente">
                                </div>

                                <div class="form-group">
                                    <label>Total Gastado Sub total (Solo completadas)</label>
                                    <input type="text" disabled class="form-control" id="t-gastado">
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

 @extends('backend.menus.inferior')
 
 @section('content-admin-js')
 
     <script src="{{ asset('js/backend/jquery.dataTables.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/backend/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
     <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>
 
   <script>

    function modalBuscar(){
        document.getElementById("formulario-buscar").reset();
        $('#modalBuscar').modal('show');
    }
  
    function buscarCliente(){
        var tel = document.getElementById('tel-cliente').value;

        if(tel === ''){
            toastr.error('Número es requerido'); 
            return;
        }

        var ruta = "{{ url('/admin/cliente/info-buscar-cliente') }}/"+tel;
        $('#tablaDatatable').load(ruta); 
 
        $('#modalBuscar').modal('hide');  
        
    }     

    // informacion del cliente
    function informacion(id){
        spinHandle = loadingOverlay().activate();
        
        axios.post('/admin/cliente/informacion',{
        'id': id  
            }) 
            .then((response) => {	
                loadingOverlay().cancel(spinHandle);
               if(response.data.success == 1){
                
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.cliente.id);
                    $('#codigo').val(response.data.cliente.device_id);

                    $('#nombre-editar').val(response.data.cliente.name);
                    $('#correo-editar').val(response.data.cliente.email);
                    $('#codigo-editar').val(response.data.cliente.codigo_correo);

                    if(response.data.cliente.activo == 0){
                        $("#toggle-activo").prop("checked", false);
                    }else{
                        $("#toggle-activo").prop("checked", true);
                    }                   
                }else{ 
                    toastr.error('Usuario no encontrado'); 
                }         
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    // editar cliente
    function editar(){
        var id = document.getElementById('id-editar').value;

        var nombre = document.getElementById('nombre-editar').value;
        var correo = document.getElementById('correo-editar').value;
        var codigo = document.getElementById('codigo-editar').value;

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        } 

        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }

        if(correo === ''){
            toastr.error("correo es requerido");
            return;
        }

        if(correo.length > 100){
            toastr.error("100 caracter máximo correo");
            return false;
        }

        if(codigo === ''){
            toastr.error("codigo recuperacion es requerido");
            return;
        }

        if(codigo.length > 10){
            toastr.error("10 caracter máximo codigo");
            return false;
        }


        var toggleactivo = document.getElementById('toggle-activo').checked;

        var toggle = 0;
        if(toggleactivo){
            toggle = 1;
        }

        let me = this;
        let formData = new FormData();
        formData.append('id', id);
        formData.append('toggle', toggle);
        formData.append('nombre', nombre); 
        formData.append('correo', correo);
        formData.append('codigo', codigo);
       
        var spinHandle = loadingOverlay().activate();
 
        axios.post('/admin/cliente/editar', formData, {
        })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);                    
                verificarEditado(response);        
            })
            .catch((error) => {
                toastr.error('Error del servidor');
                loadingOverlay().cancel(spinHandle);
        });
    }

    // verificar editado
    function verificarEditado(response){
        if (response.data.success == 1) {
            toastr.success('Cliente actualizado');
            var ruta = "{{ URL::to('admin/cliente/info-buscar-cliente') }}/"+0;
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');
        } else if (response.data.success == 2) {
            toastr.error('Error al actualizar');
        } else {
            toastr.error('Error desconocido');
        }
    }


    function verDirecciones(id){
      window.location.href="{{ url('/admin/cliente/direcciones') }}/"+id;
    }


   // ver historial del cliente
   function verHistorial(id){
        document.getElementById("formulario-historial").reset();
        spinHandle = loadingOverlay().activate();
        
        axios.post('/admin/cliente/historial-cliente',{
        'id': id  
            }) 
            .then((response) => {	
                loadingOverlay().cancel(spinHandle);

                if(response.data.success == 1){

                    $('#t-ordenes').val(response.data.total);
                    $('#t-completadas').val(response.data.completadas);
                    $('#t-cancelada-propi').val(response.data.cancelopropi);
                    $('#t-canceladas-cliente').val(response.data.cancelocliente);
                    $('#t-gastado').val(response.data.gastado);
                    
                    $('#modalHistorial').modal('show');
                }else{  
                    toastr.error('Usuario no encontrado'); 
                }         
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
   
   </script>
  
 
 
 @stop