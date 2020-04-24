 

     <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 10%">ID motorista</th> 
                    <th style="width: 15%">Motorista</th> 
                    <th style="width: 10%">Fe. ingreso</th> 
                    <th style="width: 10%">Fe. desde</th> 
                    <th style="width: 10%">Fe. hasta</th> 
                    <th style="width: 15%">Descripción</th>     
                    <th style="width: 8%">Pago</th>                    

                </tr>  
                </thead>
                <tbody> 
                @foreach($moto as $dato)
                <tr>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->fecha }}</td>
                    <td>{{ $dato->fecha1 }}</td>
                    <td>{{ $dato->fecha2 }}</td>
                    <td>{{ $dato->descripcion }}</td>
                    <td>${{ $dato->pago }}</td>
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