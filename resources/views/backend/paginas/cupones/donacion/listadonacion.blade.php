@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Lista de Donaciones</h1>
          </div>             
          <button type="button" onclick="modalDonacion()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nueva donación
          </button> 
      </div>
    </section>
    
  <!-- seccion frame --> 
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de donaciones</h3>
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



<!-- modal crear donacion -->
<div class="modal fade" id="modalNuevo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crear Institucion</h4>
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
                                    <label>Texto Cupón</label>
                                    <input type="text" maxlength="100" class="form-control" id="nombre-nuevo" placeholder="Nombre institucion">
                                </div>
                                
                                <div class="form-group">
                                    <label>Uso limite</label>
                                    <input type="number" step="1" value="0" class="form-control" id="limite-nuevo" placeholder="Limite uso cupon">
                                </div> 

                                <div class="form-group">
                                    <label>Donación</label>
                                    <input type="number" step="0.01" value="0" min="0" class="form-control" id="donacion-nuevo" placeholder="Donacion">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Servicio</label>
                                    <br>
                                    <div>                                  
                                        <select class="form-control selectpicker" id="instituciones-select" data-live-search="true" required>   
                                            @foreach($instituciones as $item)                                                
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
                <button type="button" class="btn btn-primary" onclick="nueva()">Guardar</button>                
            </div>          
        </div>        
    </div>      
</div>
 

<!-- modal editar  -->
<div class="modal fade" id="modalEditar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Institucion</h4>
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
                                    <label>Nombre</label>
                                    <input type="hidden" id="id-donacion">
                                    <input type="text" maxlength="100" class="form-control" id="nombre-editar" placeholder="Nombre institucion">
                                </div>

                                <div class="form-group">
                                    <label>Uso limite</label>
                                    <input type="number" step="1" value="0" class="form-control" id="limite-editar" placeholder="Limite uso cupon">
                                </div> 

                                <div class="form-group">
                                    <label>Donación</label>
                                    <input type="number" step="0.01" value="0" min="0" class="form-control" id="donacion-editar" placeholder="Donacion">
                                </div>

                                <div class="form-group">
                                    <label>Cupon ilimitado</label>
                                    <br>
                                    <input type="checkbox" id="cupon-ilimitado">
                                </div>   

                                <div class="form-group">
                                    <label>Cupon Activo</label>
                                    <br>
                                    <input type="checkbox" id="cupon-activo">
                                </div> 

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarInformacion()">Guardar</button>                
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
        var ruta = "{{ URL::to('admin/cupones/tabla/donacion') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script> 

  <script>  

    // modal
    function modalDonacion(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalNuevo').modal('show');
    } 

    // nueva  
    function nueva(){
        var cupon = document.getElementById('nombre-nuevo').value;
        var limite = document.getElementById('limite-nuevo').value;
        var donacion = document.getElementById('donacion-nuevo').value;
        var institucionid = document.getElementById('instituciones-select').value;
       
        // validaciones
                      
        if(cupon === ''){
            toastr.error('Nombre es requerido');
            return;
        }

        if(limite === ''){
            toastr.error('Limite es requerido');
            return;
        }

        if(donacion === ''){
            toastr.error('Donacion es requerido');
            return;
        }

        if(institucionid === ''){
            toastr.error('ID institucion es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('textocupon', cupon);
        formData.append('usolimite', limite);
        formData.append('donacion', donacion);
        formData.append('institucionid', institucionid);
        
        axios.post('/admin/cupones/nuevo/donacion ', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1) {
               // problema
               toastr.error('Error desconocido');
            
            }else if(response.data.success == 2){
                //cupon ya existe
               toastr.error('Cupo ya existe');
            }
            else if(response.data.success == 3){
                toastr.success('Agregado'); 
                var ruta = "{{ URL::to('admin/cupones/tabla/donacion') }}";
                $('#tablaDatatable').load(ruta);         
                $('#modalNuevo').modal('hide');
            }
            else {
                toastr.error('Error desconocido');
            }
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }    

    // informacion
    function informacion(id){
        document.getElementById("formulario-editar").reset();

        spinHandle = loadingOverlay().activate();
        axios.post('/admin/cupones/info/donacion',{
        'id': id 
            })
            .then((response) => {
                
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-donacion').val(response.data.info.id);
                    $('#nombre-editar').val(response.data.info.texto_cupon);
                    $('#limite-editar').val(response.data.info.uso_limite);
                    $('#donacion-editar').val(response.data.info.dinero);

                    if(response.data.info.ilimitado == 1){
                        $('#cupon-ilimitado').prop('checked', true);
                    }
                    
                    if(response.data.info.activo == 1){
                        $('#cupon-activo').prop('checked', true);
                    }

                }else{
                    toastr.error("ID no encontrado");
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });        
    }
   

    // editar informacion de un cupon
    function editarInformacion(){
        var id = document.getElementById('id-donacion').value;
        var nombre = document.getElementById('nombre-editar').value;
        var limite = document.getElementById('limite-editar').value;
        var donacion = document.getElementById('donacion-editar').value;
        var ilimitado = document.getElementById('cupon-ilimitado').checked;
        var activo = document.getElementById('cupon-activo').checked;
        
        // validaciones
                      
        if(nombre === ''){
            toastr.error('Nombre es requerido');
            return;
        }

        if(limite === ''){
            toastr.error('Limite es requerido');
            return;
        }

        if(modalDonacion === ''){
            toastr.error('Donacion es requerido');
            return;
        }

        var ilimitado_1 = 0;
        var activo_1 = 0;

        if(ilimitado){
            ilimitado_1 = 1;
        }

        if(activo){
            activo_1 = 1;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('id', id);
        formData.append('texto', nombre);   
        formData.append('limite', limite); 
        formData.append('dinero', donacion); 
        formData.append('ilimitado', ilimitado_1); 
        formData.append('activo', activo_1);
                
        axios.post('/admin/cupones/editar/donacion', formData, {
        })
        .then((response) => {
            
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1) {
                toastr.error('Error cupon no encontrado');     
            }
            else if(response.data.success == 2){
                toastr.error('Nombre Cupon ya existe');
            }
            else if(response.data.success == 3){
                toastr.success('Actualizado'); 
                var ruta = "{{ URL::to('admin/cupones/tabla/donacion') }}";
                $('#tablaDatatable').load(ruta);  
                $('#modalEditar').modal('hide');       
            }
            else {
                toastr.error('Error desconocido');
            }
        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }

     // usos que se le ha dado a este cupon
     function usos(id){
        window.location.href="{{ url('/admin/cupones/vistausogeneral') }}/"+id;
    }

  </script>
 


@stop