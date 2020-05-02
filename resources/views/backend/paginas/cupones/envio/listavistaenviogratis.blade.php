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
            <h1>Lista de cupones</h1>
          </div>             
          <button type="button" onclick="opcionZonas()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Cargar Zonas
          </button> 

          <button type="button" onclick="opcionServicios()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Cargar Servicios
          </button>

          
          <button type="button" onclick="opcionAgregarZona()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Agregar Zona
          </button>

          
          <button type="button" onclick="opcionAgregarServicio()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Agregar Servicio
          </button>

        <br>
        <br>

          <div class="form-group" style="width: 25%">
              <label>Mínimo a comprar para aplicar este cupón</label>
              <input type="number" step="0.01" class="form-control" id="dinero" placeholder="Minimo a comprar pra validar cupon">
          </div> 

          <button type="button" onclick="actualizarDinero()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Actualizar Mínimo
          </button>

      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de Opciones</h3>
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

<!-- modal para borrar zona -->
  <div class="modal fade" id="modalZona">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-zona">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                                

                                <div class="form-group">
                                    <input type="hidden" id="id-zona">
                                    <button class="form-control" type="button" onclick="borrarZona()" class="btn btn-danger">
                                        <i class="fas fa-pencil-alt"></i>
                                            Borrar Zona
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

<!-- modal para borrar servicio -->
<div class="modal fade" id="modalServicio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Borrar Servicio</h4>
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
                                    <input type="hidden" id="id-servicio">
                                    <button class="form-control" type="button" onclick="borrarServicio()" class="btn btn-danger">
                                        <i class="fas fa-pencil-alt"></i>
                                            Borrar Servicio
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

<!-- modal agregar nueva zona -->
<div class="modal fade" id="modalNuevaZona">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Zona</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario1">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                                      
                              <div class="form-group">
                                <label style="color:#191818">Zona</label>
                                <br>
                                <div>
                                  <input type="hidden" id="id-nuevo-zona">
                                        <select class="form-control selectpicker" id="zona-select" data-live-search="true" required>   
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
                <button type="button" class="btn btn-primary" onclick="agregarZona()">Guardar</button>            
            </div>          
        </div>        
    </div>      
</div>

<!-- modal agregar nueva servicio -->
<div class="modal fade" id="modalNuevaServicio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Agregar Servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario2">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">                                      
                              <div class="form-group">
                                <label style="color:#191818">Servicio</label>
                                <br>
                                <div>
                                  <input type="hidden" id="id-nuevo-servicio">
                                        <select class="form-control selectpicker" id="servicio-select" data-live-search="true" required>   
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
                <button type="button" class="btn btn-primary" onclick="agregarServicio()">Guardar</button>            
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

    <script type="text/javascript">	 
    $(document).ready(function(){

      var dinero = {{ $dinero }};
      $('#dinero').val(dinero);
    }); 
    
 </script> 

 <!-- incluir tabla --> 
  <script>  

    function opcionZonas(){
        var id = {{ $id }};

        var ruta = "{{ URL::to('admin/cupones/tabla/zonasenviogratis') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }

    function opcionServicios(){
        var id = {{ $id }};
        var ruta = "{{ URL::to('admin/cupones/tabla/serviciosenviogratis') }}/"+id;
        $('#tablaDatatable').load(ruta);
    }

    function modalBorrarZona(id){
      document.getElementById("formulario-zona").reset();
      $('#modalZona').modal('show');
      $('#id-zona').val(id);
    }

    function opcionAgregarZona(){
      document.getElementById("formulario1").reset();
      $('#modalNuevaZona').modal('show');  
    }

    function opcionAgregarServicio(){
      document.getElementById("formulario2").reset();
      $('#modalNuevaServicio').modal('show');      
    }

    function agregarZona(){ 
      var idzona = document.getElementById('zona-select').value; 
      var idcupon = {{ $id }};

      spinHandle = loadingOverlay().activate();
      var formData = new FormData();
      formData.append('id', idzona);
      formData.append('idcupon', idcupon);
        axios.post('/admin/cupones/envio/agregarzona', formData, {
        })
      .then((response) => {
          loadingOverlay().cancel(spinHandle);
          
          if(response.data.success == 0){
            toastr.error("Error de validacion");
          }else if(response.data.success == 1){
            toastr.error("Esta zona ya esta ingresada");
          }
          else if(response.data.success == 2){
            toastr.success("Agregado");
            $('#modalNuevaZona').modal('hide');
            opcionZonas();
          }else{
              toastr.error("Error");
          }
      })
        .catch((error) => {
              loadingOverlay().cancel(spinHandle); 
              toastr.error('Error del servidor');    
      });
    }

    function agregarServicio(){
      var idservicio = document.getElementById('servicio-select').value;
      var idcupon = {{ $id }};
      spinHandle = loadingOverlay().activate();
      var formData = new FormData();
      formData.append('id', idservicio);
      formData.append('idcupon', idcupon);
        axios.post('/admin/cupones/envio/agregarservicio', formData, {
        })
      .then((response) => {
          loadingOverlay().cancel(spinHandle);
          
          if(response.data.success == 0){
            toastr.error("Error de validacion");
          }else if(response.data.success == 1){
            toastr.error("Este Servicio ya esta ingresado");
          }
          else if(response.data.success == 2){
            toastr.success("Agregado");
            $('#modalNuevaServicio').modal('hide');
            opcionServicios();
          }else{
              toastr.error("Error");
          }
      })
        .catch((error) => {
              loadingOverlay().cancel(spinHandle); 
              toastr.error('Error del servidor');    
      });
    }

    function borrarZona(){

      var id = document.getElementById('id-zona').value; 

      spinHandle = loadingOverlay().activate();

      var formData = new FormData();
      formData.append('id', id);
        axios.post('/admin/cupones/envio/borrarzona', formData, {
        })
      .then((response) => {
          loadingOverlay().cancel(spinHandle);
          
          if(response.data.success == 0){
            toastr.error("Error de validacion");
          }
          else if(response.data.success == 1){
            toastr.success("Borrado");
            $('#modalZona').modal('hide');
            opcionZonas();
          }else{
              toastr.error("Error");
          }
      })
        .catch((error) => {
              loadingOverlay().cancel(spinHandle); 
              toastr.error('Error del servidor');    
      });
    }

    function modalBorrarServicio(id){
      document.getElementById("formulario-servicio").reset();
      $('#modalServicio').modal('show');
      $('#id-servicio').val(id);
    }

    function borrarServicio(){
      var id = document.getElementById('id-servicio').value; 

      spinHandle = loadingOverlay().activate();

      var formData = new FormData();
      formData.append('id', id);
        axios.post('/admin/cupones/envio/borrarservicio', formData, {
        })
      .then((response) => {
          loadingOverlay().cancel(spinHandle);
          if(response.data.success == 0){
            toastr.error("Error de validacion");
          }
          else if(response.data.success == 1){
            toastr.success("Borrado");
            $('#modalServicio').modal('hide');
            opcionServicios();
          }else{
              toastr.error("Error");
          }
      })
        .catch((error) => {
              loadingOverlay().cancel(spinHandle); 
              toastr.error('Error del servidor');    
      });
    }

    function actualizarDinero(){
      var id = {{ $id }}; 

      var dinero = document.getElementById('dinero').value; 

      if(dinero === ''){
        toastr.error("Minimo es requerido");
        return;
      }

      spinHandle = loadingOverlay().activate();

      var formData = new FormData();
      formData.append('dinero', dinero);       
      formData.append('id', id);
        axios.post('/admin/cupones/minimo/enviogratis ', formData, {
        })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                  toastr.success("Actualizado");
                }else{
                    toastr.error("Error");
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

  </script>
 


@stop