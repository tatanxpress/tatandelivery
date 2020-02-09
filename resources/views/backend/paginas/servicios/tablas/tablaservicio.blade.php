 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                  <th style="width: 8%">ID</th>
                  <th style="width: 10%">Nombre</th>
                  <th style="width: 15%">Descripcion</th>
                  <th style="width: 15%">Identificador</th>
                  <th style="width: 11%">Cerrado Emergencia</th>
                  <th style="width: 10%">Activo</th>
                  <th style="width: 15%">Nombre Servicio</th>
                  <th style="width: 20%">Opciones</th>            
                </tr>
                </thead>
                <tbody>
                @foreach($servicio as $dato)
                <tr>
                  <td>{{ $dato->id }}</td> 
                  <td>{{ $dato->nombre }}</td> 
                  <td>{{ $dato->descripcion }}</td> 
                  <td>{{ $dato->identificador }}</td> 
                  <td> 
                    @if($dato->cerrado_emergencia == 0)
                    <span class="badge bg-danger">Desactivado</span>
                    @else
                    <span class="badge bg-success">Activado</span>
                    @endif                  
                  </td> 
                  <td> 
                    @if($dato->activo == 0)
                    <span class="badge bg-danger">Desactivado</span>
                    @else
                    <span class="badge bg-success">Activado</span>
                    @endif
                  </td>
                  <td>{{ $dato->nombreServicio }}</td> 
                 
                  <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="verModales({{ $dato->id }})">
                    <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                    </button>
                    @can('completo')
                      <button type="button" class="btn btn-primary btn-xs" onclick="verMapa({{ $dato->id }})">
                      <i class="fas fa-eye" title="Mapa"></i>&nbsp; Mapa
                      </button> 

                      <button type="button" class="btn btn-primary btn-xs" onclick="verCategorias({{ $dato->id }})">
                      <i class="fas fa-eye" title="Mapa"></i>&nbsp; Categorias
                      </button>
                    @endcan

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