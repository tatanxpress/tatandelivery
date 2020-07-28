@extends('backend.menus.superior')
 
@section('content-admin-css')
<link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
@stop

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Productos de {{ $nombre }}</h1>
          </div>    
          <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Producto
          </button>    
      </div> 
    </section>
    
  <!-- seccion frame -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">  
                <div class="card-header">
                    <h3 class="card-title">Tabla de Productos</h3>
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

<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                              
                              <div class="form-group">
                                  <label>Nombre</label>
                                  <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre producto">
                              </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 250 x -</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                
                                <div class="form-group">
                                    <label>Utiliza imagen</label>
                                    <br>
                                    <input type="checkbox" id="cbimagen-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <input type="text" maxlength="500" class="form-control" id="descripcion-nuevo" placeholder="Descripcion producto">
                                </div>

                                <div class="form-group">
                                    <label>Precio producto</label>
                                    <input type="number" step="any" class="form-control" id="precio-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Unidades</label>
                                    <input type="number" value="0" min="0" max="100" class="form-control" id="unidades-nuevo">
                                </div>

                                <div class="form-group">
                                        <label>Disponibilidad</label> <br>
                                        <input type="checkbox" id="cbdisponibilidad-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Activo</label>
                                    <br>
                                    <input type="checkbox" id="cbactivo-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza cantidad</label>
                                    <br>
                                    <input type="checkbox" id="cbcantidad-nuevo">
                                </div>

                               
                                <div class="form-group">
                                    <label>Limite por orden</label>
                                    <br>
                                    <input type="checkbox" id="cblimite-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Cantidad por orden</label>
                                    <input type="number" value="0" min="0" max="100" class="form-control" id="cantidadorden-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza nota</label>
                                    <br>
                                    <input type="checkbox" id="cbnota-nuevo">
                                </div>
 
                                <div class="form-group">
                                    <label>Nota (ejemp: si un producto necesita opciones a elegir)</label>
                                    <input type="text" maxlength="50" value="" class="form-control" id="nota-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar producto</h4>
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
                                    <input type="hidden" id="id-editar">
                                </div>
                                <div class="form-group">
                                    <label>Fecha ingreso</label>
                                    <input type="text" disabled class="form-control" id="fecha-editar">
                                </div>
                                <div class="form-group">
                                    <label style="color:#191818">Categoria</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectcategoria-editar">                                        
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre producto">
                                </div>
                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <input type="text" maxlength="500" class="form-control" id="descripcion-editar" placeholder="Descripcion producto">
                                </div>
                                <div class="form-group">
                                    <label id="txtImagen">Imagen producto</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-producto" src="{{ asset('images/imagendefecto.jpg') }}" width="40%">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Precio producto</label>
                                    <input type="number" step="any" class="form-control" id="precio-editar">
                                </div>

                                <div class="form-group">
                                    <label>Unidades</label>
                                    <input type="number" min="0" max="100" class="form-control" id="unidades-editar">
                                </div>

                                <div class="form-group">
                                    <label>Disponibilidad</label>
                                    <br>
                                    <input type="checkbox" id="cbdisponibilidad-editar">
                                </div>
                                <div class="form-group">
                                    <label>Activo</label>
                                    <br>
                                    <input type="checkbox" id="cbactivo-editar">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza cantidad</label>
                                    <br>
                                    <input type="checkbox" id="cbcantidad-editar">
                                </div>
                              
                                <div class="form-group">
                                    <label>Es promocion (Solo aparecera en promociones)</label>
                                    <br>
                                    <input type="checkbox" id="cbpromocion-editar">
                                </div>
                                <div class="form-group">
                                    <label>Utiliza limite orden?</label> <br>
                                    <input type="checkbox" id="cblimite-editar">
                                </div>
                                <div class="form-group">
                                    <label>Cantidad por orden</label>
                                    <input type="number" min="1" max="100" class="form-control" id="cantidadorden-editar">
                                </div>
                                <div class="form-group">
                                    <label>Utiliza nota (ejem: cuando un producto necesita una descripcion)</label>
                                    <br>
                                    <input type="checkbox" id="cbutilizanota-editar">
                                </div>
                                <div class="form-group">
                                    <label>Nota</label>
                                    <input type="text" maxlength="50" class="form-control" id="nota-editar">
                                </div>
                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de: 250 x -</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Utiliza imagen?</label>
                                    <br>
                                    <input type="checkbox" id="cbimagen-editar">
                                </div>


                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

@extends('backend.menus.inferior')

@section('content-admin-js')

    <script src="{{ asset('js/backend/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/backend/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>
    

 <!-- incluir tabla --> 
  <script type="text/javascript">	 
    $(document).ready(function(){ 
      id = {{ $id }};
      var ruta = "{{ url('/admin/productos/tablas') }}/"+id;
      $('#tablaDatatable').load(ruta);
    });
 </script>

  <script>
     
    // modal nuevo
    function abrirModalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    function nuevo(){
    
        // id categoria
        id = {{ $id }};
        var nombre = document.getElementById('nombre-nuevo').value;
        var imagen = document.getElementById('imagen-nuevo');
        var descripcion = document.getElementById('descripcion-nuevo').value;
        var precio = document.getElementById('precio-nuevo').value;
        var unidades = document.getElementById('unidades-nuevo').value;
        var cbdisponibilidad = document.getElementById('cbdisponibilidad-nuevo').checked;
        var cbactivo = document.getElementById('cbactivo-nuevo').checked;
        var cbcantidad = document.getElementById('cbcantidad-nuevo').checked;
       

        var cblimite = document.getElementById('cblimite-nuevo').checked;
        var cantidadorden = document.getElementById('cantidadorden-nuevo').value;
        var cbnota = document.getElementById('cbnota-nuevo').checked;
        var nota = document.getElementById('nota-nuevo').value;
        var cbimagen = document.getElementById('cbimagen-nuevo').checked;

        
        var retorno = validarNuevo(nombre, imagen, descripcion, precio, unidades, cantidadorden);

        if(retorno){

            var cbdisponibilidad_1 = 0;
            var cbactivo_1 = 0;
            var cbcantidad_1 = 0;
            var cblimite_1 = 0;
            var cbnota_1 = 0;
            var cbimagen_1 = 0;
         

          
            if(cbdisponibilidad){
                cbdisponibilidad_1 = 1;
            }
            if(cbactivo){
                cbactivo_1 = 1;
            }
            if(cbcantidad){
                cbcantidad_1 = 1;
            }
            if(cblimite){
                cblimite_1 = 1;
            }
            if(cbnota){
                cbnota_1 = 1;
            }

            if(cbimagen){
                cbimagen_1 = 1;
            }
            
            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
 
            formData.append('idcategoria', id); 
            formData.append('nombre', nombre);
            formData.append('imagen', imagen.files[0]);
            formData.append('descripcion', descripcion);
            formData.append('precio', precio);
            formData.append('unidades', unidades);
            formData.append('cbdisponibilidad', cbdisponibilidad_1);
            formData.append('cbactivo', cbactivo_1);
            formData.append('cbcantidad', cbcantidad_1);
            formData.append('cbpromocion', 0); // por defecto no se utilizara
            formData.append('cblimite', cblimite_1);
            formData.append('cantidadorden', cantidadorden);
            formData.append('cbnota', cbnota_1);
            formData.append('nota', nota);
            formData.append('cbimagen', cbimagen_1);

                axios.post('/admin/productos/nuevo', formData, {
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

    function validarNuevo(nombre, imagen, descripcion, precio, unidades, cantidadorden){
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error('Imagen es requerida');
            return;
        }

        if(descripcion === ''){
            toastr.error("descripcion es requerido");
            return;
        }
        
        if(descripcion.length > 500){
            toastr.error("500 caracter máximo descripcion");
            return false;
        }

        if(precio === ''){
            toastr.error("precio es requerido");
            return;
        }

        if(unidades === ''){
            toastr.error("unidades es requerido");
            return; 
        }

        if(cantidadorden === ''){
            toastr.error("cantidad orden es requerido"); // limite de pedir un producto por orden
            return;
        }

        return true;
    }

    function respuestaNuevo(response){

        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        }  else if(response.data.success == 1){
            toastr.error('imagen no valida');
        } else if(response.data.success == 2){
            toastr.success('Actualizado');
            var id = {{ $id }};
            var ruta = "{{ url('/admin/productos/tablas') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');    
        } else if(response.data.success == 3){
            toastr.error('error al guardar');
        } else if(response.data.success == 4){
            toastr.error('error al guardar imagen');
        } else{
            toastr.error('Error desconocido');
        }
    } 
    
    function informacion(id){
        document.getElementById("formulario-nuevo").reset();
        document.getElementById("formulario-editar").reset();
  
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/productos/informacion',{
        'id': id
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);

                if(response.data.success == 1){
                    $('#modalEditar').modal('show');

                    var tipo = document.getElementById("selectcategoria-editar");
                    // limpiar select
                    document.getElementById("selectcategoria-editar").options.length = 0;
                
                    $.each(response.data.categoria, function( key, val ){  
                       if(response.data.producto.servicios_tipo_id == val.id){
                            $('#selectcategoria-editar').append('<option value="' +val.id +'" selected="selected">'+val.nombre+'</option>');
                       }else{
                            $('#selectcategoria-editar').append('<option value="' +val.id +'">'+val.nombre+'</option>');
                       }
                    });
                    $('#id-editar').val(response.data.producto.id);
                    $('#nombre-editar').val(response.data.producto.nombre);
                    $('#descripcion-editar').val(response.data.producto.descripcion);
                    
                    if(response.data.producto.utiliza_imagen == 1){
                        $('#img-producto').prop("src","{{ url('storage/productos') }}"+'/'+ response.data.producto.imagen);
                    }else{

                        if(response.data.producto.imagen.length > 0){
                           
                            $('#img-producto').prop("src","{{ url('storage/productos') }}"+'/'+ response.data.producto.imagen);
                        }else{
                           
                            
                            $('#img-producto').prop("src","{{ asset('images/imagendefecto.jpg') }}");

                        }
                    }

                    $('#precio-editar').val(response.data.producto.precio);
                    $('#unidades-editar').val(response.data.producto.unidades);
                    $('#fecha-editar').val(response.data.producto.fecha);

                    if(response.data.producto.disponibilidad == 0){
                        $("#cbdisponibilidad-editar").prop("checked", false);
                    }else{
                        $("#cbdisponibilidad-editar").prop("checked", true);
                    }

                    if(response.data.producto.activo == 0){
                        $("#cbactivo-editar").prop("checked", false);
                    }else{
                        $("#cbactivo-editar").prop("checked", true);
                    }

                    if(response.data.producto.utiliza_cantidad == 0){
                        $("#cbcantidad-editar").prop("checked", false);
                    }else{
                        $("#cbcantidad-editar").prop("checked", true);
                    }

                    if(response.data.producto.utiliza_imagen == 0){
                        $("#cbimagen-editar").prop("checked", false);
                    }else{
                        $("#cbimagen-editar").prop("checked", true);
                    }

                    if(response.data.producto.limite_orden == 0){
                        $("#cblimite-editar").prop("checked", false);
                    }else{
                        $("#cblimite-editar").prop("checked", true);
                    }

                    if(response.data.producto.utiliza_nota == 0){
                        $("#cbutilizanota-editar").prop("checked", false);
                    }else{
                        $("#cbutilizanota-editar").prop("checked", true);
                    }

                    $('#cantidadorden-editar').val(response.data.producto.cantidad_por_orden);

                    $('#nota-editar').val(response.data.producto.nota);

                }else{
                    toastr.error('Categoria no encontrada'); 
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
    
    function editar(){
        var id = document.getElementById('id-editar').value; // producto id
        var selectcategoria = document.getElementById('selectcategoria-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;
        var precio = document.getElementById('precio-editar').value;
        var unidades = document.getElementById('unidades-editar').value;
        var cbdisponibilidad = document.getElementById('cbdisponibilidad-editar').checked;
        var cbactivo = document.getElementById('cbactivo-editar').checked;
        var cbcantidad = document.getElementById('cbcantidad-editar').checked;
       
        var cblimite = document.getElementById('cblimite-editar').checked;
        var cbutilizanota = document.getElementById('cbutilizanota-editar').checked;
        var nota = document.getElementById('nota-editar').value;
        var cbimagen = document.getElementById('cbimagen-editar').checked;
        var cantidadorden = document.getElementById('cantidadorden-editar').value;
        var imagen = document.getElementById('imagen-editar');
        
        var retorno = validacionEditar(nombre, descripcion, precio, unidades, cantidadorden, imagen);

        if(retorno){

            var cbdisponibilidad_1 = 0;
            var cbactivo_1 = 0;
            var cbcantidad_1 = 0;
            
            var cblimite_1 = 0;
            var cbutilizanota_1 = 0;
            var cbimagen_1 = 0;

            if(cbdisponibilidad){
                cbdisponibilidad_1 = 1;
            }
            if(cbactivo){
                cbactivo_1 = 1;
            }
            if(cbcantidad){
                cbcantidad_1 = 1;
            }
           
            if(cblimite){
                cblimite_1 = 1;
            }
            if(cbutilizanota){
                cbutilizanota_1 = 1;
            }
            if(cbimagen){
                cbimagen_1 = 1;
            }

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);

            formData.append('nombre', nombre);
            formData.append('descripcion', descripcion);
            formData.append('selectcategoria', selectcategoria);
            formData.append('precio', precio);
            formData.append('unidades', unidades);
            formData.append('cbdisponibilidad', cbdisponibilidad_1);
            formData.append('cbactivo', cbactivo_1);
            formData.append('cbcantidad', cbcantidad_1);
            formData.append('cbpromocion', 0); // por defecto no se utilizara
            formData.append('cblimite', cblimite_1);
            formData.append('cbutilizanota', cbutilizanota_1);
            formData.append('cbimagen', cbimagen_1);
            formData.append('nota', nota);
            formData.append('cantidadorden', cantidadorden);
            formData.append('imagen', imagen.files[0]);

            axios.post('/admin/productos/editar', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                respuestaEditar(response); 
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
        }
    } 
     
    function respuestaEditar(response){
      if (response.data.success == 0) {
          toastr.error('Validacion incorrecta');
      } else if (response.data.success == 1) {
          toastr.error('error validacion de imagen'); 
      } else if (response.data.success == 2) {
            toastr.success('Categoria actualizada');
            id = {{ $id }};
            var ruta = "{{ url('/admin/productos/tablas') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');     
      } else if (response.data.success == 3) {
            toastr.error('Error subir imagen');
      } else{
            toastr.error('Error desconocido');
        }
    }

    function validacionEditar(nombre, descripcion, precio, unidades, cantidadorden, imagen){
        if (nombre === '') {
            toastr.error("Nombre es requerido");
            return false;
        }

        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if (descripcion === '') {
            toastr.error("descripcion es requerido");
            return false;
        }

        if(descripcion.length > 500){
            toastr.error("500 caracter máximo descripcion");
            return false;
        }

        if (precio === '') {
            toastr.error("precio es requerido");
            return false;
        }

        if (cantidadorden === '') {
            toastr.error("cantidad orden es requerido");
            return false;
        }

        if (unidades === '') {
            toastr.error("unidades es requerido");
            return false;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        return true;
    }

  </script>



@stop