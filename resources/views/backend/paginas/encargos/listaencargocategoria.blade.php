@extends('backend.menus.superior')
 
@section('content-admin-css')
<link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

     
@stop  

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Agregando categorias para encargo: {{ $nombre }}</h1>
          </div>     
          <button type="button" onclick="modalNegocio()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Agregar Categoria
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


<!-- modal editar -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Registro</h4>
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
                                    <label>Activo Categoria</label>
                                    <br>
                                    <input type="checkbox" id="activo-editar">
                                </div>   

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
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
                                        <select class="form-control" id="select-negocio" onchange="buscador(this)">
                                                <option value="0" selected>Seleccionar</option>
                                            @foreach($negocios as $item)                                                
                                                <option value="{{$item->id}}">{{$item->nombre}}</option>
                                            @endforeach   
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label style="color:#191818">Categoria</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="select-categoria">                                     
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
        id = {{ $id }};  // id  encargo_id
        
        var ruta = "{{ url('/admin/encargos/tabla/lista-categorias') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

<script>

    function modalNegocio(){
        document.getElementById("formulario-nuevo").reset();
        document.getElementById("select-categoria").options.length = 0;
        $('#modalNuevo').modal('show');
    }

    function buscador(sel){

        if(sel.value != 0){               
            var spinHandle = loadingOverlay().activate();
            axios.post('/admin/encargos/lista/buscador-categoria',{
            'id': sel.value 
            }) 
            .then((response) => {	
                loadingOverlay().cancel(spinHandle);                    
                if (response.data.success == 1) {
                    var tipo = document.getElementById("select-categoria");
                    // limpiar select
                    document.getElementById("select-categoria").options.length = 0;
                    if(response.data.categoria.length == 0){                          
                        tipo.options[0] = new Option('Ninguna disponible', 0); 
                    }else{
                        $.each(response.data.categoria, function( key, val ){  
                            tipo.options[key] = new Option(val.nombre, val.id);
                        });
                    }
                }else{
                    toastr.error('Error de retorno');
                }
            })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    loadingOverlay().cancel(spinHandle);
            });    
        } 
    }
    
    function modalEditar(id){
        document.getElementById("formulario-editar").reset();
        
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);

        axios.post('/admin/encargos/info/lista-encargo-categoria', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);

                    if(response.data.success == 1){

                        $('#id-editar').val(response.data.info.id); 
                        if(response.data.info.activo == 1){
                            $('#activo-editar').prop('checked', true);
                        }
                        $('#modalEditar').modal('show');     

                    }else{
                        toastr.error('Error');
                    }                          
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });

    }

    function editar(){
        var id = document.getElementById('id-editar').value;
        var valor = document.getElementById('activo-editar').checked;
        var valor_1 = 0;

        if(valor){ 
            valor_1 = 1;
        }
        
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);
        formData.append('valor', valor_1);

        axios.post('/admin/encargos/lista/editar', formData, { 
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
            toastr.success('Editado'); 
            id = {{ $id }}; 
            
            var ruta = "{{ url('/admin/encargos/tabla/lista-categorias') }}/"+id;
            $('#tablaDatatable').load(ruta);

            $('#modalEditar').modal('hide');     
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    function guardar(){
       
        var id = {{ $id }};
        var idcategoria = document.getElementById('select-categoria').value;
        

        if(idcategoria === '' || idcategoria === '0'){
            toastr.error("Categoria es requerido");
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);
        formData.append('idcategoria', idcategoria);

        axios.post('/admin/encargos/lista-nuevo', formData, { 
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
            toastr.error('Categoria ya registrada'); 
        }
        else if(response.data.success == 2){
            toastr.success('Guardado'); 
            id = {{ $id }};
            var ruta = "{{ url('/admin/encargos/tabla/lista-categorias') }}/"+id;
            $('#tablaDatatable').load(ruta);
            $('#modalNuevo').modal('hide'); 
        }
        else{
            toastr.error('Error al guardar'); 
        }
    }

    function productos(id){

        //id es el id de la lista

        window.location.href="{{ url('/admin/encargos/lista/ver-productos') }}/"+id;
    }

    function recargarVista(){
        id = {{ $id }};
        var ruta = "{{ url('/admin/encargos/tabla/lista-categorias') }}/"+id;
        $('#tablaDatatable').load(ruta);

        toastr.success('Actualizado');
    }
    
  </script>
 


@stop