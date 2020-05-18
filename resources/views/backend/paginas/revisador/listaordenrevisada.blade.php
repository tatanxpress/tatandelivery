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
            <h1>Lista de ordenes revisadas</h1>
          </div> 
          <button type="button" onclick="modalBuscar()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Buscar ordenes revisadas
          </button>    

          <button type="button" onclick="modalBuscar2()" class="btn btn-primary btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro Ordenes No Revisadas
          </button>  
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla Registros</h3>
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
<div class="modal fade" id="modalBuscar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Buscar ordenes revisadas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-buscar">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                
                            <div class="form-group">
                                    <label style="color:#191818">Revisador identificador</label>
                                    <br>
                                    <div>
                                        <select id="revisador-buscar" class="form-control" data-live-search="true" required>   
                                            @foreach($revisador as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach
                                        </select>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label>Fecha desde</label>
                                    <input type="date" class="form-control" id="fechadesde-buscar">
                                </div>
                                <div class="form-group">
                                    <label>Fecha hasta</label>
                                    <input type="date" class="form-control" id="fechahasta-buscar">
                                </div>                               
                            
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" onclick="reporte()">Reporte</button>
                <button type="button" class="btn btn-primary" onclick="buscar()">Buscar</button>
            </div>          
        </div>        
    </div>      
</div>


<!-- modal buscar ordenes donde motorista aun no ha depositado-->
<div class="modal fade" id="modalBuscar2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Motoristas falta de entregar dinero</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-buscar2">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">
                                
                            <div class="form-group">
                                    <label style="color:#191818">Motorista identificador</label>
                                    <br>
                                    <div>
                                        <select id="moto-buscar2" class="form-control" data-live-search="true" required>   
                                            @foreach($motorista as $item)                                                
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
                <button type="button" class="btn btn-primary" onclick="buscar2()">Buscar</button>
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
 <!-- incluir tabla --> 
  
  <script> 

    function modalBuscar(){
        document.getElementById("formulario-buscar").reset();
        $('#modalBuscar').modal('show');
    } 

    
    function modalBuscar2(){
        document.getElementById("formulario-buscar2").reset();
        $('#modalBuscar2').modal('show');
    } 

    function buscar(){
        var revisador = document.getElementById('revisador-buscar').value;
        var fechadesde = document.getElementById('fechadesde-buscar').value;
        var fechahasta = document.getElementById('fechahasta-buscar').value;
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            var ruta = "{{ url('/admin/ordenrevisada') }}/"+revisador+"/"+fechadesde+"/"+fechahasta;
            $('#tablaDatatable').load(ruta);       
                 
        } 
    }  

    // reporte de ordenes revisadas
    function reporte(){
        var revisador = document.getElementById('revisador-buscar').value;
        var fechadesde = document.getElementById('fechadesde-buscar').value;
        var fechahasta = document.getElementById('fechahasta-buscar').value;
        
        var retorno = validarNuevo(fechadesde, fechahasta);

        if(retorno){

            window.open("{{ URL::to('admin/ordenrevisada3') }}/" + revisador + "/" + fechadesde + "/" + fechahasta);
   
        } 
    } 

    function buscar2(){
        var moto = document.getElementById('moto-buscar2').value;
        $('#modalBuscar2').modal('hide');
        var ruta = "{{ url('/admin/ordenrevisada2') }}/"+moto
        $('#tablaDatatable').load(ruta);
    }

    function validarNuevo(fechadesde, fechahasta){
       
        if(fechadesde === ''){
            toastr.error("fecha desde es requerido"); 
            return;
        }

        if(fechahasta === ''){
            toastr.error("fecha hasta es requerido");
            return;
        }
        
        return true;
    }

    

  </script>
 


@stop