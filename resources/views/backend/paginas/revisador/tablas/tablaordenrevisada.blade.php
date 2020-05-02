  <!-- Main content -->
  <section class="content">
  <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
             <p>Dinero en caja: <strong>${{ $suma }} </strong></p>
          </div>
        </div>
      </div>
    </div>

      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 10%"># orden</th>
                    <th style="width: 15%">Identificador revisador</th>   
                    <th style="width: 20%">Nombre</th> 
                    <th style="width: 15%">Fecha orden</th> 
                    <th style="width: 15%">Fecha revisada</th>
                    <th style="width: 15%">Precio (Sub Total + Envio)</th>
                    <th style="width: 10%">Cupon</th>
                </tr>
                </thead>
                <tbody> 
                @foreach($orden as $dato)
      
                <tr>
                    <td>{{ $dato->ordenes_id }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->fecha_orden }}</td>
                    <td>{{ $dato->fecha }}</td>
                    <td>{{ $dato->precio }}</td>
                    <td>{{ $dato->usacupon }}</td>
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