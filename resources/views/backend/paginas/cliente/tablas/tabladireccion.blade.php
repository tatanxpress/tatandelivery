 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                  <th style="width: 15%">Nombre</th>
                  <th style="width: 15%">Selecionado</th>
                  <th style="width: 15%">Zona identificador</th>
                  <th style="width: 15%">Opciones</th>            
                </tr>
                </thead>
                <tbody>
                @foreach($direccion as $dato)
                <tr>
                  <td>{{ $dato->nombre }}</td> 
                  <td> 
                    @if($dato->seleccionado == 0)
                    <span class="badge bg-danger">Inactivo</span>
                    @else
                    <span class="badge bg-success">Activo</span>
                    @endif                  
                  </td> 
                  <td>{{ $dato->nombreZona }}</td>
                                 
                  <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Informacion"></i>&nbsp; Información  
                    </button>
                    <button type="button" class="btn btn-info btn-xs" onclick="verMapa({{ $dato->id }})">
                      <i class="fas fa-map" title="Mapa"></i>&nbsp; Mapa
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