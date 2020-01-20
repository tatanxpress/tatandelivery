@extends('backend.menus.superior')
 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

    
@stop 

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Motoristas</h1>
          </div>    
          <button type="button" onclick="modalAgregar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                    Nuevo Motorista
          </button>    
      </div>
    </section>
    
  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de motoristas</h3>
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
                <h4 class="modal-title">Nuevo Motorista</h4>
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
                                    <label>Identificador</label>
                                    <input type="text" maxlength="50" class="form-control" id="identificador" placeholder="Identificador unico">
                                </div>
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" maxlength="50" class="form-control" id="nombre-nuevo" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-nuevo" placeholder="Telefono">
                                </div>
                                <div class="form-group">
                                    <label>Correo</label>
                                    <input type="text" maxlength="100" class="form-control" id="correo-nuevo" placeholder="Correo">
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="text" disabled class="form-control" placeholder="12345678">
                                </div>
                                <div class="form-group">
                                    <label>Tipo vehiculo</label>
                                    <input type="text" maxlength="50" class="form-control" id="tipovehiculo-nuevo" placeholder="Tipo de vehiculo">
                                </div>
                                <div class="form-group">
                                    <label>Numero vehiculo</label>
                                    <input type="text" maxlength="50" class="form-control" id="numerovehiculo-nuevo" placeholder="Numero de vehiculo">
                                </div>
                                <div class="form-group">
                                    <label>licensia</label>
                                    <input type="text" maxlength="30" class="form-control" id="licensia-nuevo" placeholder="Licensia">
                                </div>
                                <div class="form-group">
                                    <label>DUI</label>
                                    <input type="text" maxlength="25" class="form-control" id="dui-nuevo" placeholder="DUI">
                                </div>
                                
                                <div class="form-group">
                                    <label>Zona Pago (Motorista pueden ver donde entregar el dinero)</label>
                                    <input type="checkbox" id="zonapago-nuevo">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de:</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-nuevo" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
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
                <h4 class="modal-title">Editar Motorista</h4>
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
                                    <input type="hidden" id="id-editar">
                                    <input type="text" maxlength="50" class="form-control" id="nombre-editar" placeholder="Nombre">
                                </div>
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input type="text" maxlength="20" class="form-control" id="telefono-editar" placeholder="Telefono">
                                </div>
                                <div class="form-group">
                                    <label>Correo</label>
                                    <input type="text" maxlength="100" class="form-control" id="correo-editar" placeholder="Correo">
                                </div>                           
                                <div class="form-group">
                                    <label>Tipo vehiculo</label>
                                    <input type="text" maxlength="50" class="form-control" id="tipovehiculo-editar" placeholder="Tipo de vehiculo">
                                </div>
                                <div class="form-group">
                                    <label>Numero vehiculo</label>
                                    <input type="text" maxlength="50" class="form-control" id="numerovehiculo-editar" placeholder="Numero de vehiculo">
                                </div>
                                <div class="form-group">
                                    <label>licensia</label>
                                    <input type="text" maxlength="30" class="form-control" id="licensia-editar" placeholder="Licensia">
                                </div>
                                <div class="form-group">
                                    <label>DUI</label>
                                    <input type="text" maxlength="25" class="form-control" id="dui-editar" placeholder="DUI">
                                </div>
                                
                                <div class="form-group">
                                    <label>Zona Pago (Motorista pueden ver donde entregar el dinero)</label>
                                    <input type="checkbox" id="zonapago-editar">
                                </div>

                                <div class="form-group">
                                    <label>Activo</label>
                                    <input type="checkbox" id="activo-editar">
                                </div>

                                <div class="form-group">
                                    <label>Limite ordenes</label>
                                    <input type="checkbox" id="limite-editar">
                                </div>

                                <div class="form-group">
                                    <div>
                                        <label>Imagen</label>
                                        <p>Tamaño recomendado de:</p>
                                    </div>
                                    <br>
                                    <div class="col-md-10">
                                        <input type="file" style="color:#191818" id="imagen-editar" accept="image/jpeg, image/jpg, image/png"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label id="txtImagen">Imagen promocion</label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <img id="img-imagen"  width="40%">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Fecha ingreso</label>
                                    <input type="text" disabled class="form-control" id="fecha">
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
 
<!-- modal promedio -->
<div class="modal fade" id="modalPromedio">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Promedio de calificacion</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-promedio">
                    <div class="card-body">
                        <div class="row">  
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Promedio global</label>
                                    <input type="text" class="form-control" id="promedio">
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
               
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
        var ruta = "{{ URL::to('admin/motoristas/tabla/lista') }}";
        $('#tablaDatatable').load(ruta);
    }); 
    
 </script>

  <script> 

    function modalAgregar(){
        document.getElementById("formulario-nuevo").reset();
        $('#modalAgregar').modal('show');
    } 

    function nuevo(){
        var identi = document.getElementById('identificador').value;
        var nombre = document.getElementById('nombre-nuevo').value;
        var telefono = document.getElementById('telefono-nuevo').value;
        var correo = document.getElementById('correo-nuevo').value;
        var tipovehiculo = document.getElementById('tipovehiculo-nuevo').value;
        var numerovehiculo = document.getElementById('numerovehiculo-nuevo').value;
        var licensia = document.getElementById('licensia-nuevo').value;
        var dui = document.getElementById('dui-nuevo').value;
        var cbzona = document.getElementById('zonapago-nuevo').checked;

        var imagen = document.getElementById('imagen-nuevo');

        var retorno = validarNuevo(identi, nombre, telefono, correo, tipovehiculo, numerovehiculo, licensia, dui, imagen);

        if(retorno){

            var cbzona_1 = 0;
            if(cbzona){
                cbzona_1 = 1;
            }

            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('identi', identi);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('correo', correo);
            formData.append('tipovehiculo', tipovehiculo);
            formData.append('numerovehiculo', numerovehiculo);
            formData.append('licensia', licensia);
            formData.append('dui', dui);
            formData.append('imagen', imagen.files[0]);
            formData.append('cbzona', cbzona_1);


            axios.post('/admin/motoristas/nuevo', formData, { 
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
        } else if (response.data.success == 1) {
            toastr.error('Correo ya registrado'); 
        } else if(response.data.success == 2){
            toastr.success('Agregado');           
           
            var ruta = "{{ url('/admin/motoristas/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalAgregar').modal('hide');  
            
        } else if(response.data.success == 3){
            toastr.error('Error al agregar');
        } else if(response.data.success == 4){
            toastr.error('El identificador ya existe');
        } 
        else {
            toastr.error('Error desconocido');
        }
    } 

    function validarNuevo(identi ,nombre, telefono, correo, tipovehiculo, numerovehiculo, licensia, dui, imagen){

        if(identi === ''){
            toastr.error("identificador es requerido");
            return;
        } 
        
        if(identi.length > 50){
            toastr.error("50 caracter máximo identificador");
            return false;
        }
       
        if(nombre === ''){
            toastr.error("nombre es requerido");
            return;
        }
        
        if(nombre.length > 50){
            toastr.error("50 caracter máximo nombre");
            return false;
        }

        if(telefono === ''){
            toastr.error("telefono es requerido");
            return;
        }
        
        if(telefono.length > 20){
            toastr.error("20 caracter máximo telefono");
            return false;
        }

        if(correo === ''){
            toastr.error("correo es requerido");
            return;
        }
        
        if(correo.length > 100){
            toastr.error("100 caracter máximo correo");
            return false;
        }

        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(correo)){
            // valido
        }else{
            toastr.error("El correo es invalido");
            return false;
        }

        if(tipovehiculo === ''){
            toastr.error("tipo vehiculo es requerido");
            return;
        }
        
        if(tipovehiculo.length > 50){
            toastr.error("50 caracter máximo tipo vehiculo");
            return false;
        }

        if(numerovehiculo === ''){
            toastr.error("numero vehiculo es requerido");
            return;
        }
        
        if(numerovehiculo.length > 50){
            toastr.error("50 caracter máximo numero vehiculo");
            return false;
        }

        if(licensia === ''){
            toastr.error("licensia es requerido");
            return;
        }
        
        if(licensia.length > 30){
            toastr.error("30 caracter máximo licensia");
            return false;
        }

        if(dui === ''){
            toastr.error("dui es requerido");
            return;
        }
        
        if(dui.length > 25){
            toastr.error("25 caracter máximo dui");
            return false;
        }
      
        if(imagen.files && imagen.files[0]){ // si trae imagen
            if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                return false;       
            } 
        }else{
            toastr.error("imagen es requerido");
            return false;
        }

        return true;
    }

 
    function informacion(id){
        spinHandle = loadingOverlay().activate();
        document.getElementById("formulario-editar").reset();

        axios.post('/admin/motoristas/informacion',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalEditar').modal('show');
                    $('#id-editar').val(response.data.motorista.id);
                    $('#nombre-editar').val(response.data.motorista.nombre);
                    $('#telefono-editar').val(response.data.motorista.telefono);
                    $('#correo-editar').val(response.data.motorista.correo);
                    $('#tipovehiculo-editar').val(response.data.motorista.tipo_vehiculo);
                    $('#numerovehiculo-editar').val(response.data.motorista.numero_vehiculo);
                    $('#licensia-editar').val(response.data.motorista.licensia);
                    $('#dui-editar').val(response.data.motorista.dui);
                    $('#fecha').val(response.data.motorista.fecha);
                    $('#img-imagen').prop("src","{{ url('storage/usuario') }}"+'/'+ response.data.motorista.imagen);

                    if(response.data.motorista.zona_pago == 0){
                        $("#zonapago-editar").prop("checked", false);
                    }else{
                        $("#zonapago-editar").prop("checked", true);
                    }

                    if(response.data.motorista.limite_ordenes == 0){
                        $("#limite-editar").prop("checked", false);
                    }else{
                        $("#limite-editar").prop("checked", true);
                    }

                    if(response.data.motorista.activo == 0){
                        $("#activo-editar").prop("checked", false);
                    }else{
                        $("#activo-editar").prop("checked", true);
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
    
    function editar(){
        var id = document.getElementById('id-editar').value;
        var nombre = document.getElementById('nombre-editar').value;
        var telefono = document.getElementById('telefono-editar').value;
        var correo = document.getElementById('correo-editar').value;
        var tipovehiculo = document.getElementById('tipovehiculo-editar').value;
        var numerovehiculo = document.getElementById('numerovehiculo-editar').value;
        var licensia = document.getElementById('licensia-editar').value;
        var dui = document.getElementById('dui-editar').value;
        var cbzona = document.getElementById('zonapago-editar').checked;
        var cblimite = document.getElementById('limite-editar').checked;
        var cbactivo = document.getElementById('activo-editar').checked;
        var imagen = document.getElementById('imagen-editar');

        var retorno = validarEditar(nombre, telefono, correo, tipovehiculo, numerovehiculo, licensia, dui, imagen);

        if(retorno){

            var cbzona_1 = 0;
            var cblimite_1 = 0;
            var cbactivo_1 = 0;

            if(cbzona){
                cbzona_1 = 1;
            }

            if(cblimite){
                cblimite_1 = 1;
            }

            if(cbactivo){
                cbactivo_1 = 1;
            }


            var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('telefono', telefono);
            formData.append('correo', correo);
            formData.append('tipovehiculo', tipovehiculo);
            formData.append('numerovehiculo', numerovehiculo);
            formData.append('licensia', licensia);
            formData.append('dui', dui);
            formData.append('imagen', imagen.files[0]);
            formData.append('cbzona', cbzona_1);
            formData.append('cblimite', cblimite_1);
            formData.append('cbactivo', cbactivo_1);

            axios.post('/admin/motoristas/editar', formData, {
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
        } else if (response.data.success == 1) {
            toastr.error('El correo ya esta registrado'); 
        } else if(response.data.success == 2){
            toastr.success('Actualizado');           
           
            var ruta = "{{ url('/admin/motoristas/tabla/lista') }}";
            $('#tablaDatatable').load(ruta);
            $('#modalEditar').modal('hide');  
            
        } else if(response.data.success == 3){
            toastr.error('imagen no subida');
        } else if(response.data.success == 4){
            toastr.error('ID no encontrado');
        }
        else {
            toastr.error('Error desconocido');
        }
    } 

     
    function validarEditar(nombre, telefono, correo, tipovehiculo, numerovehiculo, licensia, dui, imagen){
        if(nombre === ''){
                toastr.error("nombre es requerido");
                return;
            }
            
            if(nombre.length > 50){
                toastr.error("50 caracter máximo nombre");
                return false;
            }

            if(telefono === ''){
                toastr.error("telefono es requerido");
                return;
            }
            
            if(telefono.length > 20){
                toastr.error("20 caracter máximo telefono");
                return false;
            }

            if(correo === ''){
                toastr.error("correo es requerido");
                return;
            }
            
            if(correo.length > 100){
                toastr.error("100 caracter máximo correo");
                return false;
            }

            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(correo)){
            // valido
            }else{
                toastr.error("El correo es invalido");
                return false;
            }

            if(tipovehiculo === ''){
                toastr.error("tipo vehiculo es requerido");
                return;
            }
            
            if(tipovehiculo.length > 50){
                toastr.error("50 caracter máximo tipo vehiculo");
                return false;
            }

            if(numerovehiculo === ''){
                toastr.error("numero vehiculo es requerido");
                return;
            }
            
            if(numerovehiculo.length > 50){
                toastr.error("50 caracter máximo numero vehiculo");
                return false;
            }

            if(licensia === ''){
                toastr.error("licensia es requerido");
                return;
            }
            
            if(licensia.length > 30){
                toastr.error("30 caracter máximo licensia");
                return false;
            }

            if(dui === ''){
                toastr.error("dui es requerido");
                return;
            }
            
            if(dui.length > 25){
                toastr.error("25 caracter máximo dui");
                return false;
            }
        
            if(imagen.files && imagen.files[0]){ // si trae imagen
                if (!imagen.files[0].type.match('image/jpeg|image/jpeg|image/png')){      
                    toastr.error('Formato de imagen permitido: .png .jpg .jpeg');
                    return false;       
                } 
            }

            return true;
    }
  

    function modalPromedio(id){
        
    }


    function modalPromedio(id){
        spinHandle = loadingOverlay().activate();
        document.getElementById("formulario-promedio").reset();

        axios.post('/admin/motoristas/promedio',{
        'id': id 
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
                if(response.data.success == 1){
                    $('#modalPromedio').modal('show');

                    $('#promedio').val(response.data.promedio);

                } else if(response.data.success == 2){
                    toastr.success("Ninguna orden aun");
                }
                else{
                    toastr.error("ID no encontrado");
                }

                console.log(response);

            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle); 
                toastr.error('Error del servidor');    
        });
    }

    function promedioPorFecha(){
        var id = document.getElementById('id-editar').value;
        var fecha1 = document.getElementById('fecha1').value;
        var fecha2 = document.getElementById('fecha2').value;

        if(fecha1 === ''){
            toastr.error('Fecha desde es requerido');
            return;
        }

        if(fecha2 === ''){
            toastr.error('Fecha hasta es requerido');
            return;   
        }

        var spinHandle = loadingOverlay().activate();
            var formData = new FormData();
            formData.append('id', id);
            formData.append('fecha1', fecha1);
            formData.append('fecha2', fecha2);

            axios.post('/admin/motoristas/promediofecha', formData, {
            })
            .then((response) => {
                loadingOverlay().cancel(spinHandle);
               // respuestaPromedioFecha(response);
                 console.log(response);
            })
            .catch((error) => {
                loadingOverlay().cancel(spinHandle);
                toastr.error('Error');
            });
    }

    function respuestaPromedioFecha(response){
        console.log(response);
    }

    function promedioTotal(){
      
    }

  </script>
 


@stop