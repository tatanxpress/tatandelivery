@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />


@stop

<section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Dirección Extranjero</h1>
          </div>
      </div>
    </section>

<!-- seccion frame -->
<section class="content">
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header">
        <h3 class="card-title">Tabla de direcciones</h3>
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


<div class="modal fade" id="modal-info">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-info">
                    <div class="card-body">
                        <div class="col-md-12">

                          <div class="form-group">
                              <label style="color:#191818">Nombre</label>
                              <br>
                              <input type="hidden" id="id-editar">
                              <input type="text" maxlength="100" id="nombre" disabled  class="form-control"/>
                          </div>

                          <div class="form-group">
                              <label style="color:#191818">Dirección</label>
                              <br>
                              <input type="text" maxlength="400" id="direccion"  class="form-control">
                          </div>

                          <div class="form-group">
                              <label style="color:#191818"># Casa</label>
                              <br>
                              <input type="text" maxlength="30" id="numero"  class="form-control">
                          </div>
                          <div class="form-group">
                              <label style="color:#191818">Punto de referencia</label>
                              <br>
                              <input type="text" maxlength="400" id="referencia"  class="form-control">
                          </div>

                          <div class="form-group">
                              <label style="color:#191818">Latitud</label>
                              <br>
                              <input type="text" maxlength="50" id="latitud" class="form-control">
                          </div>

                          <div class="form-group">
                              <label style="color:#191818">Longitud </label>
                              <br>
                              <input type="text" maxlength="50" id="longitud" class="form-control">
                          </div>

                          <div class="form-group">
                              <label style="color:#191818">Latitud Real</label>
                              <br>
                              <input type="text" maxlength="50" id="latitud-real" class="form-control">
                          </div>

                          <div class="form-group">
                              <label style="color:#191818">Longitud Real</label>
                              <br>
                              <input type="text" maxlength="50" id="longitud-real" class="form-control">
                          </div>


                          <div class="form-group">
                              <label style="color:#191818">Cargo Envio</label>
                              <br>
                              <input type="number" value="0.00" step="0.01" id="cargo-envio" class="form-control">
                          </div>

                          <div class="form-group">
                              <label style="color:#191818">Ganancia Motorista</label>
                              <br>
                              <input type="number" value="0.00" step="0.01" id="ganancia-motorista" class="form-control">
                          </div>

                            <div class="form-group">
                                <label>Hora abierto</label>
                                <input type="time" class="form-control" id="horainicio-nuevo">
                            </div>
                            <div class="form-group">
                                <label>Hora cerrado</label>
                                <input type="time" class="form-control" id="horafin-nuevo">
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editarDireccion()">Guardar</button>
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

        var ruta = "{{ URL::to('admin/ver/toda/tabla/direccion-extranjero') }}";
        $('#tablaDatatable').load(ruta);
    });

 </script>

  <script>


    // aqui veremos todas las direcciones del usuario
    function informacion(id){

        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();
        formData.append('id', id);

        axios.post('/admin/informacion/direccion/extranjero', formData, {
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);

            if(response.data.success == 1){
                $('#modal-info').modal('show');
                $('#id-editar').val(response.data.direccion.id);
                $('#nombre').val(response.data.direccion.nombre);
                $('#direccion').val(response.data.direccion.direccion);
                $('#numero').val(response.data.direccion.numero_casa);
                $('#referencia').val(response.data.direccion.punto_referencia);

                $('#latitud').val(response.data.direccion.latitud);
                $('#longitud').val(response.data.direccion.longitud);

                $('#latitud-real').val(response.data.direccion.latitud_real);
                $('#longitud-real').val(response.data.direccion.longitud_real);

                $('#horainicio-nuevo').val(response.data.direccion.hora_inicio);
                $('#horafin-nuevo').val(response.data.direccion.hora_fin);

                $('#cargo-envio').val(response.data.direccion.precio_envio);
                $('#ganancia-motorista').val(response.data.direccion.ganancia_motorista);


            }else{
                toastr.error('Direccion no encontrada');
            }

        })
        .catch((error) => {
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
        });
    }


    function editarDireccion(){

        var id = document.getElementById('id-editar').value;

        var direccion = document.getElementById('direccion').value;
        var numero = document.getElementById('numero').value;
        var referencia = document.getElementById('referencia').value;

        var latitud = document.getElementById('latitud').value;
        var longitud = document.getElementById('longitud').value;
        var latitudreal = document.getElementById('latitud-real').value;
        var longitudreal = document.getElementById('longitud-real').value;

        // verificando la direccion del extranjero
        var cargo = document.getElementById('cargo-envio').value;
        var ganamotorista = document.getElementById('ganancia-motorista').value;

        var horainicio = document.getElementById('horainicio-nuevo').value;
        var horafin = document.getElementById('horafin-nuevo').value;

        if(horainicio === ''){
            toastr.error("hora inicio es requerido");
            return;
        }

        if(horafin === ''){
            toastr.error("hora fin es requerido");
            return;
        }

        if(cargo === ''){
            toastr.error("cargo envio es requerido");
            return;
        }

        if(ganamotorista === ''){
            toastr.error("ganancia motorista es requerido");
            return;
        }

        if(direccion === ''){
            toastr.error("direccion es requerido");
            return;
        }

        if(latitud === ''){
            toastr.error("latitud es requerido");
            return;
        }

        if(longitud === ''){
            toastr.error("longitud es requerido");
            return;
        }


        var spinHandle = loadingOverlay().activate();
        var formData = new FormData();

        formData.append('id', id);

        formData.append('direccion', direccion);
        formData.append('numero', numero);
        formData.append('referencia', referencia);
        formData.append('latitud', latitud);
        formData.append('longitud', longitud);
        formData.append('latitudreal', latitudreal);
        formData.append('longitudreal', longitudreal);
        formData.append('ganmotorista', ganamotorista);
        formData.append('cargoenvio', cargo);
        formData.append('horainicio', horainicio);
        formData.append('horafin', horafin);

        axios.post('/admin/cliente/actualizar/extranjero/direccionv2', formData, {
                })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);

                    if(response.data.success == 1){
                        toastr.success('Actualizado');
                        $('#modal-info').modal('hide');
                    }
                    else{
                        toastr.error('Error de validacion');
                    }

                })
                .catch((error) => {
                    loadingOverlay().cancel(spinHandle);
                    toastr.error('Error');
                });
    }


  </script>



@stop
