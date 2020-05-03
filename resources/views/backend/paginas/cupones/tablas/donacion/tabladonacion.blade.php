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
                    <th style="width: 15%">Texto</th>
                    <th style="width: 15%">Institucion</th>  
                    <th style="width: 15%">Donacion</th>          
                    <th style="width: 10%">Uso Limite</th> 
                    <th style="width: 10%">Contador</th>  
                    <th style="width: 10%">Fecha</th> 
                    <th style="width: 10%">Activo</th>  
                    <th style="width: 10%">Ilimitado</th>             
                    <th style="width: 15%">Opciones</th>     
                </tr>
                </thead>
                <tbody> 
                @foreach($donacion as $dato)
      
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->texto_cupon }}</td>
                    <td>{{ $dato->institucion }}</td>
                    <td>{{ $dato->dinero }}</td>
                    <td>{{ $dato->uso_limite }}</td>
                    <td>{{ $dato->contador }}</td>
                    <td>{{ $dato->fecha }}</td>
                    <td>{{ $dato->activo }}</td>
                    @if($dato->ilimitado == 1)
                    <td>Si</td>
                    @else
                    <td>No</td>
                    @endif
                    <td>

                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                        <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                      </button> 
                      
                      <button type="button" class="btn btn-primary btn-xs" onclick="usos({{ $dato->id }})">
                        <i class="fas fa-eye" title="Usos"></i>&nbsp; Usos
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