 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr> 
                  <th style="width: 15%">Teléfono</th>
                  <th style="width: 20%">Código</th>
                  <th style="width: 10%">Contador</th>
                </tr>
                </thead>
                <tbody>
                @foreach($codigo as $dato)
                <tr>
                  <td>{{ $dato->telefono }}</td> 
                  <td>{{ $dato->codigo }}</td>
                  <td>{{ $dato->contador }}</td>
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