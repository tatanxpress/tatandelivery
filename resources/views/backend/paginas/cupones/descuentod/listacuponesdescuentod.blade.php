@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Lista de cupones para Descuento Dinero</h1>
          </div>             
          <button type="button" onclick="modal1()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Cupón
          </button> 
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de cupones</h3>
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


<!-- modal desactivar -->
<div class="modal fade" id="modalDesactivar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Desactivar cupón</h4>
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
                                    <input type="hidden" id="id-cupon-desactivar">
                                    <button class="form-control" type="button" onclick="desactivarCupon()" class="btn btn-danger">
                                        <i class="fas fa-pencil-alt"></i>
                                            Desactivar Cupón
                                    </button>
                                </div>                    
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>               
                   
        </div>        
    </div>      
</div>
 
<!-- modal activar -->
<div class="modal fade" id="modalActivar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activar cupón</h4>
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
                                    <input type="hidden" id="id-cupon-activar">
                                    <button class="form-control" type="button" onclick="activarCupon()" class="btn btn-danger">
                                        <i class="fas fa-pencil-alt"></i>
                                            Activar Cupón
                                    </button>
                                </div>                    
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>               
                   
        </div>        
    </div>      
</div>


<!-- modal crear cupon -->
<div class="modal fade" id="modalDescuentoD">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Crear Cupón Descuento $$</h4>
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
                                    <input type="text" maxlength="50" class="form-control" id="texto-nuevo" placeholder="Nombre del cupón">
                                </div>

                                <div class="form-group">
                                    <label>Uso limite</label>
                                    <input type="number" step="1" value="0" class="form-control" id="limite-nuevo" placeholder="Limite uso cupon">
                                </div>   

                                 <div class="form-group">
                                    <label>Dinero Descuento</label>
                                    <input type="number" step="0.01" value="0" class="form-control" id="dinero-nuevo" placeholder="Dinero descuento">
                                </div>

                                <div class="form-group">
                                    <label>Aplica para $0.00 cargo de envio</label>
                                    <br>
                                    <input type="checkbox" id="aplica-nuevo">
                                </div>

                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="servicios-nuevo" multiple="multiple" >   
                                            @foreach($servicios as $item)                                                
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
                <button type="button" class="btn btn-primary" onclick="nuevoCupon()">Guardar</button>                
            </div>          
        </div>        
    </div>      
</div>
 
<!-- modal informacion cupon -->
<div class="modal fade" id="modalInformacion">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-informacion">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Texto Cupón</label>
                                    <input type="hidden" id="id-cupon-informacion">
                                    <input type="text" maxlength="50" class="form-control" id="texto-editar" placeholder="Nombre del cupón">
                                </div>

                                <div class="form-group">
                                    <label>Uso limite</label>
                                    <input type="number" step="1" value="0" class="form-control" id="limite-editar" placeholder="Limite uso cupon">
                                </div> 

                                 <div class="form-group">
                                    <label>Dinero Descuento</label>
                                    <input type="number" step="0.01" value="0" class="form-control" id="dinero-editar" placeholder="Dinero descuento">
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarInformacion()">Actualizar</button>                
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
        var ruta = "{{ URL::to('admin/cupones/tabla/descuentod') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script> 

  <script>  

    // modal
    function modal1(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalDescuentoD').modal('show');
    } 

    // nuevo cupon 
    function nuevoCupon(){
        var servicios = $('#servicios-nuevo').val();  
        var textocupon = document.getElementById('texto-nuevo').value;
        var limite = document.getElementById('limite-nuevo').value;
        var dinero = document.getElementById('dinero-nuevo').value;
        var aplica = document.getElementById('aplica-nuevo').checked;

        // validaciones
                      
        if(textocupon === ''){
            toastr.error('Nombre cupon es requerido');
            return;
        }

        if(limite === ''){
            toastr.error('Limite cupon es requerido');
            return;
        }

        if(dinero === ''){
            toastr.error('Dinero es requerido');
            return;
        }
       
        if(servicios.length == null || servicios.length == 0){
            toastr.error('Seleccionar mínimo 1 servicio');
            return;
        }

        var aplica_1 = 0

            if(aplica){
                aplica_1 = 1;
            }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        for (var i = 0; i < servicios.length; i++) {
            formData.append('idservicios[]', servicios[i]);
        }

        formData.append('textocupon', textocupon);
        formData.append('usolimite', limite);
        formData.append('dinero', dinero);
        formData.append('aplica', aplica_1);       
        
        axios.post('/admin/cupones/nuevo/descuentod ', formData, {
        })
        .then((response) => {
            
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1) {
                toastr.error('Error al ingresar datos');
            }else if(response.data.success == 2) {
                toastr.error('Este Cupon ya existe');
            }else if(response.data.success == 3) {
                toastr.success('Agregado'); 
                var ruta = "{{ URL::to('admin/cupones/tabla/descuentod') }}";
                $('#tablaDatatable').load(ruta);         
                $('#modalDescuentoD').modal('hide'); 
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

    // informacion del cupon
    function informacion(id){
        document.getElementById("formulario-informacion").reset();

        spinHandle = loadingOverlay().activate();
        axios.post('/admin/cupones/info/descuentod',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalInformacion').modal('show');
                    $('#id-cupon-informacion').val(response.data.info.id);
                    $('#texto-editar').val(response.data.info.texto_cupon);
                    $('#limite-editar').val(response.data.info.uso_limite);
                    $('#dinero-editar').val(response.data.info.dinero);
                }else{
                    toastr.error("ID no encontrado");
                }
            }) 
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
        
    }

    // usos que se le ha dado a este cupon
    function usos(id){
        window.location.href="{{ url('/admin/cupones/vistausogeneral') }}/"+id;
    }

    // desactivar cupon, lo que hace es llegar el contador al limite de usos
    function desactivar(id){
        document.getElementById("formulario-desactivar").reset();
        $('#modalDesactivar').modal('show');
        $('#id-cupon-desactivar').val(id);       
    }

    // peticion para desactivar cupon
    function desactivarCupon(){
        var id = document.getElementById('id-cupon-desactivar').value;
        
        spinHandle = loadingOverlay().activate();
        axios.post('/admin/cupones/desactivar',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                
                if(response.data.success == 0){ 
                    toastr.error("Error de validacion");
                }else if(response.data.success == 1){         
                    $('#modalDesactivar').modal('hide');
                    toastr.success("Desactivado"); 
                    var ruta = "{{ URL::to('admin/cupones/tabla/descuentod') }}";
                    $('#tablaDatatable').load(ruta); 
                }else if(response.data.success == 2){
                    toastr.error("ID no encontrado");
                }else{                   
                    toastr.error("ID no encontrado");
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function activar(id){
        document.getElementById("formulario-activar").reset();
        $('#modalActivar').modal('show');
        $('#id-cupon-activar').val(id);       
    }

    // peticion para activar cupon
    function activarCupon(){
        var id = document.getElementById('id-cupon-activar').value;
        
        spinHandle = loadingOverlay().activate();
        axios.post('/admin/cupones/activar',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                
                if(response.data.success == 0){ 
                    toastr.error("Error de validacion");
                }
                else if(response.data.success == 1){
                    // cupon llego al limite usos
                    toastr.error("Limite de uso superado");  
                    $('#modalActivar').modal('hide');              
                }else if(response.data.success == 2){      
                    $('#modalActivar').modal('hide');
                    toastr.success("Activado"); 
                    var ruta = "{{ URL::to('admin/cupones/tabla/descuentod') }}";
                    $('#tablaDatatable').load(ruta); 
                }else if(response.data.success == 3){
                    toastr.error("ID no encontrado");
                }else{                   
                    toastr.error("ID no encontrado");
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
    
    // ajustes del cupon
    function editar(id){
        window.location.href="{{ url('/admin/cupones/vista/descd') }}/"+id;        
    }

    // editar informacion de un cupon
    function editarInformacion(){
        var id = document.getElementById('id-cupon-informacion').value;
        var textocupon = document.getElementById('texto-editar').value;
        var limite = document.getElementById('limite-editar').value;
        var dinero = document.getElementById('dinero-editar').value;

        // validaciones
                      
        if(textocupon === ''){
            toastr.error('Nombre cupon es requerido');
            return;
        }

        if(limite === ''){
            toastr.error('Limite cupon es requerido');
            return;
        }

        if(dinero === ''){
            toastr.error('Dinero es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        
        formData.append('id', id);
        formData.append('texto', textocupon);   
        formData.append('limite', limite);   
        formData.append('dinero', dinero); 
        
        axios.post('/admin/cupones/editar/descuentod', formData, {
        })
        .then((response) => {
            
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1) {
                toastr.error('Cupon no encontrado');
            }else if(response.data.success == 2){
                toastr.error('Este Nombre Cupon ya existe');
            }
            else if(response.data.success == 3) {               
                toastr.success('Actualizado'); 
                var ruta = "{{ URL::to('admin/cupones/tabla/descuentod') }}";
                $('#tablaDatatable').load(ruta);  
                $('#modalInformacion').modal('hide');        
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

  </script>
 


@stop