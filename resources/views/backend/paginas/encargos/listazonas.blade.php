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
            <h1>Zonas de Encargo para: {{ $nombre }}</h1>
          </div>     
          <button type="button" onclick="modalNegocio()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva Zona
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de Categorias</h3>
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


<!-- modal borrar -->
<div class="modal fade" id="modalBorrar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar Registro</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-borrar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 

                                <div class="form-group">
                                    <input type="hidden" id="idborrar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-danger" onclick="borrar()">Borrar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal nuevo -->
<div class="modal fade" id="modalNuevo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nueva Zona</h4>
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
                                    <label style="color:#191818">Zona</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-zona">
                                                <option value="0" selected>Seleccionar</option>
                                            @foreach($zonas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach   
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label>Precio Envio</label>
                                    <input type="number" step="any" class="form-control" id="precio-nuevo">
                                </div>

                                <div class="form-group">
                                    <label>Ganancia Motorista</label>
                                    <input type="number" step="any" class="form-control" id="ganancia-nuevo">
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
                                    <input type="hidden" id="id-editar">
                                </div>

                                <div class="form-group">
                                    <label>Identificador Zona</label>
                                    <input type="text" disabled class="form-control" id="nombrezona">
                                </div>

                                <div class="form-group">
                                    <label>Precio Envio</label>
                                    <input type="number" step="any" class="form-control" id="precio-editar">
                                </div>

                                <div class="form-group">
                                    <label>Ganancia Motorista</label>
                                    <input type="number" step="any" class="form-control" id="ganancia-editar">
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
 
<script src="{{ asset('js/backend/jquery-ui-drag.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/backend/datatables-drag.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>
   



 <!-- incluir tabla -->  
  <script type="text/javascript">	 
    $(document).ready(function(){
        id = {{ $id }};
        
        var ruta = "{{ url('/admin/encargos/tabla/zonas-lista') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

<script>


    function modalNegocio(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalNuevo').modal('show');
    }

    function modalBorrar(id){
        $('#idborrar').val(id);
        $('#modalBorrar').modal('show');
    }

    function borrar(id){
        var id = document.getElementById('idborrar').value;

        spinHandle = loadingOverlay().activate();
        axios.post('/admin/encargos/zonas/zona-borrar',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                respuestaBorrar(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function respuestaBorrar(response){
        if(response.data.success == 1){
            toastr.success('Borrado'); 
            id = {{ $id }}; // id del encargo
            
            var ruta = "{{ url('/admin/encargos/tabla/zonas-lista') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalBorrar').modal('hide');     
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }


    function informacion(id){ 
        document.getElementById("formulario-editar").reset();
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/zonas/zona-informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                if(response.data.success == 1){

                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.zona.id);
                    $('#precio-editar').val(response.data.zona.precio_envio);
                    $('#ganancia-editar').val(response.data.zona.ganancia_motorista); 
                    $('#nombrezona').val(response.data.nombre);
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
        var precio = document.getElementById('precio-editar').value;
        var ganancia = document.getElementById('ganancia-editar').value;
         
        if(precio === ''){
            toastr.error("precio zona es requerido");
            return;
        }
        
        if(ganancia === ''){
            toastr.error("ganancia motorista es requerido");
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('precioenvio', precio);
        formData.append('ganancia', ganancia);
        
        axios.post('/admin/encargos/zonas/zona-editar', formData, {
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

    function respuestaEditar(response){
        
        if(response.data.success == 1){

            toastr.success('Guardado');
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/tabla/zonas-lista') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide'); 
        }
        else{
            toastr.error('Error desconocido');
        }
    }
    
    function nuevoNegocio(){
        var id = {{ $id }}; // id del encargo
        var idzona = document.getElementById('select-zona').value;
        var precio = document.getElementById('precio-nuevo').value;
        var ganancia = document.getElementById('ganancia-nuevo').value;

        if(idzona === '' || idzona === '0'){
            toastr.error("zona es requerido");
            return;
        }

        if(precio === ''){
            toastr.error("precio es requerido");
            return;
        }
        
        if(ganancia === ''){
            toastr.error("ganancia motorista es requerido");
            return;
        }
     
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);
        formData.append('idzona', idzona); 
        formData.append('precio', precio);
        formData.append('ganancia', ganancia);

        axios.post('/admin/encargos/zonas/zona-nuevo', formData, { 
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

    function respuesta(response){
        if(response.data.success == 1){
            toastr.error('Zona ya asignada');           
        } else if(response.data.success == 2){
            toastr.success('Guardado'); 
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/tabla/zonas-lista') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalNuevo').modal('hide'); 
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    
  </script> 
 


@stop 