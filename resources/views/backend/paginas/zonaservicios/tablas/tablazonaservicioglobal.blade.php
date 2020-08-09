 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                        <th style="width: 15%">Identificador</th>
                        <th style="width: 15%">Nombre</th>
                        <th style="width: 20%">En cuantas zonas esta</th>   
                        <th style="width: 20%">Esta Activo en Zonas</th>      
                </tr>
                </thead>
                <tbody>
                      @foreach($servicios as $dato)
                        <tr class="row1" data-id="{{ $dato->id }}">
                        
                        <td>{{ $dato->identificador }}</td> 
                        <td>{{ $dato->nombre }}</td>
                        <td>{{ $dato->cuantas }}</td>
                        <td>{{ $dato->activos }}</td>
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
        "pageLength": 100,
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