 

     <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 15%">ID orden</th> 
                    <th style="width: 15%">Identificador motorista</th> 
                    <th style="width: 15%">Nombre</th> 
                    <th style="width: 15%">Fecha</th>   
           
                </tr> 
                </thead>
                <tbody> 
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->ordenes_id }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->fecha_agarrada }}</td>

                
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