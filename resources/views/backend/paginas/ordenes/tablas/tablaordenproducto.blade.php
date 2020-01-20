 

     <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 15%">Producto ID</th> 
                    <th style="width: 15%">Producto nombre</th> 
                    <th style="width: 15%">Producto descripcion</th> 
                    <th style="width: 15%">Imagen</th>   
                    <th style="width: 15%">Nota</th>                    
                    <th style="width: 15%">Precio Unidad</th>  
                    <th style="width: 15%">Cantidad</th>
                    <th style="width: 15%">Total</th>
                        
                </tr> 
                </thead>
                <tbody> 
                @foreach($producto as $dato)
                <tr>
                    <td>{{ $dato->productoid }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->descripcion }}</td>
                    @if($dato->utiliza_imagen == 0)
                    <td><center><img alt="producto" src="{{ url('storage/productos/imagendefault.jpg') }}" width="100px" height="100px" /></center></td>
                    @else
                    <td><center><img alt="producto" src="{{ url('storage/productos/'.$dato->imagen) }}" width="100px" height="100px" /></center></td>
                    @endif

                    <td>{{ $dato->nota }}</td>
                    <td>${{ $dato->preciounidad }}</td>
                    <td>{{ $dato->cantidad }}</td>
                    <td>${{ $dato->multiplicado }}</td>
 
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