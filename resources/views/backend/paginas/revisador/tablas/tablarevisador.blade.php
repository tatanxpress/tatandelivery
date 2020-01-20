 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 15%">Identificador</th>   
                    <th style="width: 20%">Nombre</th> 
                    <th style="width: 10%">Teléfono</th>        
                    <th style="width: 15%">Disponible</th> 
                    <th style="width: 15%">Activo</th>
                    <th style="width: 15%">Opciones</th>   

                </tr>
                </thead>
                <tbody> 
                @foreach($revisador as $dato)
      
                <tr>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->telefono }}</td>
                    <td> 
                      @if($dato->disponible == 0)
                      <span class="badge bg-danger">No disponible</span>
                      @else
                      <span class="badge bg-primary">Disponible</span>
                      @endif
                    </td>
                    <td> 
                      @if($dato->activo == 0)
                      <span class="badge bg-danger">Inactivo</span>
                      @else
                      <span class="badge bg-primary">Activo</span>
                      @endif 
                    </td>
                    <td>
                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                      <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                      </button> 

                      <button type="button" class="btn btn-primary btn-xs" onclick="modalReseteo({{ $dato->id }})">
                      <i class="fas fa-eye" title="Resetear"></i>&nbsp; Resetear contraseña
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