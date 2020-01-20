 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                  <th style="width: 10%">Nombre</th>
                  <th style="width: 22%">Descripcion</th>
                  <th style="width: 10%">Saturación</th>
                  <th style="width: 10%">Estado</th>
                  <th style="width: 8%">Identificador</th>
                  <th style="width: 10%">Abierto</th>
                  <th style="width: 10%">Cerrado</th>                   
                  <th style="width: 20%">Opciones</th>            
                </tr>
                </thead>
                <tbody>
                @foreach($zonas as $dato)
                <tr>
                  <td>{{ $dato->nombre }}</td> 
                  <td>{{ $dato->descripcion }}</td> 
                  <td> 
                    @if($dato->saturacion == 0)
                    <span class="badge bg-danger">Desactivado</span>
                    @else
                    <span class="badge bg-success">Activado</span>
                    @endif                  
                  </td> 
                  <td> 
                    @if($dato->activo == 0)
                    <span class="badge bg-danger">Inactivo</span>
                    @else
                    <span class="badge bg-success">Activo</span>
                    @endif                  
                  </td> 
                  <td>{{ $dato->identificador }}</td> 
                  <td>{{ $dato->hora_abierto_delivery }}</td> 
                  <td>{{ $dato->hora_cerrado_delivery }}</td>
                  <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="verInformacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                    </button>

                    <button type="button" class="btn btn-success btn-xs" onclick="agregarPoligonos({{ $dato->id }})">
                    <i class="fa fa-location-arrow" title="Poligonos"></i>&nbsp; Poligonos
                    </button>

                    <button type="button" class="btn btn-warning btn-xs" onclick="verPoligono({{ $dato->id }})">
                    <i class="fa fa-location-arrow" title="Mapa"></i>&nbsp; Mapa
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