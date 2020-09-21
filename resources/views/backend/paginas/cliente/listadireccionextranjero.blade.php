@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Dirección Extranjero</h1>
          </div>   
      </div>
    </section>
    
<!-- seccion frame -->
<section class="content">
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
        <h3 class="card-title">Tabla de direcciones</h3>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-info">
                    <div class="card-body">
                        <div class="col-md-12">

                          <div class="form-group">
                              <label style="color:#191818">Nombre</label>
                              <br>
                              <input type="hidden" id="id-editar">
                              <input type="text" maxlength="100" id="nombre" disabled  class="form-control"/></label>
                          </div>  

                          <div class="form-group">
                              <label style="color:#191818">Dirección</label>
                              <br>
                              <input type="text" maxlength="400" id="direccion" disabled  class="form-control"></label>
                          </div>

                          <div class="form-group">
                              <label style="color:#191818"># Casa</label>
                              <br>
                              <input type="text" maxlength="30" id="numero" disabled  class="form-control"></label>
                          </div>     
                          <div class="form-group">
                              <label style="color:#191818">Punto de referencia</label>
                              <br>                
                              <input type="text" maxlength="400" id="referencia" disabled  class="form-control"></label>
                          </div>
                      
                          <div class="form-group">
                              <label style="color:#191818">Latitud</label>
                              <br>
                              <input type="text" maxlength="50" id="latitud" disabled class="form-control"></label>
                          </div>  

                          <div class="form-group">
                              <label style="color:#191818">Longitud </label>
                              <br>
                              <input type="text" maxlength="50" id="longitud" disabled class="form-control"></label>
                          </div>  

                          <div class="form-group">
                              <label style="color:#191818">Latitud Real</label>
                              <br>
                              <input type="text" maxlength="50" id="latitud-real" disabled class="form-control"></label>
                          </div>  

                          <div class="form-group">
                              <label style="color:#191818">Longitud Real</label>
                              <br>
                              <input type="text" maxlength="50" id="longitud-real" disabled class="form-control"></label>
                          </div>  
 
                          <div class="form-group">
                                <label style="color:#191818">Estado (Direccion extranjero)</label>
                                <br>
                                <div>
                                    <select class="form-control" id="select-estado">
                                        <option value="0" selected>No Verificado</option>
                                        <option value="1">Verificado</option>
                                        <option value="2">Rechazada</option>
                                    </select>
                                </div>
                          </div> 

                          
                          <div class="form-group">
                              <label style="color:#191818">Mensaje rechazo de direccion</label>
                              <br>
                              <input type="text" maxlength="200" id="mensaje-rechazo" class="form-control"></label>
                          </div> 

                          <div class="form-group">
                              <label style="color:#191818">Cargo Envio</label>
                              <br>
                              <input type="number" step="0.01" id="cargo-envio" class="form-control"></label>
                          </div>  
 
                          <div class="form-group">
                              <label style="color:#191818">Ganancia Motorista</label>
                              <br>
                              <input type="number" step="0.01" id="ganancia-motorista" class="form-control"></label>
                          </div> 

                        </div> 
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarDireccion()">Guardar</button>
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

        var id = {{ $id }}
        var ruta = "{{ URL::to('admin/extranjero/tabla/todas/direcciones') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>  
 
  <script> 


    // aqui veremos todas las direcciones del usuario
    function informacion(id){

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        
        axios.post('/admin/informacion/direccion/extranjero', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);
 
            if(response.data.success == 1){            
                $('#modal-info').modal('show');
                $('#id-editar').val(response.data.direccion.id);
                $('#nombre').val(response.data.direccion.nombre);
                $('#direccion').val(response.data.direccion.direccion);
                $('#numero').val(response.data.direccion.numero_casa);
                $('#referencia').val(response.data.direccion.punto_referencia);        

                $('#latitud').val(response.data.direccion.latitud);
                $('#longitud').val(response.data.direccion.longitud);
               
                $('#latitud-real').val(response.data.direccion.latitud_real);
                $('#longitud-real').val(response.data.direccion.longitud_real);

                if(response.data.direccion.estado == 0){
                    $('#select-estado option')[0].selected = true;
                }else  if(response.data.direccion.estado == 1){
                    $('#select-estado option')[1].selected = true;
                }else  if(response.data.direccion.estado == 2){
                    $('#select-estado option')[2].selected = true;
                }
 
                $('#cargo-envio').val(response.data.direccion.precio_envio);
                $('#ganancia-motorista').val(response.data.direccion.ganancia_motorista);

                $('#mensaje-rechazo').val(response.data.direccion.mensaje_rechazo);


            }else{
                toastr.error('Direccion no encontrada'); 
            } 
             
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }


    function editarDireccion(){

        var id = document.getElementById('id-editar').value;

        // verificando la direccion del extranjero
        var cargo = document.getElementById('cargo-envio').value;
        var ganamotorista = document.getElementById('ganancia-motorista').value;
        var mensaje = document.getElementById('mensaje-rechazo').value;
        var estado = document.getElementById('select-estado').value;

        if(estado == 1){
            if(mensaje === ''){
                toastr.error("Mensaje es requerido");
                return;
            }
        }

        if(cargo === ''){
            toastr.error("cargo envio es requerido");
            return;
        }

        if(ganamotorista === ''){
            toastr.error("ganancia motorista es requerido");
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);

        formData.append('estado', estado);
        formData.append('ganmotorista', ganamotorista);
        formData.append('cargoenvio', cargo);
        formData.append('mensaje', mensaje);

        axios.post('/admin/cliente/actualizar/extranjero/direccion', formData, { 
                })
                .then((response) => { 
                    loadingOverlay().cancel(spinHandle);
                    
                    if(response.data.success == 1){
                        toastr.success('Actualizado'); 
                        $('#modal-info').modal('hide');
                    }
                    else{
                        toastr.error('Error de validacion'); 
                    }

                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
    }
   

  </script>
 


@stop