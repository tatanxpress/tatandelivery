@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Bitacora revisador</h1>
          </div>    
          <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Bitacora
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de bitacoras</h3>
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
                <h4 class="modal-title">Nuevo revisador</h4>
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
                                    <label style="color:#191818">Revisador identificador</label>
                                    <br>
                                    <div>
                                        <select id="revisador-nuevo" class="form-control">   
                                            @foreach($revisador as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label>Fecha Desde</label>
                                    <input type="date" class="form-control" id="fechadesde-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Fecha Hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Total $</label>
                                    <input type="number" step="0.01" class="form-control" id="total-nuevo">
                                </div>
                                <div class="form-group">
                                    <label>Confirmadas Total</label>
                                    <input type="number" step="1" class="form-control" id="confirmada-nuevo" >
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
                <h4 class="modal-title">Editar bitacora</h4>
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
                                    <label>Fecha desde</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="date" class="form-control" id="fechadesde-editar">
                                </div>
                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-editar">
                                </div>
                                <div class="form-group">
                                    <label>Total ordenes</label>
                                    <input type="number" class="form-control" id="total-editar">
                                </div>
                                <div class="form-group">
                                    <label>Confirmadas total</label>
                                    <input type="number" class="form-control" id="confirmada-editar" >
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

    <script src="{{ asset('js/backend/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/backend/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>

 <!-- incluir tabla --> 
  <script type="text/javascript">	 
    $(document).ready(function(){       
        var ruta = "{{ URL::to('admin/revisadorbitacora/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script> 

    function modalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    } 

    function nuevo(){
        var revisador = document.getElementById('revisador-nuevo').value;
        var fechadesde = document.getElementById('fechadesde-nuevo').value;
        var fechahasta = document.getElementById('fechahasta-nuevo').value;
        var total = document.getElementById('total-nuevo').value;
        var confirmada = document.getElementById('confirmada-nuevo').value;
        
        var retorno = validarNuevo(revisador, fechadesde, fechahasta, total, confirmada);

        if(retorno){

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('revisador', revisador);
            formData.append('fechadesde', fechadesde);
            formData.append('fechahasta', fechahasta);
            formData.append('total', total);
            formData.append('confirmada', confirmada);
            
            axios.post('/admin/revisadorbitacora/nuevo', formData, { 
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
        } else if(response.data.success == 1){
            toastr.success('Agregado');           
           
            var ruta = "{{ url('/admin/revisadorbitacora/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');  
            
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

    function validarNuevo(revisador, fechadesde, fechahasta, total, confirmada){
       
        if(fechadesde === ''){
            toastr.error("fecha desde es requerido");
            return;
        }

        if(fechahasta === ''){
            toastr.error("fecha hasta es requerido");
            return;
        }

        if(total === ''){
            toastr.error("total es requerido");
            return;
        }

        if(confirmada === ''){
            toastr.error("confirmada es requerido");
            return;
        }
        
        return true;
    }

    function informacion(id){
        spinHandle = loadingOverlay().activate();
        document.getElementById("formulario-editar").reset();

        axios.post('/admin/revisadorbitacora/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.bitacora.id);
                    $('#fechadesde-editar').val(response.data.bitacora.fecha1);
                    $('#fechahasta-editar').val(response.data.bitacora.fecha2);
                    $('#total-editar').val(response.data.bitacora.total);
                    $('#confirmada-editar').val(response.data.bitacora.confirmadas);
                   
                }else{
                    toastr.error("ID no encontrado");
                }

            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
    
    function editar(){
        
        var id = document.getElementById('id-editar').value;
        var fechadesde = document.getElementById('fechadesde-editar').value;
        var fechahasta = document.getElementById('fechahasta-editar').value;
        var total = document.getElementById('total-editar').value;
        var confirmada = document.getElementById('confirmada-editar').value;

        var retorno = validarEditar(fechadesde, fechahasta, total, confirmada);

        if(retorno){
            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fechadesde', fechadesde);
            formData.append('fechahasta', fechahasta);
            formData.append('total', total);
            formData.append('confirmada', confirmada);       

            axios.post('/admin/revisadorbitacora/editar', formData, { 
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
        } else if(response.data.success == 1){
            toastr.success('Actualizado');
           
            var ruta = "{{ url('/admin/revisadorbitacora/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

    function validarEditar(fechadesde, fechahasta, total, confirmada){

        if(fechadesde === ''){
            toastr.error("fecha desde es requerido");
            return;
        }

        if(fechahasta === ''){
            toastr.error("fecha hasta desde es requerido");
            return;
        }

        if(total === ''){
            toastr.error("total desde es requerido");
            return;
        }

        if(confirmada === ''){
            toastr.error("confirmada desde es requerido");
            return;
        }
        
        return true;
    }

  </script>
 


@stop