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
            <h1>Agregando a categoria: {{ $nombre }}</h1>
          </div>
          <button type="button" onclick="modalNegocio()" class="btn btn-success btn-sm">
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
<div class="modal fade" id="modalNuevo">
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
                                    <input type="text" maxlength="200" class="form-control" id="nombre-nuevo" placeholder="Nombre" required>
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>400 x 300 pixeles</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>
 
                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <textarea rows="2" maxlength="500" class="form-control" id="descripcion-nuevo" placeholder="Descripcion"></textarea>
                                </div>


                                <div class="form-group">
                                    <label>Precio producto</label>
                                    <input type="number" step="any" class="form-control" id="precio-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza nota</label>
                                    <br>
                                    <input type="checkbox" id="cbnota-nuevo">
                                </div>
 
                                <div class="form-group">
                                    <label>Nota (ejemp: si un producto necesita opciones a elegir)</label>
                                    <input type="text" maxlength="75" value="" class="form-control" id="nota-nuevo">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevoNegocio()">Guardar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal editar encargo -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Producto</h4>
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
                                    <label>Nombre</label>
                                    <input type="text" maxlength="200" class="form-control" id="nombre-editar" placeholder="Nombre" required>
                                </div>
                                
                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>400 x 300 pixeles</p>
                                    </div> 
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label id="txtImagen">Imagen producto</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-producto" src="{{ asset('images/imagendefecto.jpg') }}" width="40%">
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label>Descripcion</label>
                                    <textarea rows="2" maxlength="500" class="form-control" id="descripcion-editar" placeholder="Descripcion"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Precio producto</label>
                                    <input type="number" step="any" class="form-control" id="precio-editar">
                                </div>

                                <div class="form-group">
                                    <label>Utiliza nota</label>
                                    <br>
                                    <input type="checkbox" id="cbnota-editar">
                                </div>
 
                                <div class="form-group">
                                    <label>Nota (ejemp: si un producto necesita opciones a elegir)</label>
                                    <input type="text" maxlength="75" value="" class="form-control" id="nota-editar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarNegocio()">Guardar</button>
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
        id = {{ $id }};
        
        var ruta = "{{ url('/admin/encargos/tabla/negocios/categorias-productos') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

<script>

$(function() {
  $('.selectpicker').selectpicker();
});

    function modalNegocio(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalNuevo').modal('show');
    }

    function informacion(id){
        document.getElementById("formulario-editar").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/negocios/categorias/productos-informacion',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                if(response.data.success == 1){

                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.producto.id);
                    $('#nombre-editar').val(response.data.producto.nombre); 
                    $('#descripcion-editar').val(response.data.producto.descripcion); 
                    $('#precio-editar').val(response.data.producto.precio); 
                    $('#nota-editar').val(response.data.producto.nota);

                    if(response.data.producto.utiliza_nota == 1){
                        $('#cbnota-editar').prop('checked', true);
                    }else{
                        $('#cbnota-editar').prop('checked', false);
                    }

                    $('#img-producto').prop("src","{{ url('storage/productos') }}"+'/'+ response.data.producto.imagen);                   
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

    function editarNegocio(){
        var id = document.getElementById('id-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var descripcion = document.getElementById('descripcion-editar').value;
        var precio = document.getElementById('precio-editar').value;
        var imagen = document.getElementById('imagen-editar');
        var cbnota = document.getElementById('cbnota-editar').checked;
        var nota = document.getElementById('nota-editar').value;
     
        var cbnota_1 = 0;
        if(cbnota){
            cbnota_1 = 1;
        }

        var retorno = validarEditar(nombre, imagen, descripcion, precio);

        if(retorno){
            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id); 
            formData.append('nombre', nombre); 
            formData.append('imagen', imagen.files[0]);
            formData.append('descripcion', descripcion);
            formData.append('precio', precio);  
            formData.append('cbnota', cbnota_1); 
            formData.append('nota', nota); 
            
            axios.post('/admin/encargos/negocios/categorias/productos-editar', formData, {
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

    function validarEditar(nombre, imagen, descripcion, precio){

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }

        if(nombre.length > 200){
            toastr.error("200 caracter máximo nombre");
            return false;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }

        if(descripcion === ''){
            // no hacer nada
        }else{
            if(descripcion.length > 500){
                toastr.error("500 caracter máximo descripcion");
                return false;
            }
        }

        if(precio === ''){
            toastr.error("precio es requerido");
            return;
        }

        return true;
    }

    function respuestaEditar(response){
        
        if(response.data.success == 1){

            toastr.success('Guardado');
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/tabla/negocios/categorias-productos') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide'); 
        }
        else{
            toastr.error('Error desconocido');
        }
    }
    
    function nuevoNegocio(){
        var id = {{ $id }}; // id de la categoria
        var nombre = document.getElementById('nombre-nuevo').value;
        var imagen = document.getElementById('imagen-nuevo');
        var descripcion = document.getElementById('descripcion-nuevo').value;
        var precio = document.getElementById('precio-nuevo').value;
        var cbnota = document.getElementById('cbnota-nuevo').checked;
        var nota = document.getElementById('nota-nuevo').value;
                
        var retorno = validarNuevo(nombre, imagen, descripcion, precio);

        var cbnota_1 = 0;
        if(cbnota){
            cbnota_1 = 1;
        }

        if(retorno){

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();

            formData.append('id', id);
            formData.append('nombre', nombre); 
            formData.append('imagen', imagen.files[0]);
            formData.append('descripcion', descripcion);
            formData.append('precio', precio);  
            formData.append('cbnota', cbnota_1); 
            formData.append('nota', nota); 

            axios.post('/admin/encargos/negocios/categorias/productos-nuevo', formData, { 
                    })
                    .then((response) => {
                        loadingOverlay().cancel(spinHandle);
                        respuesta(response);                        
                    })
                    .catch((error) => {
                        loadingOverlay().cancel(spinHandle);
                        toastr.error('Error');
                    });

        }
    }

    function validarNuevo(nombre, imagen, descripcion, precio){

        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 200){
            toastr.error("200 caracter máximo nombre");
            return false;
        }

        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("Imagen es requerida");
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

        if(precio === ''){
            toastr.error("precio es requerido");
            return;
        }

        return true;
    }

    function respuesta(response){
        if(response.data.success == 1){
            toastr.success('Guardado'); 
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/tabla/negocios/categorias-productos') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalNuevo').modal('hide'); 
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    


  </script>
 


@stop