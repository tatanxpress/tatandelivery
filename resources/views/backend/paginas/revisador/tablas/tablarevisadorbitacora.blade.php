 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 15%">Identificador revisador</th>   
                    <th style="width: 20%">Nombre revisador</th> 
                    <th style="width: 10%">Fecha inicio</th>        
                    <th style="width: 15%">Fecha final</th> 
                    <th style="width: 15%">Total $</th>
                    <th style="width: 15%">Confirmadas</th>
                    <th style="width: 15%">Opciones</th>
                </tr>
                </thead>
                <tbody> 
                @foreach($revisador as $dato)
                <tr>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->fecha1 }}</td>
                    <td>{{ $dato->fecha2 }}</td> 
                    <td>{{ $dato->total }}</td>
                    <td>{{ $dato->confirmadas }}</td>                    
                    <td>
                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                      <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                      </button> 
                    </td> 
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