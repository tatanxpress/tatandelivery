

     <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                <th style="width: 10%">ID</th>
                <th style="width: 15%">Nombre</th>
                <th style="width: 15%">Descripcion</th>
                <th style="width: 15%">Imagen</th> 
                <th style="width: 15%">Nota</th>
                <th style="width: 10%">Cantidad</th>                
                <th style="width: 15%">Unidad</th>
                <th style="width: 15%">Total</th>   
                </tr>
                </thead>
                <tbody> 
                @foreach($producto as $dato)
      
                <tr>
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->nombre }}</td>
                <td>{{ $dato->descripcion }}</td>
                <td><center><img alt="producto" src="{{ url('storage/productos/'.$dato->imagen) }}" width="100px" height="100px" /></center></td>

                <td>{{ $dato->nota }}</td>
                <td>{{ $dato->cantidad }}</td>
                <td>{{ $dato->precio }}</td>
                <td>{{ $dato->multiplicado }}</td>

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