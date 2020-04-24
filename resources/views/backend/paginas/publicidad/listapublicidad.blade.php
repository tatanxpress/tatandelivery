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
            <h1>Publicidad</h1>
          </div>    
          <button type="button" onclick="modalOpciones()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva publicidad o promoción
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de publicidad activa</h3>
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

    <!-- opciones de modales -->
<div class="modal fade" id="modalOpcion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Opciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-opciones">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                            <input type="hidden" id="id-editar">
                                <div class="form-group">
                                    <button class="form-control" type="button" onclick="modalPromocion()" class="btn btn-info btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                            Promocion a servicio
                                    </button>
                                </div>
                                <div class="form-group">
                                    <button class="form-control" type="button" onclick="modalPublicidad()" class="btn btn-info btn-sm">
                                        <i class="fas fa-pencil-alt"></i>
                                            Publicidad a cliente
                                    </button>
                                </div>                     
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>        
    </div>      
</div>

<!-- modal nuevo promocion-->
<div class="modal fade" id="modalAgregarPromocion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva promocion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-promocion">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>Identificador unico</label>
                                    <input type="text" maxlength="50" class="form-control" id="identificador-nuevo" placeholder="Identificador unico">
                                </div>

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-nuevo" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Logo</label>
                                        <p>Tamaño recomendado de:</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagenlogo-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de:</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control" id="fechainicio-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Fecha finalizar</label>
                                    <input type="date" class="form-control" id="fechafin-nuevo">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevapromocion()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal nuevo publicidad-->
<div class="modal fade" id="modalAgregarPublicidad">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva publicidad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-publicidad">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Identificador unico</label>
                                    <input type="text" maxlength="50" class="form-control" id="identificador-pu" placeholder="Identificador unico">
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-pu" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-pu" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Logo</label>
                                        <p>Tamaño recomendado de: 100x100</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagenlogo-pu" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 600x300</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-pu" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Utiliza facebook</label>
                                    <input type="checkbox" id="cbfacebook-pu">
                                </div>
                                <div class="form-group">
                                    <label>URL Facebook</label>
                                    <input type="text" maxlength="300" class="form-control" id="urlfacebook-pu" placeholder="url facebook">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza youtube</label>
                                    <input type="checkbox" id="cbyoutube-pu">
                                </div>
                                <div class="form-group">
                                    <label>URL Youtube</label>
                                    <input type="text" maxlength="300" class="form-control" id="urlyoutube-pu" placeholder="url youtube">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza instagram</label>
                                    <input type="checkbox" id="cbinstagram-pu">
                                </div>
                                <div class="form-group">
                                    <label>URL Instagram</label>
                                    <input type="text" maxlength="300" class="form-control" id="urlinstagram-pu" placeholder="url instagram">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza titulo</label>
                                    <input type="checkbox" id="cbtitulo-pu">
                                </div>
                                <div class="form-group">
                                    <label>Titulo</label>
                                    <input type="text" maxlength="300" class="form-control" id="titulo-pu" placeholder="Titulo inferior">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza descripcion</label>
                                    <input type="checkbox" id="cbdescripcion-pu">
                                </div>
                                <div class="form-group">
                                    <label>descripcion titulo</label>
                                    <textarea type="text" maxlength="800" class="form-control" id="titulodescripcion-pu" placeholder="Descripcion de la publicidad"> </textarea>
                                </div>

                                <div class="form-group">
                                    <label>Utiliza telefono</label>
                                    <input type="checkbox" id="cbtelefono-pu">
                                </div>

                                <div class="form-group">
                                    <label>Telefono</label>
                                    <textarea type="text" maxlength="100" class="form-control" id="telefono-pu" placeholder="Telefono"> </textarea>
                                </div>

                                <div class="form-group">
                                    <label>Utiliza visitanos</label>
                                    <input type="checkbox" id="cbvisitanos-pu">
                                </div>

                                <div class="form-group">
                                    <label>Texto visitanos</label>
                                    <input type="text" maxlength="50" class="form-control" id="visitanos-pu" placeholder="texto visitanos">
                                </div>

                                <div class="form-group">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control" id="fechainicio-pu">
                                </div>
                                <div class="form-group">
                                    <label>Fecha finalizar</label>
                                    <input type="date" class="form-control" id="fechafin-pu">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevapublicidad()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar promocion-->
<div class="modal fade" id="modalEditarPromocion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar promocion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editarpromocion">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="hidden" id="idpromo-editar">
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-editar" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Logo</label>
                                        <p>Tamaño recomendado de:</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagenlogo-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label id="txtImagen">Logo promocion</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-logo" width="40%">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de:</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label id="txtImagen">Imagen promocion</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-imagen"  width="40%">
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <label>Activo</label>
                                    <input type="checkbox" id="activo-editar">
                                </div>

                                <div class="form-group">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control" id="fechainicio-editar">
                                </div>
                                <div class="form-group">
                                    <label>Fecha finalizar</label>
                                    <input type="date" class="form-control" id="fechafin-editar">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarpromocion()">Actualizar promoción</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar publicidad-->
<div class="modal fade" id="modalEditarPublicidad">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar publicidad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-editarpublicidad">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-e" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" maxlength="100" class="form-control" id="descripcion-e" placeholder="Descripción">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Logo</label>
                                        <p>Tamaño recomendado de: 100x100</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagenlogo-e" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label id="txtImagen">Logo promocion</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-logo-e" width="40%">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 600x300</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-e" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label id="txtImagen">Imagen promocion</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-imagen-e"  width="40%">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Utiliza facebook</label>
                                    <input type="checkbox" id="cbfacebook-e">
                                </div>
                                <div class="form-group">
                                    <label>URL Facebook</label>
                                    <input type="text" maxlength="300" class="form-control" id="urlfacebook-e" placeholder="url facebook">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza youtube</label>
                                    <input type="checkbox" id="cbyoutube-e">
                                </div>
                                <div class="form-group">
                                    <label>URL Youtube</label>
                                    <input type="text" maxlength="300" class="form-control" id="urlyoutube-e" placeholder="url youtube">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza instagram</label>
                                    <input type="checkbox" id="cbinstagram-e">
                                </div>
                                <div class="form-group">
                                    <label>URL Instagram</label>
                                    <input type="text" maxlength="300" class="form-control" id="urlinstagram-e" placeholder="url instagram">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza titulo</label>
                                    <input type="checkbox" id="cbtitulo-e">
                                </div>
                                <div class="form-group">
                                    <label>Titulo</label>
                                    <input type="text" maxlength="300" class="form-control" id="titulo-e" placeholder="Titulo inferior">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza descripcion</label>
                                    <input type="checkbox" id="cbdescripcion-e">
                                </div>
                                <div class="form-group">
                                    <label>descripcion titulo</label>
                                    <textarea type="text" maxlength="800" class="form-control" id="titulodescripcion-e" placeholder="Descripcion de la publicidad"> </textarea>
                                </div>

                                <div class="form-group">
                                    <label>Utiliza telefono</label>
                                    <input type="checkbox" id="cbtelefono-e">
                                </div>

                                <div class="form-group">
                                    <label>Telefono</label>
                                    <textarea type="text" maxlength="100" class="form-control" id="telefono-e" placeholder="Telefono"> </textarea>
                                </div>

                                <div class="form-group">
                                    <label>Utiliza visitanos</label>
                                    <input type="checkbox" id="cbvisitanos-e">
                                </div>

                                <div class="form-group">
                                    <label>Texto visitanos</label>
                                    <input type="text" maxlength="50" class="form-control" id="visitanos-e" placeholder="texto visitanos">
                                </div>


                                <div class="form-group">
                                    <label>Activo</label>
                                    <input type="checkbox" id="activo-e">
                                </div>


                                <div class="form-group">
                                    <label>Fecha inicio</label>
                                    <input type="date" class="form-control" id="fechainicio-e">
                                </div>
                                <div class="form-group">
                                    <label>Fecha finalizar</label>
                                    <input type="date" class="form-control" id="fechafin-e">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarpublicidad()">Guardar</button>
            </div>
        </div>
    </div>
</div>


 <!-- elegir servicio a quien meterle el producto -->
<div class="modal fade" id="modalServicio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Servicio para agregar producto</h4>
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
                                    <input type="hidden" id="id-promocion">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select id="selectservicio" class="form-control selectpicker" data-live-search="true" required>   
                                            @foreach($zonas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="buscarServicio()">Buscar</button>
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
        var ruta = "{{ URL::to('admin/publicidad/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script>

$(function() {
  $('.selectpicker').selectpicker();
});
     
    function modalOpciones(){
        $('#modalOpcion').modal('show');
    }

    function modalPromocion(){
        document.getElementById("formulario-promocion").reset();
        $('#modalAgregarPromocion').modal('show');
    }

    function modalPublicidad(){
        $('#modalAgregarPublicidad').modal('show');
    }

    function nuevapromocion(){
        var identificador = document.getElementById('identificador-nuevo').value;
        var nombre = document.getElementById('nombre-nuevo').value;
        var descripcion = document.getElementById('descripcion-nuevo').value;
        var logo = document.getElementById('imagenlogo-nuevo'); 
        var imagen = document.getElementById('imagen-nuevo'); 
        var fechainicio = document.getElementById('fechainicio-nuevo').value;
        var fechafin = document.getElementById('fechafin-nuevo').value;

        var retorno = validarNuevo(nombre, descripcion, logo, imagen, fechainicio, fechafin, identificador);

        if(retorno){

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
           
            formData.append('nombre', nombre);
            formData.append('identificador', identificador);
            formData.append('descripcion', descripcion);
            formData.append('logo', logo.files[0]);
            formData.append('imagen', imagen.files[0]);
            formData.append('fechainicio', fechainicio);
            formData.append('fechafin', fechafin);

            axios.post('/admin/publicidad/nuevo-promocion', formData, { 
                    })
                    .then((response) => {
                        loadingOverlay().cancel(spinHandle);
                        respuestaNuevo(response);
                    })
                    .catch((error) => {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error('Error');
                    });
        }
    }

    function respuestaNuevo(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Promocion agregada');           
           
            var ruta = "{{ url('/admin/publicidad/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregarPromocion').modal('hide');     
        } else if(response.data.success == 2){
            toastr.error('No se pudo agregar');
        } else if(response.data.success == 4){
            toastr.error('identificador ya existe');
        } 
        else {
            toastr.error('Error desconocido');
        }
    } 

    function validarNuevo(nombre, descripcion, logo, imagen, fechainicio, fechafin, identificador){
        
        if(identificador === ''){
            toastr.error("identificador es requerido");
            return;
        }
        
        if(identificador.length > 50){
            toastr.error("50 caracter máximo identificador");
            return false;
        }
        
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }

        if(descripcion === ''){
            toastr.error("descripcion es requerido");
            return;
        }
        
        if(descripcion.length > 100){
            toastr.error("100 caracter máximo descripcion");
            return false;
        }

        if(fechainicio === ''){
            toastr.error("fecha inicio es requerido");
            return;
        }
        

        if(fechafin === ''){
            toastr.error("fecha fin es requerido");
            return;
        }
        
        if(logo.files && logo.files[0]){ // si trae imagen
            if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("logo es requerido");
            return false;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("imagen es requerido");
            return false;
        }

        return true;
    }

    function nuevapublicidad(){
        var nombre = document.getElementById('nombre-pu').value;
        var identificador = document.getElementById('identificador-pu').value;
        var descripcion = document.getElementById('descripcion-pu').value;
        var logo = document.getElementById('imagenlogo-pu'); 
        var imagen = document.getElementById('imagen-pu'); 
        var cbfacebook = document.getElementById('cbfacebook-pu').checked;
        var cbyoutube = document.getElementById('cbyoutube-pu').checked;
        var cbinstagram = document.getElementById('cbinstagram-pu').checked;
        var cbdescripcion = document.getElementById('cbdescripcion-pu').checked;
        var cbtelefono = document.getElementById('cbtelefono-pu').checked;
        var cbvisitanos = document.getElementById('cbvisitanos-pu').checked;
        var cbtitulo = document.getElementById('cbtitulo-pu').checked;
        var urlfacebook = document.getElementById('urlfacebook-pu').value;
        var urlyoutube = document.getElementById('urlyoutube-pu').value;
        var urlinstagram = document.getElementById('urlinstagram-pu').value;

        var titulo = document.getElementById('titulo-pu').value;
        var titulodescripcion = document.getElementById('titulodescripcion-pu').value;
        var telefono = document.getElementById('telefono-pu').value;
        var visitanos = document.getElementById('visitanos-pu').value;

        var fechainicio = document.getElementById('fechainicio-pu').value;
        var fechafin = document.getElementById('fechafin-pu').value;

        var retorno = validarNuevoPu(nombre, descripcion, logo, imagen, fechainicio, fechafin, identificador);

        if(retorno){

            var cbfacebook_1 = 0;
            var cbyoutube_1 = 0;
            var cbinstagram_1 = 0;
            var cbdescripcion_1 = 0;
            var cbtelefono_1 = 0;
            var cbvisitanos_1 = 0;
            var cbtitulo_1 = 0;

            if(cbfacebook){
                cbfacebook_1 = 1;

                if(urlfacebook === ''){
                    toastr.error('url facebook es requerida si marco opcion');
                    return;
                }
            }else{

                if(urlfacebook.length > 0){
                    // no tocar texto
                }else{
                    urlfacebook = "-";
                }
            }


            if(cbyoutube){
                cbyoutube_1 = 1;
                if(urlyoutube === ''){
                    toastr.error('url youtube es requerida si marco opcion');
                    return;
                }
            }else{
                if(urlyoutube.length > 0){
                    // no tocar texto
                }else{
                    urlyoutube = "-";
                }
            }

            if(cbinstagram){
                cbinstagram_1 = 1;
                if(urlinstagram === ''){
                    toastr.error('url instagram es requerida si marco opcion');
                    return;
                }
            }else{
                if(urlinstagram.length > 0){
                    // no tocar texto
                }else{
                    urlinstagram = "-";
                }
            }

            if(cbdescripcion){
                cbdescripcion_1 = 1;
                if(titulodescripcion === ''){
                    toastr.error('titulo descripcion es requerida si marco opcion');
                    return;
                }
            }else{
                if(titulodescripcion.length > 0){
                    // no tocar texto
                }else{
                    titulodescripcion = "-";
                }
            } 

            if(cbtelefono){
                cbtelefono_1 = 1;
                if(telefono === ''){
                    toastr.error('telefono es requerida si marco opcion');
                    return;
                }
            }else{
                if(telefono.length > 0){
                    // no tocar texto
                }else{
                    telefono = "-";
                }              
            }

            if(cbvisitanos){
                cbvisitanos_1 = 1;

                if(visitanos === ''){
                    toastr.error('visitanos es requerida si marco opcion');
                    return;
                }
            }else{
                if(visitanos.length > 0){
                    // no tocar texto
                }else{
                    visitanos = "-";
                } 
            }

            if(cbtitulo){
                cbtitulo_1 = 1;
                
                if(titulo === ''){
                    toastr.error('titulo es requerida si marco opcion');
                    return;
                }
            }else{
                if(titulo.length > 0){
                    // no tocar texto
                }else{
                    titulo = "-";
                }
            }

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
           
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('logo', logo.files[0]);
            formData.append('imagen', imagen.files[0]);

            formData.append('cbfacebook', cbfacebook_1);
            formData.append('cbyoutube', cbyoutube_1);
            formData.append('cbinstagram', cbinstagram_1);
            formData.append('cbdescripcion', cbdescripcion_1);
            formData.append('cbtelefono', cbtelefono_1);
            formData.append('cbvisitanos', cbvisitanos_1);
            formData.append('cbtitulo', cbtitulo_1);
            formData.append('urlfacebook', urlfacebook);
            formData.append('urlyoutube', urlyoutube);
            formData.append('urlinstagram', urlinstagram);
            formData.append('titulo', titulo);
            formData.append('titulodescripcion', titulodescripcion);
            formData.append('telefono', telefono);
            formData.append('visitanos', visitanos);
            formData.append('identificador', identificador);

            formData.append('fechainicio', fechainicio);
            formData.append('fechafin', fechafin);

            axios.post('/admin/publicidad/nuevo-publicidad', formData, {
                    })
                    .then((response) => {
                        loadingOverlay().cancel(spinHandle);
                        console.log(response);
                        respuestapublicidadNuevo(response);
                    })
                    .catch((error) => {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error('Error');
                    });
        }
    }
     
    function respuestapublicidadNuevo(response){
      if (response.data.success == 0) {
            toastr.error('Validacion incorrecta'); 
        } else if (response.data.success == 1) {
            toastr.success('Publicidad agregada');           
           
            var ruta = "{{ url('/admin/publicidad/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregarPublicidad').modal('hide');
        } else if(response.data.success == 2){
            toastr.error('No se pudo agregar');
        } else if(response.data.success == 4){
            toastr.error('El identificador ya existe'); 
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

    function validarNuevoPu(nombre, descripcion, logo, imagen, fechainicio, fechafin, identificador){
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }

        if(identificador === ''){
            toastr.error("identificador es requerido");
            return;
        }
        
        if(identificador.length > 50){
            toastr.error("50 caracter máximo identificador");
            return false;
        }

        if(descripcion === ''){
            toastr.error("descripcion es requerido");
            return;
        }
        
        if(descripcion.length > 100){
            toastr.error("100 caracter máximo descripcion");
            return false;
        }

        if(fechainicio === ''){
            toastr.error("fecha inicio es requerido");
            return;
        }        

        if(fechafin === ''){
            toastr.error("fecha fin es requerido");
            return;
        }
        
        if(logo.files && logo.files[0]){ // si trae imagen
            if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("logo es requerido");
            return false;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("imagen es requerido");
            return false;
        }

        return true;
    }
    
    function validarNuevoPu2(nombre, descripcion, logo, imagen, fechainicio, fechafin){
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
         
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }

        if(descripcion === ''){
            toastr.error("descripcion es requerido");
            return;
        }
        
        if(descripcion.length > 100){
            toastr.error("100 caracter máximo descripcion");
            return false;
        }

        if(fechainicio === ''){
            toastr.error("fecha inicio es requerido");
            return;
        }        

        if(fechafin === ''){
            toastr.error("fecha fin es requerido");
            return;
        }
        
        if(logo.files && logo.files[0]){ // si trae imagen
            if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        return true;
    }

    function informacion(id){
        document.getElementById("formulario-publicidad").reset();
        document.getElementById("formulario-editarpublicidad").reset();

        // resetear campo imagen
        document.getElementById("imagenlogo-editar").value = "";
        document.getElementById("imagen-editar").value = "";
        document.getElementById("imagenlogo-e").value = "";
        document.getElementById("imagen-e").value = "";


        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/publicidad/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                if(response.data.success == 1){
              
                    // promocion
                    if(response.data.publicidad.tipo_publicidad == 1){                     
                        
                        $('#modalEditarPromocion').modal('show');
                        $('#idpromo-editar').val(response.data.publicidad.id);
                        $('#nombre-editar').val(response.data.publicidad.nombre);
                        $('#descripcion-editar').val(response.data.publicidad.descripcion);
                        $("#activo-editar").prop("checked", true);

                        $('#img-imagen').prop("src","{{ url('storage/listaservicios') }}"+'/'+ response.data.publicidad.imagen);
                        $('#img-logo').prop("src","{{ url('storage/listaservicios') }}"+'/'+ response.data.publicidad.logo);

                        $('#fechainicio-editar').val(response.data.publicidad.fecha_inicio);
                        $('#fechafin-editar').val(response.data.publicidad.fecha_fin);
                        
                    }else{
                        // publicidad 
                        $('#modalEditarPublicidad').modal('show');
                        $('#idpromo-editar').val(response.data.publicidad.id);
                        $('#nombre-e').val(response.data.publicidad.nombre);
                        $('#descripcion-e').val(response.data.publicidad.descripcion);

                        $('#img-imagen-e').prop("src","{{ url('storage/listaservicios') }}"+'/'+ response.data.publicidad.imagen);
                        $('#img-logo-e').prop("src","{{ url('storage/listaservicios') }}"+'/'+ response.data.publicidad.logo);

                        $('#urlfacebook-e').val(response.data.publicidad.url_facebook);
                        $('#urlyoutube-e').val(response.data.publicidad.url_youtube);
                        $('#urlinstagram-e').val(response.data.publicidad.url_instagram);
                        $('#titulo-e').val(response.data.publicidad.titulo);
                        $('#telefono-e').val(response.data.publicidad.telefono);
                        $('#titulodescripcion-e').val(response.data.publicidad.titulo_descripcion);
                        $('#visitanos-e').val(response.data.publicidad.visitanos);

                        if(response.data.publicidad.utiliza_facebook == 0){
                            $("#cbfacebook-e").prop("checked", false);
                        }else{
                            $("#cbfacebook-e").prop("checked", true);
                        }

                        if(response.data.publicidad.utiliza_youtube == 0){
                            $("#cbyoutube-e").prop("checked", false);
                        }else{
                            $("#cbyoutube-e").prop("checked", true);
                        }

                        if(response.data.publicidad.utiliza_instagram == 0){
                            $("#cbinstagram-e").prop("checked", false);
                        }else{
                            $("#cbinstagram-e").prop("checked", true);
                        }

                        if(response.data.publicidad.utiliza_titulo == 0){
                            $("#cbtitulo-e").prop("checked", false);
                        }else{
                            $("#cbtitulo-e").prop("checked", true);
                        }

                        if(response.data.publicidad.utiliza_descripcion == 0){
                            $("#cbdescripcion-e").prop("checked", false);
                        }else{
                            $("#cbdescripcion-e").prop("checked", true);
                        }

                        if(response.data.publicidad.utiliza_telefono == 0){
                            $("#cbtelefono-e").prop("checked", false);
                        }else{
                            $("#cbtelefono-e").prop("checked", true);
                        }

                        if(response.data.publicidad.utiliza_visitanos == 0){
                            $("#cbvisitanos-e").prop("checked", false);
                        }else{
                            $("#cbvisitanos-e").prop("checked", true);
                        }

                        $("#activo-e").prop("checked", true);

                        $('#fechainicio-e').val(response.data.publicidad.fecha_inicio);
                        $('#fechafin-e').val(response.data.publicidad.fecha_fin);

                    }

                }else{
                    toastr.error('publicidad o promo no encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
    
    function editarpromocion(){
      var id = document.getElementById('idpromo-editar').value;
      var nombre = document.getElementById('nombre-editar').value;
      var descripcion = document.getElementById('descripcion-editar').value;
      var logo = document.getElementById('imagenlogo-editar');
      var imagen = document.getElementById('imagen-editar');
      var fechainicio = document.getElementById('fechainicio-editar').value;
      var fechafin = document.getElementById('fechafin-editar').value;
      var activo = document.getElementById('activo-editar').checked;

      var retorno = validarEditarPromo(nombre, descripcion, fechainicio, fechafin, imagen, logo);

        if(retorno){

            var activo_1 = 0;
            if(activo){
                activo_1 = 1;
            } 

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('imagen', imagen.files[0]);
            formData.append('logo', logo.files[0]);
            formData.append('fechainicio', fechainicio);
            formData.append('fechafin', fechafin);
            formData.append('activo', activo_1);

            axios.post('/admin/publicidad/editar-promo', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                
                respuestaEditarPromo(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
        }     
    } 

    function validarEditarPromo(nombre, descripcion, fechainicio, fechafin, imagen, logo){
 
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
      
        if(nombre.length > 100){
            toastr.error("100 caracter máximo nombre");
            return false;
        }

        if(descripcion === ''){
            toastr.error("descripcion es requerido");
            return;
        }
      
        if(descripcion.length > 100){
            toastr.error("100 caracter máximo descripcion");
            return false;
        }

        if(fechainicio === ''){
            toastr.error("fecha inicio es requerido");
            return;
        }

        if(fechafin === ''){
            toastr.error("fecha fin es requerido");
            return;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        if(logo.files && logo.files[0]){ // si trae imagen
            if (!logo.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        return true;
    }
    
    function respuestaEditarPromo(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.error('Error al subir imagen');
        } else if (response.data.success == 2) {
            toastr.success('Promocion actualizada');
            
            var ruta = "{{ URL::to('admin/publicidad/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditarPromocion').modal('hide');
        }
        else {
            toastr.error('Error desconocido');
        }
    }

    function editarpublicidad(){
        var id = document.getElementById('idpromo-editar').value;
        var nombre = document.getElementById('nombre-e').value;
        var descripcion = document.getElementById('descripcion-e').value;
        var logo = document.getElementById('imagenlogo-e'); 
        var imagen = document.getElementById('imagen-e'); 
        var cbfacebook = document.getElementById('cbfacebook-e').checked;
        var cbyoutube = document.getElementById('cbyoutube-e').checked;
        var cbinstagram = document.getElementById('cbinstagram-e').checked;
        var cbdescripcion = document.getElementById('cbdescripcion-e').checked;
        var cbtelefono = document.getElementById('cbtelefono-e').checked;
        var cbvisitanos = document.getElementById('cbvisitanos-e').checked;
        var cbtitulo = document.getElementById('cbtitulo-e').checked;
        var urlfacebook = document.getElementById('urlfacebook-e').value;
        var urlyoutube = document.getElementById('urlyoutube-e').value;
        var urlinstagram = document.getElementById('urlinstagram-e').value;
        var activo = document.getElementById('activo-e').checked;

        var titulo = document.getElementById('titulo-e').value;
        var titulodescripcion = document.getElementById('titulodescripcion-e').value;
        var telefono = document.getElementById('telefono-e').value;
        var visitanos = document.getElementById('visitanos-e').value;

        var fechainicio = document.getElementById('fechainicio-e').value;
        var fechafin = document.getElementById('fechafin-e').value;

        var retorno = validarNuevoPu2(nombre, descripcion, logo, imagen, fechainicio, fechafin);

        if(retorno){

            var cbfacebook_1 = 0;
            var cbyoutube_1 = 0;
            var cbinstagram_1 = 0;
            var cbdescripcion_1 = 0;
            var cbtelefono_1 = 0;
            var cbvisitanos_1 = 0;
            var cbtitulo_1 = 0;
            var activo_1 = 0;

            if(activo){
                activo_1 = 1;
            }

            if(cbfacebook){
                cbfacebook_1 = 1;

                if(urlfacebook === ''){
                    toastr.error('url facebook es requerida si marco opcion');
                    return;
                }
            }else{

                if(urlfacebook.length > 0){
                    // no tocar texto
                }else{
                    urlfacebook = "-";
                }
            }


            if(cbyoutube){
                cbyoutube_1 = 1;
                if(urlyoutube === ''){
                    toastr.error('url youtube es requerida si marco opcion');
                    return;
                }
            }else{
                if(urlyoutube.length > 0){
                    // no tocar texto
                }else{
                    urlyoutube = "-";
                }
            }

            if(cbinstagram){
                cbinstagram_1 = 1;
                if(urlinstagram === ''){
                    toastr.error('url instagram es requerida si marco opcion');
                    return;
                }
            }else{
                if(urlinstagram.length > 0){
                    // no tocar texto
                }else{
                    urlinstagram = "-";
                }
            }

            if(cbdescripcion){
                cbdescripcion_1 = 1;
                if(titulodescripcion === ''){
                    toastr.error('titulo descripcion es requerida si marco opcion');
                    return;
                }
            }else{
                if(titulodescripcion.length > 0){
                    // no tocar texto
                }else{
                    titulodescripcion = "-";
                }
            }

            if(cbtelefono){
                cbtelefono_1 = 1;
                if(telefono === ''){
                    toastr.error('telefono es requerida si marco opcion');
                    return;
                }
            }else{
                if(telefono.length > 0){
                    // no tocar texto
                }else{
                    telefono = "-";
                }              
            }

            if(cbvisitanos){
                cbvisitanos_1 = 1;

                if(visitanos === ''){
                    toastr.error('visitanos es requerida si marco opcion');
                    return;
                }
            }else{
                if(visitanos.length > 0){
                    // no tocar texto
                }else{
                    visitanos = "-";
                } 
            }

            if(cbtitulo){
                cbtitulo_1 = 1;
                
                if(titulo === ''){
                    toastr.error('titulo es requerida si marco opcion');
                    return;
                }
            }else{
                if(titulo.length > 0){
                    // no tocar texto
                }else{
                    titulo = "-";
                }
            }

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('logo', logo.files[0]);
            formData.append('imagen', imagen.files[0]);

            formData.append('cbfacebook', cbfacebook_1);
            formData.append('cbyoutube', cbyoutube_1);
            formData.append('cbinstagram', cbinstagram_1);
            formData.append('cbdescripcion', cbdescripcion_1);
            formData.append('cbtelefono', cbtelefono_1);
            formData.append('cbvisitanos', cbvisitanos_1);
            formData.append('cbtitulo', cbtitulo_1);
            formData.append('urlfacebook', urlfacebook);
            formData.append('urlyoutube', urlyoutube);
            formData.append('urlinstagram', urlinstagram);
            formData.append('titulo', titulo);
            formData.append('titulodescripcion', titulodescripcion);
            formData.append('telefono', telefono);
            formData.append('visitanos', visitanos);
            formData.append('activo', activo_1);

            formData.append('fechainicio', fechainicio);
            formData.append('fechafin', fechafin);

            axios.post('/admin/publicidad/editar-publi', formData, {
                    })
                    .then((response) => {
                        loadingOverlay().cancel(spinHandle);
                        console.log(response);
                        respuestaEditarPubli(response);
                    })
                    .catch((error) => {
                        loadingOverlay().cancel(spinHandle);
                        
                        toastr.error('Error');
                    });
        }
    }

    function respuestaEditarPubli(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.error('Error al subir imagen');
        } else if (response.data.success == 2) {
            toastr.success('Publicidad actualizada');
            
            var ruta = "{{ URL::to('admin/publicidad/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditarPublicidad').modal('hide');
        }
        else {
            toastr.error('Error desconocido');
        }
    }

    // para agregar productos a promocion
    function revision(id){
        spinHandle = loadingOverlay().activate();
        
        $('#id-promocion').val(id);

        axios.post('/admin/productopromocion/revision',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                verificacion(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function verificacion(response){
        if (response.data.success == 1) { 

            // ya tienen producto
            var idservicio = response.data.idservicio;
            buscarServicio2(idservicio);

        } else if (response.data.success == 2) {
            // buscar servicio
            $('#modalServicio').modal('show');
        }
        else { 
            toastr.error('Error desconocido');
        } 
    }

    // vamos a buscar el servicio y ver el producto a agregar a la promocion
    function buscarServicio(){
        var idservicio = document.getElementById('selectservicio').value;
        var idpro = document.getElementById('id-promocion').value;
        window.location.href="{{ url('/admin/productopromocion') }}/"+idservicio+"/"+idpro;
    }

    // como ya hay productos, solo obtengo el id del servicio, de algun producto
    function buscarServicio2(idservicio){
        var idpro = document.getElementById('id-promocion').value;
        window.location.href="{{ url('/admin/productopromocion') }}/"+idservicio+"/"+idpro;
    }

  </script>
 


@stop