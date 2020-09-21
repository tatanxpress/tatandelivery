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
                    <th style="width: 15%">Cantidad</th>
                    <th style="width: 10%">Precio</th>
                    <th style="width: 10%">Total</th>
                    <th style="width: 10%">Nota</th>
                    <th style="width: 10%">Imagen</th> 
                </tr>
                </thead>
                <tbody>
                @foreach($lista as $dato)
                <tr>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->cantidad }}</td>
                    <td>{{ $dato->precio }}</td>
                    <td>{{ $dato->total }}</td>
                    <td>{{ $dato->nota }}</td>

                    <td><center><img alt="Servicios" src="{{ url('storage/productos/'.$dato->imagen) }}" width="150px" height="150px" /></center></td>

                  
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
        "pageLength": 5000,
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
  