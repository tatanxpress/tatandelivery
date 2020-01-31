
     <!-- Main content -->
     <section class="content">

      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 15%">Orden #</th> 
                    <th style="width: 15%">Total orden</th>
                    <th style="width: 15%">Fecha orden</th> 
                    <th style="width: 15%">Identi servicio</th> 
                    <th style="width: 15%">Estado 7 (completado)</th>   
                    <th style="width: 10%">Estado 8 (cancelado)</th>                 
                    <th style="width: 15%">Opciones</th>            
                </tr> 
                </thead>
                <tbody>  
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->idorden }}</td>
                    <td>{{ $dato->precio_total }}</td>
                    <td>{{ $dato->fecha_orden }}</td>
                    <td>{{ $dato->identiservicio }}</td>

                    <td> 
                      @if($dato->estado_7 == 0)
                      <span class="badge bg-danger">No</span>
                      @else
                      <span class="badge bg-primary">Completado</span>
                      @endif
                    </td>

                    <td> 
                      @if($dato->estado_8 == 0)
                      <span class="badge bg-primary">No</span>
                      @else
                      <span class="badge bg-danger">Cancelado</span>
                      @endif
                    </td>

                    <td>
                      <button type="button" class="btn btn-info btn-xs" onclick="informacion({{ $dato->idorden }})">
                      <i class="fas fa-eye" title="Informacion"></i>&nbsp; Info
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