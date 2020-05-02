 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 10%">ID</th>   
                    <th style="width: 15%">Minimo</th> 
                    <th style="width: 15%">Texto</th>        
                    <th style="width: 10%">Uso Limite</th> 
                    <th style="width: 10%">Contador</th>  
                    <th style="width: 10%">Fecha</th> 
                    <th style="width: 10%">Activo</th>               
                    <th style="width: 15%">Opciones</th>   
                </tr>
                </thead>
                <tbody> 
                @foreach($cupones as $dato)
      
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->dinero }}</td>
                    <td>{{ $dato->texto_cupon }}</td>
                    <td>{{ $dato->uso_limite }}</td>
                    <td>{{ $dato->contador }}</td>
                    <td>{{ $dato->fecha }}</td>
                    @if($dato->activo == 0)
                    <td><center>No</center></td>
                    @else
                    <td><center>Si</center></td>
                    @endif
                  
                    <td>

                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                        <i class="fas fa-eye" title="Información"></i>&nbsp; Información
                      </button> 

                      <button type="button" class="btn btn-primary btn-xs" onclick="usos({{ $dato->id }})">
                        <i class="fas fa-eye" title="Usos"></i>&nbsp; Usos
                      </button>

                      <button type="button" class="btn btn-warning btn-xs" onclick="editar({{ $dato->id }})">
                        <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                      </button> 
  
                      @if($dato->activo == 0)
                        <button type="button" class="btn btn-success btn-xs" onclick="activar({{ $dato->id }})">
                          <i class="fas fa-eye" title="Activar"></i>&nbsp; Activar
                        </button>                                           
                      @else
                      <button type="button" class="btn btn-danger btn-xs" onclick="desactivar({{ $dato->id }})">
                        <i class="fas fa-eye" title="Desactivar"></i>&nbsp; Desactivar
                      </button>
                      @endif
                      
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