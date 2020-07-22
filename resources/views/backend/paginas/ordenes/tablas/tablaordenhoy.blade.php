

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
                    <th style="width: 20%">ID Servicio</th> 
                    <th style="width: 15%">Nombre</th>
                    <th style="width: 15%">Hora</th>                     
                    <th style="width: 15%">Sub Total</th>
                    <th style="width: 15%">ID Zona</th>
                    <th style="width: 15%">Verificado</th>
                    <th style="width: 15%">Estado</th>
                                             
                </tr> 
                </thead>
                <tbody> 
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->fecha_orden }}</td>
                    <td>{{ $dato->precio_total }}</td>                   
                    <td>{{ $dato->zonaidenti }}</td>     
                    <td>{{ $dato->verificado }}</td>            
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