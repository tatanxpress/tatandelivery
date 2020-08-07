

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
                    <th style="width: 15%">Cliente</th> 
                    <th style="width: 15%">Servicio</th>
                    <th style="width: 15%">Hora</th>                     
                    <th style="width: 15%">Sub Total</th>
                    <th style="width: 15%">Zona</th>
                    <th style="width: 15%">Verificado</th>
                    <th style="width: 15%">Motorista</th>
                    <th style="width: 15%">Estado</th>
                                             
                </tr> 
                </thead>
                <tbody> 
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->cliente }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->fecha_orden }}</td>
                    <td>{{ $dato->precio_total }}</td>                   
                    <td>{{ $dato->zonaidenti }}</td>     
                    <td>{{ $dato->verificado }}</td> 
                    <td>{{ $dato->motorista }}</td>            
                    <td>{{ $dato->estado }}</td>
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
        "pageLength": 100,
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