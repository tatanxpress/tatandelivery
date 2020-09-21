 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                  <th style="width: 12%">Nombre</th>
                  <th style="width: 12%">Numero</th>
                  <th style="width: 12%">Fecha Agrego Direc.</th>  
                  <th style="width: 12%">Area</th>  
                  <th style="width: 12%">Zona</th>  
                  <th style="width: 20%">Opciones</th>        
                </tr>
                </thead>
                <tbody>
                @foreach($datos as $dato)
                <tr>
                  <td>{{ $dato->name }}</td> 
                  <td>{{ $dato->phone }}</td>
                  <td>{{ $dato->fecha }}</td>
                  <td>{{ $dato->area }}</td>
                  <td>{{ $dato->identificador }}</td>
                  <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Informacion"></i>&nbsp; Información  
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