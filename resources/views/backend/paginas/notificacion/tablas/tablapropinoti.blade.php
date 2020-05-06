
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
                    <th style="width: 15%">Telefono</th>
                    <th style="width: 15%">Disponibilidad</th> 
                    <th style="width: 15%">Activo</th>   
                    <th style="width: 20%">ID Noti</th>        
                    <th style="width: 15%">Opcion</th>              
                </tr> 
                </thead>
                <tbody>  
                @foreach($noti as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->telefono }}</td>
                    <td>{{ $dato->disponibilidad }}</td>
                    <td>{{ $dato->activo }}</td>
                    <td>{{ $dato->device_id }}</td>

                    <td>
                      <button type="button" class="btn btn-primary btn-xs" onclick="modalNoti({{ $dato->id }})">
                      <i class="fas fa-eye" title="Notificacion"></i>&nbsp; Noti
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