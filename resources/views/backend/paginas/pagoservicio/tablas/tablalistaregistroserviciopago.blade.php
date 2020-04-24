 

     <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 15%">IDenti</th> 
                    <th style="width: 15%">Nombre servicio</th>
                    <th style="width: 15%">Fecha ingreso</th>  
                    <th style="width: 15%">Fecha desde</th> 
                    <th style="width: 15%">Fecha hasta</th>
                    <th style="width: 15%">Descripción</th>
                    <th style="width: 15%">Pago</th>                    

                </tr> 
                </thead>
                <tbody> 
                @foreach($servicio as $dato)
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