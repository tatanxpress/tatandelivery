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
            <h1>Encargos Finalizados</h1>
          </div>  
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de Encargos Finalizados</h3>
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






<!-- modal editar encargo -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Encargo</h4>
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
                                    <label>Fecha Registrado</label>                                    
                                    <input type="hidden" id="idencargo-editar">
                                    <input type="text" class="form-control" id="fecharegistrado" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Identificador unico</label>
                                    <input type="text" maxlength="100" class="form-control" id="identificador-editar" placeholder="Identificador unico" required>
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="200" class="form-control" id="nombre-editar" placeholder="Nombre" required>
                                </div>

                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea maxlength="500" rows="2" class="form-control" id="descripcion-editar" placeholder="Descripción"></textarea>
                                </div>
 
                                <div class="form-group">
                                    <label>Fecha inicia</label>
                                    <input type="date" class="form-control" id="fechainicio-editar">
                                </div>

                                <div class="form-group">
                                    <label>Fecha finaliza</label>
                                    <input type="datetime-local" class="form-control" id="fechafin-editar">
                                </div>  

                                <div class="form-group">
                                    <label>Fecha entrega</label>
                                    <input type="datetime-local" class="form-control" id="fechaentrega-editar">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Tipo Vista</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="tipovista-editar">
                                            <option value="0" selected>Vista Vertical</option>
                                            <option value="1">Vista Horizontal</option>
                                        </select>
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <label>Activo</label>
                                    <br>
                                    <input type="checkbox" id="activo">
                                </div>   

                                <div class="form-group">
                                    <label>Ya puede ver este encargo el cliente</label>
                                    <br>
                                    <input type="checkbox" id="vistacliente">
                                </div>   

                                <div class="form-group">
                                    <label>Permiso motorista para que vea este encargo asignado</label>
                                    <br>
                                    <input type="checkbox" id="checkmotorista">
                                </div>  

                                <div class="form-group">
                                    <label>Permiso a propietario para que vea este encargo asignado</label>
                                    <br>
                                    <input type="checkbox" id="checkpropietario">
                                </div>  
                              
                                <div class="form-group">
                                    <label id="txtImagen">Imagen producto</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-producto" src="{{ asset('images/imagendefecto.jpg') }}" width="40%">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 600x300 pixeles</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarEncargo()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- asignar servicio -->
<div class="modal fade" id="modalServicio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignar Servicio?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-servicio">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 

                                <div class="form-group">
                                    <input type="hidden" id="idencargo-servicio">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Servicio</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-servicio">
                                            @foreach($servicios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label>Si se activa, se borrara la asignacion</label>
                                    <br>
                                    <input type="checkbox" id="checkservicio">
                                </div>  

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="guardarAsignacion()">Guardar</button>
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
        var ruta = "{{ URL::to('admin/encargos/tabla/lista-finalizo') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

<script>


    function informacion(id){
        $('#id').val(id);
        $('#modal').modal('show');
    }

    function modalEncargo(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalNuevo').modal('show');
    }

    function informacion(id){
        document.getElementById("formulario-editar").reset();

        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/ver-informacion',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                if(response.data.success == 1){

                    $('#modalEditar').modal('show');

                    $.each(response.data.encargo, function( key, val ){  
                       
                        $('#idencargo-editar').val(val.id);
                        $('#fecharegistrado').val(val.ingreso);
                        $('#identificador-editar').val(val.identificador); 
                        
                        $('#nombre-editar').val(val.nombre);
                        $('#descripcion-editar').val(val.descripcion);
                                          
                        $('#fechainicio-editar').val(val.fecha_inicia); 
                        $('#fechafin-editar').val(val.fecha_finaliza);
                        $('#fechaentrega-editar').val(val.fecha_entrega); 

                        if(val.tipo_vista == 1){
                            $('#tipovista-editar option')[1].selected = true;
                        }

                        if(val.vista_cliente == 1){
                            $('#vistacliente').prop('checked', true);
                        }

                        if(val.permiso_motorista == 1){
                            $('#checkmotorista').prop('checked', true);
                        }

                        if(val.visible_propietario == 1){
                            $('#checkpropietario').prop('checked', true);
                        }

                        if(val.activo == 1){
                            $('#activo').prop('checked', true);
                        }

                        $('#img-producto').prop("src","{{ url('storage/listaservicios') }}"+'/'+ val.imagen);
                    });  

                  

                }
                else{
                    toastr.error('Error de validacion'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function editarEncargo(){

        var id = document.getElementById('idencargo-editar').value;
        var identificador = document.getElementById('identificador-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;
        var fechainicio = document.getElementById('fechainicio-editar').value;
        var fechafin = document.getElementById('fechafin-editar').value;
        var fechaentrega = document.getElementById('fechaentrega-editar').value;
        var activo = document.getElementById('activo').checked;
        var imagen = document.getElementById('imagen-editar');
        var tipovista = document.getElementById('tipovista-editar').value;
        var vistacliente = document.getElementById('vistacliente').checked;
        var checkmoto = document.getElementById('checkmotorista').checked;
        var checkpropi = document.getElementById('checkpropietario').checked;

        var activo_1 = 0;
        var vistacliente_1 = 0;
        var checkmoto_1 = 0;
        var checkpropi_1 = 0;


        if(activo){
            activo_1 = 1;
        }

        if(vistacliente){
            vistacliente_1 = 1;
        }

        if(checkmoto){
            checkmoto_1 = 1;
        }

        if(checkpropi){
            checkpropi_1 = 1;
        }


        var retorno = validarEncargo(identificador, nombre, descripcion, fechainicio, fechafin, imagen, fechaentrega);

        if(retorno){

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('identificador', identificador);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('fechainicio', fechainicio);
            formData.append('fechafin', fechafin);
            formData.append('fechaentrega', fechaentrega); 
            formData.append('activo', activo_1);
            formData.append('imagen', imagen.files[0]);
            formData.append('tipovista', tipovista);
            formData.append('vistacliente', vistacliente_1);
            formData.append('permisomotorista', checkmoto_1);
            formData.append('visiblepropietario', checkpropi_1);
                        
            axios.post('/admin/encargos/editar-encargos', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

                if(response.data.success == 1){
                    toastr.error('Identificador ya existe');
                }else if(response.data.success == 2){

                    toastr.success('Guardado');
                    var ruta = "{{ url('/admin/encargos/tabla/lista-finalizo') }}";
                    $('#tablaDatatable').load(ruta);
                    $('#modalEditar').modal('hide'); 
                }
                else{
                    toastr.error('Error desconocido');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });

        }       
    }

    function validarEncargo(identificador, nombre, descripcion, fechainicio, fechafin, imagen, fechaentrega){

        if(identificador === ''){
            toastr.error("identificador es requerido");
            return;
        }
        
        if(identificador.length > 100){
            toastr.error("100 caracter máximo identificador");
            return false;
        }

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 200){
            toastr.error("200 caracter máximo nombre");
            return false;
        }
        
        if(descripcion === ''){
            // no hacer nada
        }else{
            if(descripcion.length > 500){
                toastr.error("500 caracter máximo descripcion");
                return false;
            }
        }
        
        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        if(fechainicio === ''){
            toastr.error("fecha inicio es requerido");
            return;
        }
        

        if(fechafin === ''){
            toastr.error("fecha fin es requerido");
            return;
        }

        if(fechaentrega === ''){
            toastr.error("fecha entrega es requerido");
            return;
        }

        return true;
    }
    
    
  
    function zonas(id){
        window.location.href="{{ url('/admin/encargos/zonas-lista') }}/"+id;
    }

    function lista(id){
        window.location.href="{{ url('/admin/encargos/lista-categorias') }}/"+id;
    }

    function ordenes(id){
        window.location.href="{{ url('/admin/encargos/ordenes-lista') }}/"+id;
    }

    function asignarMotorista(id){
        window.location.href="{{ url('/admin/encargos/asignar/motorista-encargo') }}/"+id;
    }


    function modalServicio(id){
        $('#idencargo-servicio').val(id);
        $('#modalServicio').modal('show');
    } 
    
    function guardarAsignacion(){
        var idencargo = document.getElementById('idencargo-servicio').value;
        var servicio = document.getElementById('select-servicio').value;
        var checkBorrar = document.getElementById('checkservicio').checked;
 
        var checkBorrar_1 = 0;
        if(checkBorrar){
            checkBorrar_1 = 1;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('idencargo', idencargo);
        formData.append('idservicio', servicio);
        formData.append('checkborrar', checkBorrar_1);
                    
        axios.post('/admin/encargos/asignar-servicio', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            respu(response);
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }

    function respu(response){
        
        if(response.data.success == 1){
                toastr.success('Asignacion borrada');
            }else if(response.data.success == 2){
                toastr.success('Editado correctamente');
            }else if(response.data.success == 3){
                toastr.error('Ya existe un registro');
            }else if(response.data.success == 4){
                toastr.success('Guardado');
            }
            else if(response.data.success == 6){
                toastr.error('No existe registro a borrar');
            }
            else{
                toastr.error('Error al Guardar');
            }

            document.getElementById("formulario-servicio").reset();

            var ruta = "{{ url('/admin/encargos/tabla/lista-finalizo') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalServicio').modal('hide'); 
    }



  </script>
 


@stop