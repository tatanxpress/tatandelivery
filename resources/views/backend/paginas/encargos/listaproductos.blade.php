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
            <h1>Agregando productos para encargo: {{ $nombre }} </h1>
          </div>     
          <button type="button" onclick="modalNegocio()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Agregar Productos
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
                <h4 class="modal-title">Nueva Producto</h4>
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
                                    <label style="color:#191818">Producto</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-producto">
                                                <option value="0" selected>Seleccionar</option>
                                            @foreach($pro as $item)                                                
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
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
                <button type="button" class="btn btn-primary" onclick="guardar()">Guardar</button>
            </div>          
        </div>
    </div>      
</div>

<div class="modal fade" id="modalActivar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activar producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-activar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 

                                <div class="form-group">
                                    <input type="hidden" id="idactivar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="activar()">Activar</button>
            </div>           
        </div>        
    </div>      
</div>

<div class="modal fade" id="modalDesactivar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Desactivar producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-desactivar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 

                                <div class="form-group">
                                    <input type="hidden" id="iddesactivar">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" onclick="desactivar()">Desactivar</button>
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
                                <label style="color:#191818">Tipo Servicio</label>
                                <br>
                                <div>
                                    <select class="form-control" id="select-zonaeditar">                                     
                                    </select>
                                </div> 
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
         
        var ruta = "{{ url('/admin/encargos/lista/ver-productos-tabla') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
     
 </script> 

<script>

    function modalNegocio(){
        $('#modalNuevo').modal('show');
    }
    
     
    function respuestaBorrar(response){
        if(response.data.success == 1){
            toastr.success('Borrado'); 
            id = {{ $id }}; // id del encargo
            
            var ruta = "{{ url('/admin/encargos/lista/ver-productos-tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalBorrar').modal('hide');     
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    function guardar(){
       
        var id = {{ $id }}; // lista_encargo_id
        var id2 = document.getElementById('select-producto').value; // producto_cate_nego_id
        
        if(id2 === '' || id2 === '0'){
            toastr.error("ID producto es requerido");
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);
        formData.append('id2', id2);

        axios.post('/admin/encargos/lista/producto/guardar', formData, { 
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
            toastr.error('Producto ya registrado'); 
        }
        else if(response.data.success == 2){
            toastr.success('Guardado');
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/lista/ver-productos-tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalNuevo').modal('hide'); 
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    function recargarVista(){ 
        id = {{ $id }};
        var ruta = "{{ url('/admin/encargos/lista/ver-productos-tabla') }}/"+id;
        $('#tablaDatatable').load(ruta);

        toastr.success('Actualizado');
    }


    function modalActivar(id){
       
       $('#idactivar').val(id);
       $('#modalActivar').modal('show');
    }

   
    function modalDesactivar(id){
       $('#iddesactivar').val(id);
       $('#modalDesactivar').modal('show');
    }

    function activar(){
        var id = document.getElementById('idactivar').value;
        spinHandle = loadingOverlay().activate();
       
        axios.post('/admin/encargos/lista/producto/activar-desactivar',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                respuestaActivar(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function respuestaActivar(response){
        if(response.data.success == 1){
            toastr.success('Activado'); 
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/lista/ver-productos-tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalActivar').modal('hide'); 
        }
        else{
            toastr.error('Error de validacion'); 
        } 

        document.getElementById("formulario-activar").reset();
    }

    function desactivar(){
        var id = document.getElementById('iddesactivar').value;
        spinHandle = loadingOverlay().activate();

        axios.post('/admin/encargos/lista/producto/activar-desactivar',{
        'id': id 
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
              
                respuestaDesactivar(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function respuestaDesactivar(response){
       
        if(response.data.success == 1){
            toastr.success('Desactivado'); 
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/lista/ver-productos-tabla') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalDesactivar').modal('hide'); 
        }
        else{
            toastr.error('Error de validacion'); 
        }

       document.getElementById("formulario-desactivar").reset();
    }
    
  </script>
 


@stop