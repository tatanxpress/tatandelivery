@extends('backend.menus.superior') 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/estiloToggle.css') }}" type="text/css" rel="stylesheet" /> 

@stop

<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-3">
          <h1>Números registrados (ultimos 100)</h1>
          </div>
          <div class="col-sm-2">
            <button type="button" onclick="abrirModal()" class="btn btn-info btn-sm">
            <i class="fas fa-pencil-alt"></i>
                Nuevo Registro
            </button>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
            <h3 class="card-title">Números</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="tablaDatatable">
                    </div>
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
                <h4 class="modal-title">Nuevo Registro</h4>
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
                                    <label>Area</label>
                                    <input type="text" maxlength="10" class="form-control" id="area-nuevo" placeholder="Area">
                                </div>
                                
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" maxlength="50" class="form-control" id="numero-nuevo" placeholder="Numero">
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
                                    <label>Area</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="10" class="form-control" id="area-editar" placeholder="Area">
                                </div>
                                
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="text" maxlength="50" class="form-control" id="numero-editar" placeholder="Numero">
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

 <script type="text/javascript">
    $(document).ready(function(){
      var ruta = "{{ URL::to('admin/numeros/tabla/temporales') }}";
      $('#tablaDatatable').load(ruta);
    });
 </script>

<script>

    // modal nuevo tipo servicio
    function abrirModal(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    function nuevo(){
        var area = document.getElementById('area-nuevo').value;
        var numero = document.getElementById('numero-nuevo').value;

        if(area === ''){
            toastr.error('Area es requerida'); 
            return;
        }

        if(numero === ''){
            toastr.error('Numero es requerida'); 
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('area', area);
        formData.append('numero', numero);
        
        axios.post('/admin/numeros/nuevo/registro', formData, { 
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                    if(response.data.success == 1){
                        toastr.success('Guardado');
                        var ruta = "{{ URL::to('admin/numeros/tabla/temporales') }}";
                        $('#tablaDatatable').load(ruta);
                        $('#modalAgregar').modal('hide');
                    }else{
                        toastr.error('Error al guardar');
                    }
                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
    }



    // informacion del cliente
    function informacion(id){
        document.getElementById("formulario-editar").reset();
        spinHandle = loadingOverlay().activate();
        
        axios.post('/admin/numeros/informacion',{
        'id': id  
            }) 
            .then((response) => {	
                loadingOverlay().cancel(spinHandle);
               if(response.data.success == 1){                
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.info.id);
                    $('#area-editar').val(response.data.info.area);
                    $('#numero-editar').val(response.data.info.numero);                                   
                }else{ 
                    toastr.error('Usuario no encontrado'); 
                }         
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    // editar registro
    function editar(){
        var id = document.getElementById('id-editar').value;
        var area = document.getElementById('area-editar').value;
        var numero = document.getElementById('numero-editar').value;


        if(area === ''){
            toastr.error('Area es requerida'); 
            return;
        }

        if(numero === ''){
            toastr.error('Numero es requerida'); 
            return;
        }
      
        let formData = new FormData();
        formData.append('id', id);
        formData.append('area', area);
        formData.append('numero', numero);
       
        var spinHandle = loadingOverlay().activate();
 
        axios.post('/admin/numeros/editar', formData, {
        })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);                    
                if(response.data.success == 1){
                    toastr.success('Actualizado');
                    var ruta = "{{ URL::to('admin/numeros/tabla/temporales') }}";
                    $('#tablaDatatable').load(ruta);
                    $('#modalEditar').modal('hide');
                }else{
                    toastr.error('Error al editar');
                }
            })
            .catch((error) => {
                toastr.error('Error del servidor');
                loadingOverlay().cancel(spinHandle);
        });
    }


</script>
 
@endsection