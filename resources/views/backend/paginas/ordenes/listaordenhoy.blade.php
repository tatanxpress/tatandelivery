@extends('backend.menus.superior')

@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />

@stop

    <section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">
            <h1>Ordenes Hoy {{ $fecha }}</h1>
            <label id="total-hoy">Total Hoy $</label>
          </div>

          <button type="button" onclick="recargar()" class="btn btn-success btn-sm">
                <i class="fas fa-pencil-alt"></i>
                  Recargar
          </button>

          <div class="form-group" style="width: 25%">
              <label>Cronometro</label>
              <label id="contador"></label>
          </div>

      </div>
    </section>

  <!-- seccion frame -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Tabla de ordenes</h3>
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


  <!-- modal iniciar orden del cliente -->
<div class="modal fade" id="modalIniciar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Contestar orden</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-iniciar">
                    <div class="card-body">
                        <div class="row">

                            <div class="form-group">
                                <input type="hidden" id="id-editar">
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnGuardar" onclick="iniciar()">Iniciar</button>
            </div>
        </div>
    </div>
</div>


<!-- modal informacion de la orden-->
<div class="modal fade" id="modalInfoOrden">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion Orden</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-infoorden">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label>Nota orden</label>
                                    <input type="text" disabled class="form-control" id="notaorden">
                                </div>

                                <div class="form-group">
                                    <label>Cambio vuelto</label>
                                    <input type="text" disabled class="form-control" id="cambiovuelto">
                                </div>

                                <div class="form-group">
                                    <label>Estado 2 (propietario respondio con tiempo de espera)</label>
                                    <br>
                                    <input type="checkbox" id="estado2" disabled style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 2</label>
                                    <input type="text" disabled class="form-control" id="fecha2">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo dado por el propietario</label>
                                    <input type="text" disabled class="form-control" id="minutosespera">
                                </div>

                                <div class="form-group">
                                    <label>Tiempo extra de la zona</label>
                                    <input type="text" disabled class="form-control" id="minutosextra">
                                </div>

                                <div class="form-group">
                                    <label>Cliente espera en total (Minutos)</label>
                                    <input type="text" disabled class="form-control" id="minutostotal">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 3 (cliente responde, que esperara tiempo)</label>
                                    <br>
                                    <input type="checkbox" id="estado3" disabled style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 3</label>
                                    <input type="text" disabled class="form-control" id="fecha3">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 4 (propietario inicia la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado4" disabled  style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 4</label>
                                    <input type="text" disabled class="form-control" id="fecha4">
                                </div>

                                <div class="form-group">
                                    <label>Hora Estimada Entrega al Cliente (hora2 + tiempo extra)</label>
                                    <input type="text" disabled class="form-control" id="estimada">
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 5 (propietario finalizo de preparar la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado5" disabled  style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 5</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha5" disabled>
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 6 (Motorista va en camino a dejar la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado6" disabled style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 6</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha6" disabled>
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 7 (Motorista entrega la orden)</label>
                                    <br>
                                    <input type="checkbox" id="estado7" disabled style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 7</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha7" disabled>
                                </div>

                                <!-- -->

                                <div class="form-group">
                                    <label>Estado 8 (Orden fue cancelada)</label>
                                    <br>
                                    <input type="checkbox" id="estado8" disabled style="width: 20px; height: 20px;">
                                </div>

                                <div class="form-group">
                                    <label>Fecha estado 8</label>
                                    <br>
                                    <input type="text" disabled class="form-control" id="fecha8" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Mensaje estado 8 (cancelado)</label>
                                    <input type="text" disabled class="form-control" id="mensaje8">
                                </div>

                                <div class="form-group">
                                    <label>Cancelado por</label>
                                    <input type="text" disabled class="form-control" id="canceladapor">
                                </div>

                                <div class="form-group">
                                    <label>Cancelado orden (Cancelacion desde panel del control)</label>
                                    <input type="text" disabled class="form-control" id="ordencancelada">
                                </div>

                                <div class="form-group">
                                    <label>Copia precio Zona para este servicio</label>
                                    <input type="text" disabled class="form-control" id="preciozona">
                                </div>

                                <div class="form-group">
                                    <label>Ganancia de motorista</label>
                                    <input type="text" disabled class="form-control" id="gananciamotorista">
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

<!-- modal informacion del cliente-->
<div class="modal fade" id="modalInfoCliente">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Informacion del cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulario-infocliente">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <input type="hidden" id="id-orden">
                                </div>

                                <div class="form-group">
                                    <label>Nombre Cliente</label>
                                    <input type="text" disabled class="form-control" id="nombrecliente">
                                </div>

                                <div class="form-group">
                                    <label>Zona identificador</label>
                                    <input type="text" disabled class="form-control" id="zonaidentificador">
                                </div>

                                <div class="form-group">
                                    <label>Nombre Zona</label>
                                    <input type="text" disabled class="form-control" id="nombrezona">
                                </div>

                                <div class="form-group">
                                    <label>Direccion</label>
                                    <input type="text" disabled class="form-control" id="direccion">
                                </div>

                                <div class="form-group">
                                    <label>Numero de casa</label>
                                    <input type="text" disabled class="form-control" id="numerocasa">
                                </div>

                                <div class="form-group">
                                    <label>Punto de referencia</label>
                                    <input type="text" disabled class="form-control" id="puntoreferencia">
                                </div>

                                <div class="form-group">
                                    <label>Telefono Registrado en la App</label>
                                    <input type="text" disabled class="form-control" id="telefonoreal">
                                </div>


                                <div class="form-group">
                                    <label>Pidio desde</label>
                                    <input type="text" disabled class="form-control" id="versionapp">
                                </div>

                                <div class="form-group">
                                    <label>Latitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="latitud">
                                </div>

                                <div class="form-group">
                                    <label>Longitud Direccion</label>
                                    <input type="text" disabled class="form-control" id="longitud">
                                </div>



                                <button type="button" onclick="mapa()" class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                    Mapa
                                </button>

                                <div class="form-group">
                                    <label>Latitud Real</label>
                                    <input type="text" disabled class="form-control" id="latitudreal">
                                </div>

                                <div class="form-group">
                                    <label>Longitud real</label>
                                    <input type="text" disabled class="form-control" id="longitudreal">
                                </div>



                                <button type="button" onclick="mapa2()" class="btn btn-success btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                    Mapa
                                </button>

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
        var ruta = "{{ URL::to('admin/control/tabla/ordeneshoy') }}";
        $('#tablaDatatable').load(ruta);

        var total = {{$total}};

        document.getElementById('total-hoy').innerHTML = 'Total Hoy $'+total;

      countdown();
    });

 </script>

 <script>

  function recargar(){
    var ruta = "{{ url('/admin/control/tabla/ordeneshoy') }}";
    $('#tablaDatatable').load(ruta);

    // traer total de dinero de ordenes completadas
    axios.post('/admin/control/total/de/ventas-hoy', {
      'id':0
      })
      .then((response) => {
          if(response.data.success == 1){

            document.getElementById('total-hoy').innerHTML = 'Total Hoy $'+response.data.total;
          }
          else{
              toastr.error('Error al obtener Total $');
          }
      })
      .catch((error) => {
          toastr.error('Error');
      });
  }

  function countdown() {
    var seconds = 60;
    function tick() {
        var counter = document.getElementById("contador");
        seconds--;
        counter.innerHTML = "0:" + (seconds < 10 ? "0" : "") + String(seconds);
        if( seconds > 0 ) {
            setTimeout(tick, 1000);
        } else {
            recargar();
            countdown();
        }
    }
    tick();
  }


  // iniciar una orden que el cliente no contesta
  function modalIniciar(id){
        $('#modalIniciar').modal('show');
        $('#id-editar').val(id);
  }

  function iniciar(){
    var id = document.getElementById('id-editar').value; // id de orden

    spinHandle = loadingOverlay().activate();

    var formData = new FormData();
    formData.append('ordenid', id);

    axios.post('/api/usuario/proceso/orden/estado-3',formData,{
        })
        .then((response) => {
            loadingOverlay().cancel(spinHandle);
            toastr.success('Realizado');
            $('#modalIniciar').modal('hide');

            recargar();
    })
    .catch((error) => {
        loadingOverlay().cancel(spinHandle);
        toastr.error('Error del servidor');
    });
  }

  // vista de productos
  function verProductos(id){

    window.open("{{ URL::to('admin/productos/orden/nueva') }}/" + id);

  }

  function infocliente(id){
      document.getElementById("formulario-infocliente").reset();
      spinHandle = loadingOverlay().activate();

      axios.post('/admin/buscar/orden/infocliente',{
          'id': id
      })
          .then((response) => {
              loadingOverlay().cancel(spinHandle);

              if(response.data.success == 1){

                  $('#modalInfoCliente').modal('show');
                  var datos = response.data.orden;

                  datos.forEach(function(value, index) {

                      // informacion del cliente
                      $('#id-orden').val(datos[index].id);

                      $('#nombrecliente').val(datos[index].nombre);
                      $('#zonaidentificador').val(datos[index].identificador);
                      $('#nombrezona').val(datos[index].nombrezona);
                      $('#direccion').val(datos[index].direccion);
                      $('#numerocasa').val(datos[index].numero_casa);
                      $('#puntoreferencia').val(datos[index].punto_referencia);

                      if(datos[index].movil_ordeno == 0){
                          $('#versionapp').val("No ha actualizado");
                      }
                      else if(datos[index].movil_ordeno == 1){
                          $('#versionapp').val("Iphone");
                      }else if(datos[index].movil_ordeno == 2){
                          $('#versionapp').val("Android");
                      }else{
                          $('#versionapp').val(datos[index].movil_ordeno);
                      }

                      $('#latitud').val(datos[index].latitud);
                      $('#longitud').val(datos[index].longitud);
                      $('#latitudreal').val(datos[index].latitud_real);
                      $('#longitudreal').val(datos[index].longitud_real);
                      $('#copiaenvio').val(datos[index].copia_envio);
                      $('#telefonoreal').val(datos[index].phone);

                  });

              }else{
                  toastr.error('No encontrada');
              }
          })
          .catch((error) => {
              loadingOverlay().cancel(spinHandle);
              toastr.error('Error del servidor');
          });
  }

  function infoorden(id){
      document.getElementById("formulario-infoorden").reset();
      spinHandle = loadingOverlay().activate();

      axios.post('/admin/buscar/orden/infoorden',{
          'id': id
      })
          .then((response) => {
              loadingOverlay().cancel(spinHandle);

              if(response.data.success == 1){

                  $('#modalInfoOrden').modal('show');
                  var datos = response.data.orden;

                  datos.forEach(function(value, index) {

                      $('#notaorden').val(datos[index].nota_orden);
                      $('#cambiovuelto').val(datos[index].cambio);
                      $('#minutosespera').val(datos[index].hora_2);
                      $('#minutosextra').val(datos[index].copia_tiempo_orden);
                      $('#minutostotal').val(datos[index].minutostotal);

                      if(datos[index].cancelado_extra == 1){
                          $('#ordencancelada').val("Si");
                      }else{
                          $('#ordencancelada').val("No");
                      }

                      if(datos[index].estado_2 == 0){
                          $("#estado2").prop("checked", false);
                      }else{
                          $("#estado2").prop("checked", true);
                          $('#fecha2').val(datos[index].fecha_2);
                      }

                      if(datos[index].estado_3 == 0){
                          $("#estado3").prop("checked", false);
                      }else{
                          $("#estado3").prop("checked", true);
                          $('#fecha3').val(datos[index].fecha_3);
                      }

                      if(datos[index].estado_4 == 0){
                          $("#estado4").prop("checked", false);
                      }else{
                          $("#estado4").prop("checked", true);
                          $('#fecha4').val(datos[index].fecha_4);
                          $('#estimada').val(datos[index].estimada);
                      }

                      if(datos[index].estado_5 == 0){
                          $("#estado5").prop("checked", false);
                      }else{
                          $("#estado5").prop("checked", true);
                          $('#fecha5').val(datos[index].fecha_5);
                      }

                      if(datos[index].estado_6 == 0){
                          $("#estado6").prop("checked", false);
                      }else{
                          $("#estado6").prop("checked", true);
                          $('#fecha6').val(datos[index].fecha_6);
                      }

                      if(datos[index].estado_7 == 0){
                          $("#estado7").prop("checked", false);
                      }else{
                          $("#estado7").prop("checked", true);
                          $('#fecha7').val(datos[index].fecha_7);
                      }

                      if(datos[index].estado_8 == 0){
                          $("#estado8").prop("checked", false);
                      }else{
                          $("#estado8").prop("checked", true);
                          $('#fecha8').val(datos[index].fecha_8);
                          $('#mensaje8').val(datos[index].mensaje_8);
                      }



                      $('#canceladapor').val(datos[index].canceladopor);

                      if(datos[index].cancelado_extra == 1){
                          $('#ordencancelada').val("CANCELADO");
                          $('#mensaje8').val(datos[index].mensaje_8);
                      }



                      $('#preciozona').val(datos[index].copia_envio);
                      $('#gananciamotorista').val(datos[index].ganancia_motorista);




                  });

              }else{
                  toastr.error('No encontrada');
              }
          })
          .catch((error) => {
              loadingOverlay().cancel(spinHandle);
              toastr.error('Error del servidor');
          });
  }

  // latitud y longitud del puntero gps
  function mapa(){
      var id = document.getElementById('id-orden').value;

      // comprobar que latitud y longitud no esten vacios
      var la = document.getElementById('latitud').value;
      var lo = document.getElementById('longitud').value;

      if(la === '' || lo === ''){
          toastr.error('Latitud o Longitud estan vacios');
          return;
      }

      window.open("{{ URL::to('admin/mapa/orden/cliente/direccion') }}/" + id);
  }

  // latitud y longitud real donde guardo la direccion
  function mapa2(){
      var id = document.getElementById('id-orden').value;

      var la = document.getElementById('latitudreal').value;
      var lo = document.getElementById('longitudreal').value;

      if(la === '' || lo === ''){
          toastr.error('Latitud o Longitud estan vacios');
          return;
      }
      window.open("{{ URL::to('admin/mapa/orden/cliente/direccion-real') }}/" + id);
  }

 </script>


@stop
