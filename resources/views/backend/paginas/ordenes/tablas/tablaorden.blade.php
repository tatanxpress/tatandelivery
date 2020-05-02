

     <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 10%">ID orden</th> 
                    <th style="width: 20%">Identificador servicio</th> 
                    <th style="width: 15%">Precio total</th>                     
                    <th style="width: 15%">Fecha orden</th>
                    <th style="width: 15%">Estado</th>
                    <th style="width: 10%">Cupon</th>
                                             
                </tr>
                </thead>
                <tbody> 
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->precio_total }}</td>
                    <td>{{ $dato->fecha_orden }}</td>
                    @if($dato->estado_7 == 0)
                    <td>Orden en proceso</td>
                    @elseif($dato->estado_7 == 1)
                    <td>Orden completada</td>
                    @elseif($dato->estado_8 == 1)
                    <td>Orden Cancelada</td>              
                    @endif
                    <td>{{ $dato->cupon }}</td>
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