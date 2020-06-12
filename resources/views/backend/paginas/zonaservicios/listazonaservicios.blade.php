@extends('backend.menus.superior') 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/estiloToggle.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/bootstrap-select.min.css') }}" type="text/css" rel="stylesheet" />

@stop

<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
          <h1>Zona servicios</h1>
          </div>
          <div style="margin-top:15px;">
            <button type="button" onclick="abrirModalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Zona Servicio
            </button>

            <button type="button" style="margin-left" onclick="abrirModalFiltro()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro para posiciones
            </button>

            <button type="button" style="margin-left" onclick="abrirModalFiltro2()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro para envio gratis por zonas (Servicio Publico)
            </button>
            <button type="button" style="margin-left" onclick="abrirModalFiltro3()" class="btn btn-info btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Filtro para mitad de precio por zonas (Servicio Publico)
            </button>
          </div>
        </div>
      </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Zona Servicios</h3>
            </div>
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
</section>


<!-- modal nuevo -->
<div class="modal fade" id="modalAgregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Zona Servicio</h4>
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
                                
                                    <label style="color:#191818">Siempre mover el select, para obtener el ID</label>

                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" data-live-search="true" required id="selectzona-identificador">   
                                            @foreach($zonas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div> 
                                </div> 
                                <div class="form-group">
                                    <label style="color:#191818">Servicios identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control selectpicker" id="selectservicio-identificador" data-live-search="true" required>   
                                            @foreach($servicios as $item)                               
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label>Activo</label>
                                    <input type="checkbox" id="cbactivo">
                                </div>
                                <div class="form-group">
                                <br>
                                    <label>Precio Envío $</label>
                                    <input type="number" step="any" id="precioenvio">
                                </div>
                                <div class="form-group">
                                <br>
                                    <label>Ganancia Motorista $</label>
                                    <input type="number" step="any" id="ganancia">
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
                <h4 class="modal-title">Editar Zona Servicio</h4>
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
                                    <label>Fecha ingreso</label>
                                    <input type="text" disabled class="form-control" id="fecha-editar">
                                </div>                    
                                <div class="form-group">
                                    <label>Zona identificador</label>
                                    <input type="hidden" id="id-editar">
                                    <input type="text" disabled class="form-control" id="zonaidentificador-editar">
                                </div>
                                <div class="form-group">
                                    <label>Servicio identificador</label>
                                    <input type="text" disabled class="form-control" id="servicioidentificador-editar">
                                </div>
                                <div class="form-group">
                                    <label>Activo</label>
                                    <input type="checkbox" id="cbactivo-editar">
                                </div>
                                <div class="form-group">
                                <br>
                                    <label>Precio Envío $</label>
                                    <input type="number" step="any" id="precioenvio-editar">
                                </div>
                                <div class="form-group">
                                <br>
                                    <label>Ganancia Motorista $</label>
                                    <input type="number" step="any" id="ganancia-editar">
                                </div>


                              <!-- nuevo tipo de cargo si supera x cantidad -->

                                <div class="form-group">
                                    <label>Nuevo cargo si supera x cantidad</label>
                                    <br>
                                    <input type="checkbox" id="cbmingratis-editar">
                                </div>                              

                                <div class="form-group">                                
                                    <label>Minimo de compra para aplicar nuevo tipo de cargo</label>
                                    <input type="number" step="0.01" id="minenvio-editar">
                                </div>

                                <div class="form-group">                                
                                    <label>Nuevo Cargo a aplicar si supera la x cantidad</label>
                                    <input type="number" step="0.01" id="nuevocargo-editar">
                                </div>

                                <!-- *** -->

                                <div class="form-group">
                                    <label>Esta zona tiene envio gratis (unicamente servicios publicos)?</label>
                                    <br>
                                    <input type="checkbox" id="cbzonagratis-editar">
                                </div>

                                <div class="form-group">
                                    <label>Mitad de precio (unicamente servicios publicos)?</label>
                                    <br>
                                    <input type="checkbox" id="cbmitadprecio-editar">
                                </div>

                               

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editar()">Actualizar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal filtro -->
<div class="modal fade" id="modalFiltro">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro para cambiar posiciones</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectzona-filtro">   
                                            @foreach($zonas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div>  
                                </div> 
                                <div class="form-group">
                                    <label style="color:#191818">Servicios tipo identificador</label>
                                    <br>
                                    <div> 
                                        <select class="form-control" id="selectservicio-filtro">   
                                            @foreach($serviciostipo as $item)                               
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
                <button type="button" class="btn btn-primary" onclick="filtrar()">Filtrar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal filtro para servicios publicos, cambiar estado envio gratis -->
<div class="modal fade" id="modalFiltro2">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro para colocar envio gratis (Servicios publicos)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro2">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectzona-filtro2" multiple="multiple" >   
                                            @foreach($zonas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div>  
                                </div>    
                                <div class="form-group">
                                    <label>Envio gratis a todas las zonas con servicio publico</label>
                                    <input type="checkbox" id="cbzonapublico">
                                </div>                             
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtrar2()">Filtrar</button>
            </div>          
        </div>        
    </div>      
</div>

<!-- modal filtro para servicios publicos, cambiar estado mitad de precio -->
<div class="modal fade" id="modalFiltro3">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filtro para colocar mitad de precio (Servicios publicos)</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-filtro3">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12"> 
                                <div class="form-group">
                                    <label style="color:#191818">Zonas identificador</label>
                                    <br>
                                    <div>
                                        <select class="form-control" id="selectzona-filtro3" multiple="multiple" >   
                                            @foreach($zonas as $item)                                                
                                                <option value="{{$item->id}}">{{$item->identificador}}</option>
                                            @endforeach                                         
                                        </select>
                                    </div>  
                                </div>    
                                <div class="form-group">
                                    <label>Mitad de precio a todas las zonas con servicio publico</label>
                                    <input type="checkbox" id="cbzonapublico2">
                                </div>                             
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="filtrar3()">Filtrar</button>
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
      var ruta = "{{ URL::to('admin/zonaservicios/tabla/lista') }}";
      $('#tablaDatatable').load(ruta);
    });

 </script>

<script>  

    // modal nuevo zona servicio
    function abrirModalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    }

    // agregar nuevo zona servicio
    function nuevo(){ 

        var selectzona = document.getElementById('selectzona-identificador').value;
        var selectservicio = document.getElementById('selectservicio-identificador').value;
        var cbactivo = document.getElementById('cbactivo').checked;
        var precioenvio = document.getElementById("precioenvio").value;
        var ganancia = document.getElementById("ganancia").value;
       
                                
        var retorno = validacionNuevo(precioenvio, ganancia);

        if (retorno) {

            var cbactivo_1 = 0;

            if(cbactivo){
                cbactivo_1 = 1;
            }
             
            let me = this;
            let formData = new FormData();
            formData.append('selectzona', selectzona);
            formData.append('selectservicio', selectservicio);
            formData.append('cbactivo', cbactivo_1);
            formData.append('precioenvio', precioenvio);
            formData.append('ganancia', ganancia);
            

            var spinHandle = loadingOverlay().activate();

            axios.post('/admin/zonaservicios/nuevo', formData, {
            })
                .then((response) => {
                   
                    loadingOverlay().cancel(spinHandle);
                    respuestaNuevo(response);
                })
                .catch((error) => {
                    toastr.error('Error del servidor');
                    loadingOverlay().cancel(spinHandle);
            });
        }
    }

    // verificar agregado nuevo
    function respuestaNuevo(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if(response.data.success == 1){
            toastr.error('Esta zona servicio ya esta agregado');        
        } else if (response.data.success == 2) {
            toastr.success('Zona servicio agregado');
           // var ruta = "{{ URL::to('admin/zonaservicios/tabla/lista') }}";
           // $('#tablaDatatable').load(ruta);
           // $('#modalAgregar').modal('hide');      
        } else if (response.data.success == 3) {
            toastr.error('Error al crear');
        } else {
            toastr.error('Error desconocido');
        } 
    }

    // validar nuevo 
    function validacionNuevo(precioenvio, ganancia){
     
        if (precioenvio === '') {
            toastr.error("Precio envio es requerido");
            return false;
        }

        if (ganancia === '') {
            toastr.error("Ganancia motorista es requerido");
            return false;
        }

        return true; 
    } 

    // informacion tipo servicios zona
    function verInformacion(id){
        spinHandle = loadingOverlay().activate();
        document.getElementById("formulario-editar").reset();
        
        axios.post('/admin/zonaservicios/informacion',{
        'id': id
            }) 
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.zonaservicio.id);
                    $('#fecha-editar').val(response.data.zonaservicio.fecha);
                    $('#zonaidentificador-editar').val(response.data.zonaservicio.idenZona);
                    $('#servicioidentificador-editar').val(response.data.zonaservicio.idenServicio);
                    $('#nuevocargo-editar').val(response.data.zonaservicio.nuevo_cargo);
                   

                    if(response.data.zonaservicio.activo == 0){
                        $("#cbactivo-editar").prop("checked", false);
                    }else{
                        $("#cbactivo-editar").prop("checked", true);
                    }  
                    $('#precioenvio-editar').val(response.data.zonaservicio.precio_envio);
                    $('#ganancia-editar').val(response.data.zonaservicio.ganancia_motorista);

                    if(response.data.zonaservicio.min_envio_gratis == 0){
                        $("#cbmingratis-editar").prop("checked", false);
                    }else{
                        $("#cbmingratis-editar").prop("checked", true);
                    } 
                    $('#minenvio-editar').val(response.data.zonaservicio.costo_envio_gratis);
 
                    // no podra tocarse los servicios que sean privados
                    if(response.data.zonaservicio.privado == 1){
                        document.getElementById("cbzonagratis-editar").disabled = true;
                    }else{
                        document.getElementById("cbzonagratis-editar").disabled = false;
                        if(response.data.zonaservicio.zona_envio_gratis == 0){
                            $("#cbzonagratis-editar").prop("checked", false);
                        }else{
                            $("#cbzonagratis-editar").prop("checked", true);
                        }    
                    }     

                     // no podra tocarse los mitad de precio los servicios privador
                    if(response.data.zonaservicio.privado == 1){
                        document.getElementById("cbmitadprecio-editar").disabled = true;

                    }else{
                        document.getElementById("cbmitadprecio-editar").disabled = false;
                        if(response.data.zonaservicio.mitad_precio == 0){
                            $("#cbmitadprecio-editar").prop("checked", false);
                        }else{
                            $("#cbmitadprecio-editar").prop("checked", true);
                        }    
                    }            

                }else{  
                    toastr.error('Tipo servicio zona no encontrado'); 
                }          
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }
 
    // editar tipo servicio zona
    function editar(){
            
        var id = document.getElementById('id-editar').value;
        var toggleactivo = document.getElementById('cbactivo-editar').checked;
        var precioenvio = document.getElementById('precioenvio-editar').value;
        var ganancia = document.getElementById('ganancia-editar').value;

        var cbmingratis = document.getElementById('cbmingratis-editar').checked;
        var minenvio = document.getElementById('minenvio-editar').value;

        var cbzonagratis = document.getElementById('cbzonagratis-editar').checked;
        var cbmitadprecio = document.getElementById('cbmitadprecio-editar').checked;

        var nuevocargo = document.getElementById('nuevocargo-editar').value;
                
        var toggle = 0;
        var cbmingratis_1 = 0;
        var cbzonagratis_1 = 0;
        var cbmitadprecio_1 = 0;

        if(toggleactivo){
            toggle = 1;
        }

        if(cbmitadprecio){
            cbmitadprecio_1 = 1;
        }
        
        if(cbmingratis){
            cbmingratis_1 = 1;
        }

        if(cbzonagratis){
            cbzonagratis_1 = 1;
        }

        if(precioenvio === ''){
            toastr.error('Agregar precio de envio');
            return;
        }

        if(ganancia === ''){
            toastr.error('Agregar ganancia');
            return;
        } 

        if(nuevocargo === ''){
            toastr.error('Nuevo cargo es requerido');
            return;
        }
    
        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);
        formData.append('toggle', toggle);
        formData.append('precioenvio', precioenvio);
        formData.append('ganancia', ganancia);
        formData.append('cbmingratis', cbmingratis_1);
        formData.append('minenvio', minenvio);
        formData.append('cbzonagratis', cbzonagratis_1);
        formData.append('cbmitadprecio', cbmitadprecio_1);
        formData.append('nuevocargo', nuevocargo);
        
        axios.post('/admin/zonaservicios/editar', formData, {
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

    // respuesta al editar 
    function respuestaEditar(response){
        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            toastr.success('Zona servicio actualizado');
            var ruta = "{{ URL::to('admin/zonaservicios/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');      
        } else if (response.data.success == 2) {
            toastr.error('Zona servicio no encontrado');
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

    // filtros
    function abrirModalFiltro(){
        $('#modalFiltro').modal('show');
    }

    function abrirModalFiltro2(){
        document.getElementById("formulario-filtro2").reset();
        $('#modalFiltro2').modal('show');
    }

    function abrirModalFiltro3(){
        document.getElementById("formulario-filtro3").reset();
        $('#modalFiltro3').modal('show');
    }

    function filtrar(){
        var idzona = document.getElementById('selectzona-filtro').value;
        var idtipo = document.getElementById('selectservicio-filtro').value;
        
         
        window.location.href="{{ url('/admin/zonaservicios') }}/"+idzona+'/'+idtipo;
    }

    function filtrar2(){
        var values = $('#selectzona-filtro2').val();  
        var cbzonapublico = document.getElementById('cbzonapublico').checked;
                
        var cbzonapublico_1 = 0;        
        if(cbzonapublico){
            cbzonapublico_1 = 1;
        }

        if(values.length == null || values.length == 0){
            toastr.error('Seleccionar mínimo 1 zona');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        for (var i = 0; i < values.length; i++) {
            formData.append('idzonas[]', values[i]);
        }
        formData.append('cbzonapublico', cbzonapublico_1);       
        
        axios.post('/admin/zonaservicios/enviogratis', formData, {
        })
        .then((response) => {
            
            loadingOverlay().cancel(spinHandle);
            if(response.data.success == 1) {
                toastr.success('Actualizado');
                var ruta = "{{ URL::to('admin/zonaservicios/tabla/lista') }}";
                $('#tablaDatatable').load(ruta);               
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

    function filtrar3(){
        var values = $('#selectzona-filtro3').val();  
        var cbzonapublico = document.getElementById('cbzonapublico2').checked;
                
        var cbzonapublico_1 = 0;        
        if(cbzonapublico){
            cbzonapublico_1 = 1;
        }

        if(values.length == null || values.length == 0){
            toastr.error('Seleccionar mínimo 1 zona');
            return;
        }

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        for (var i = 0; i < values.length; i++) {
            formData.append('idzonas[]', values[i]);
        }
        formData.append('cbzonapublico', cbzonapublico_1);       
        
        axios.post('/admin/zonaservicios/mitadprecio', formData, {
        })
        .then((response) => {
            
            loadingOverlay().cancel(spinHandle);
            if(response.data.success == 1) {
                toastr.success('Actualizado');
                var ruta = "{{ URL::to('admin/zonaservicios/tabla/lista') }}";
                $('#tablaDatatable').load(ruta);               
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
 
@endsection