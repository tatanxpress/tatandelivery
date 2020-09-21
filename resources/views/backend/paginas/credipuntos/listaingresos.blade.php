@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/estiloToggle.css') }}" type="text/css" rel="stylesheet" /> 
@stop  

  <section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
          <h1>Revisar Credi Puntos Comprados</h1>
        </div>          
    </div> 
  </section> 
     
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de ingresos</h3>
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
                <h4 class="modal-title">Agregar Credi Puntos</h4>
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
                                    <label>MONEDERO ACTUAL DEL CLIENTE</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="number" disabled class="form-control" id="credito">
                                </div>

                                <div class="form-group">
                                    <label>Nota</label>
                                    <input type="text" max="200" class="form-control" id="nota-nuevo" placeholder="Nota">
                                </div>

                                <div class="form-group"> 
                                    <label>Activar si NO AGREGAR CREDI PUNTOS</label><br>
                                    <input type="hidden" id="id-editar">
                                    <label class="switch" style="margin-top:10px">
                                        <input type="checkbox" id="cbvista">
                                        <div class="slider round">
                                            <span class="on">No Agregar</span> 
                                            <span class="off">Agregando</span>
                                        </div>
                                    </label>
                                </div> 

                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="nuevo()">Verificar</button>
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
        var ruta = "{{ URL::to('admin/credipuntos/lista') }}";
        $('#tablaDatatable').load(ruta);
    });  

    function modalVerificar(id){
        document.getElementById("formulario-nuevo").reset();

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
       
        axios.post('/admin/ver/credito/actual', formData, { 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#id-editar').val(id);
                    $('#credito').val(response.data.monedero);
                    $('#modalAgregar').modal('show');
                }else{
                    toastr.error('No encontrado');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
    }

    // setear valores y agregar credi puntos al cliente
    function nuevo(){
        var id = document.getElementById('id-editar').value;        
        var nota = document.getElementById('nota-nuevo').value;
        var cbvista = document.getElementById('cbvista').checked;
        
        var cbvista_1 = 0;
        if(cbvista){
            cbvista_1 = 1;
        }
       
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('nota', nota);
        formData.append('estado', cbvista_1);

        axios.post('/admin/verificar/credipuntos/cliente', formData, { 
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

    function respuestaNuevo(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Verificado'); 
            var ruta = "{{ url('/admin/credipuntos/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');  
        } else if(response.data.success == 2){
            toastr.error('Error al guardar');
        }
        else { 
            toastr.error('Error desconocido');
        }
    } 
  
    
 </script>


 
@stop