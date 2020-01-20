
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
                    <th style="width: 20%">Descripcion</th>                    
                    <th style="width: 10%">Tipo publicidad</th>  
                    <th style="width: 15%">Fecha inicio</th>
                    <th style="width: 15%">Fecha fin</th> 
                    <th style="width: 15%">Opciones</th>             
                </tr>
                </thead>
                <tbody> 
                @foreach($publicidad as $dato)
      
                <tr>
                    
                <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->descripcion }}</td>
                    <td> 
                      @if($dato->tipo_publicidad == 1)
                      <span class="badge bg-info">Promoción servicio</span>
                      @else
                      <span class="badge bg-warning">Publicidad cliente</span>
                      @endif
                    </td>
                    <td>{{ $dato->fecha_inicio }}</td>
                    <td>{{ $dato->fecha_fin }}</td>
                    <td>
                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                      <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
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