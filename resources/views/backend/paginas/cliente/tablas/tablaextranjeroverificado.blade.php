 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th style="width: 12%">Número</th>
                  <th style="width: 12%">Nombre</th>
                  <th style="width: 12%">Direccion</th>
                  <th style="width: 12%">Seleccionado</th>
                  <th style="width: 12%">Envío</th>
                  <th style="width: 12%">Motorista</th>
                  <th style="width: 12%">Hora inicio</th>
                  <th style="width: 12%">Hora fin</th>
                  <th style="width: 20%">Opciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cliente as $dato)
                <tr>
                  <td>{{ $dato->phone }}</td>
                  <td>{{ $dato->nombre }}</td>
                  <td>{{ $dato->direccion }}</td>
                  <td>{{ $dato->seleccionado }}</td>
                  <td>{{ $dato->precio_envio }}</td>
                    <td>{{ $dato->ganancia_motorista }}</td>
                    <td>{{ $dato->hora_inicio }}</td>
                    <td>{{ $dato->hora_fin }}</td>

                    <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Informacion"></i>&nbsp; Información
                    </button>
                  </td>

                </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language": {
        "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas"
        }
      });
    });
</script>
