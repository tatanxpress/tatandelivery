@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    
@stop  

  <section class="content-header">
    <div class="container-fluid">
        <div class="col-sm-12">
          <h1>Buscar Cliente para Quitar Credito</h1>
        </div>
        <button type="button" onclick="modal()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar Cliente
            </button>  
    </div> 
  </section> 
     
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla</h3>
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

    <!-- modal buscar -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar Cliente</h4>
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
                                    <label>Área + Número unido</label>
                                    <input type="text" id="numero" class="form-control" maxlength="20">
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

    <!-- modal registro -->
<div class="modal fade" id="modalRegistro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quitar Credi Puntos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-nuevo2">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Credito Actual</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" disabled id="actual" class="form-control" maxlength="20">
                                </div>

                                <div class="form-group">
                                    <label>Credito a Eliminar (con signo -)</label> </br>
                                    <input type="number" step="0.01" min="0.01" max="100.00" style="width:60%" id="credito">
                                </div>

                                <div class="form-group">
                                    <label>Nota</label> </br>
                                    <input type="text" maxlength="200" style="width:60%" id="nota">
                                </div> 
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="registrar()">Quitar</button>
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
  
    function modal(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    // setear valores y agregar credi puntos al cliente
    function nuevo(){
        $('#modalAgregar').modal('hide');
        var numero = document.getElementById('numero').value;   

        if(numero === ''){
            toastr.error('Número es requerido');
        }
       
        var ruta = "{{ url('/admin/lista/tabla/credito/para/quitar') }}/"+numero;
        $('#tablaDatatable').load(ruta);
    }

    function modalVerificar(id){        
        document.getElementById("formulario-nuevo2").reset();

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
       
        axios.post('/admin/ver/credito/actual2', formData, { 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
               
                if(response.data.success == 1){
                    $('#id-editar').val(id);
                
                    $('#actual').val(response.data.monedero);
                    $('#modalRegistro').modal('show');
                }else{
                    toastr.error('No encontrado');
                }
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
    }

    function registrar(){
        var id = document.getElementById('id-editar').value;   
        var credito = document.getElementById('credito').value;
        var nota = document.getElementById('nota').value;
      
        if(credito === ''){
            toastr.error('Credito es requerido');
            return;
        }

        if(nota === ''){
            toastr.error('Nota es requerido');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('credito', credito);
        formData.append('nota', nota);
        
        axios.post('/admin/eliminar/credito/manual', formData, { 
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
            toastr.success('Registrado'); 
            
            $('#modalRegistro').modal('hide');  
        } else if(response.data.success == 2){
            toastr.error('Error al guardar');
        }
        else { 
            toastr.error('Error desconocido');
        }
    } 
  
    
 </script>


 
@stop