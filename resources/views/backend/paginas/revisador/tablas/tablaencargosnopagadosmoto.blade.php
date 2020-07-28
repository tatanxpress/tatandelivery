  <!-- Main content -->
  <section class="content">
    <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
             <p>Total ordenes sin completar: <strong>{{ $sincompletar }} </strong></p>
             <p>Total dinero sin entregar: <strong> ${{ $suma }} </p>
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
                    <th style="width: 15%">Identificador Motorista</th>
                    <th style="width: 15%">Fecha Selecciono</th>                    
                    <th style="width: 15%">Total $</th> 
                </tr>
                </thead>
                <tbody> 
                @foreach($ordenid as $dato) 
      
                <tr>
                    <td>{{ $dato->ordenes_encargo_id }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->fecha_agarrada }}</td>                   
                    <td>${{ $dato->total }}</td>
                   
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