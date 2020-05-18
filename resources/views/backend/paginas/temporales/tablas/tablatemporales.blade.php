 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                  <th style="width: 15%">ID</th>
                  <th style="width: 10%">Area</th>
                  <th style="width: 10%">Número</th>
                  <th style="width: 10%">Código</th>
                  <th style="width: 10%">Código Fijo</th>
                  <th style="width: 10%">Contador</th>
                  <th style="width: 10%">Fecha</th>
                  <th style="width: 15%">Opciones</th>            
                </tr>
                </thead>
                <tbody>
                @foreach($registro as $dato)
                <tr>
                  <td>{{ $dato->id }}</td> 
                  <td>{{ $dato->area }}</td> 
                  <td>{{ $dato->numero }}</td>
                  <td>{{ $dato->codigo }}</td>
                  <td>{{ $dato->codigo_fijo }}</td>
                  <td>{{ $dato->contador }}</td>
                  <td>{{ $dato->fecha }}</td>
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