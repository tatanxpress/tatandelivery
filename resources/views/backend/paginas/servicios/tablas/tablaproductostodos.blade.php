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
                    <th style="width: 15%">Categoria</th>
                    <th style="width: 10%">Precio</th>
                    <th style="width: 15%">Disponibilidad</th>
                    <th style="width: 10%">Activo</th>
                    <th style="width: 15%">Usa Cantidad</th>
                    <th style="width: 10%">Promocion</th>
                    <th style="width: 10%">Imagen</th> 
                </tr>
                </thead>
                <tbody>
                @foreach($producto as $dato)
                <tr>
                <td>{{ $dato->id }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->categoria }}</td>
                    <td>{{ $dato->precio }}</td>
                    <td> 
                      @if($dato->disponibilidad == 0)
                      <span class="badge bg-danger">No disponible</span>
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
                    <td> 
                      @if($dato->utiliza_cantidad == 0)
                      <span class="badge bg-danger">No</span>
                      @else
                      <span class="badge bg-success">Si</span>
                      @endif
                    </td> 

                    <td> 
                      @if($dato->es_promocion == 0)
                      <span class="badge bg-danger">No</span>
                      @else
                      <span class="badge bg-success">Si</span>
                      @endif
                    </td>

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
  