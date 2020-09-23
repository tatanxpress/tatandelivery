<!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th style="width: 10%">ID orden</th>
                    <th style="width: 15%">Cliente</th>
                    <th style="width: 15%">Identi</th>
                    <th style="width: 15%">Hora</th>
                    <th style="width: 15%">Nota</th>
                    <th style="width: 15%">Sub Total</th>
                    <th style="width: 15%">Zona</th>
                    <th style="width: 15%">Verificado</th>
                    <th style="width: 15%">Motorista</th>
                    <th style="width: 15%">Estado</th>

                </tr>
                </thead>
                <tbody>
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->cliente }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->fecha_orden }}</td>
                    <td>{{ $dato->nota_orden }}</td>
                    <td>{{ $dato->precio_total }}</td>
                    <td>{{ $dato->zonaidenti }}</td>
                    <td>{{ $dato->verificado }}</td>
                    <td>{{ $dato->motorista }}</td>
                    <td> {{ $dato->estado }}
                        @if($dato->estado_2 == 1 && $dato->estado_3 == 0 && $dato->estado_8 == 0)
                        </br></br>
                        <button type="button" class="btn btn-success btn-xs" onclick="modalIniciar({{ $dato->id }})">
                          <i class="fas fa-eye" title="Iniciar"></i>&nbsp; Iniciar
                          </button>
                        @endif
                        </br></br>
                        <button type="button" class="btn btn-info btn-xs" onclick="verProductos({{ $dato->id }})">
                          <i class="fas fa-eye" title="Productos"></i>&nbsp; Productos
                          </button>
                        <br><br>
                        <button type="button" class="btn btn-info btn-xs" onclick="infocliente({{ $dato->id }})">
                            <i class="fas fa-eye" title="Informacion"></i>&nbsp; Info Cliente
                        </button>
                        <br><br>
                        <button type="button" class="btn btn-info btn-xs" onclick="infoorden({{ $dato->id }})">
                            <i class="fas fa-eye" title="Informacion Orden"></i>&nbsp; Info Orden
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
        "pageLength": 100,
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
