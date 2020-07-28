

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
                <th style="width: 15%">Identificador</th>
                <th style="width: 15%">Nombre</th>
                <th style="width: 15%">Inicia</th> 
                <th style="width: 10%">Termina</th>  
                <th style="width: 10%">Servicio</th>  
                <th style="width: 15%">Opciones</th>            
                </tr>
                </thead>
                <tbody>  
                @foreach($encargos as $dato)
      
                <tr>
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->identificador }}</td>
                <td>{{ $dato->nombre }}</td>
                <td>{{ $dato->fecha_inicia }}</td>
                <td>{{ $dato->fecha_finaliza }}</td>

                @if($dato->tengo == 0)
                <td></td>  
                @else
                <td><span class="badge bg-primary"> {{ $dato->servicio }} </span></td>  
                @endif   
               
                  <td> 
                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                      <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                      </button>  

                     
                      <button type="button" class="btn btn-primary btn-xs" onclick="zonas({{ $dato->id }})">
                      <i class="fas fa-eye" title="Zonas"></i>&nbsp; Zonas
                      </button>       

                      </br>
                      </br> 
 
                      <button type="button" class="btn btn-success btn-xs" onclick="lista({{ $dato->id }})">
                      <i class="fas fa-eye" title="Lista"></i>&nbsp; Lista
                      </button>     
                     
                      <button type="button" class="btn btn-success btn-xs" onclick="ordenes({{ $dato->id }})">
                      <i class="fas fa-eye" title="Ordenes"></i>&nbsp; Ordenes
                      </button> 

                      </br>
                      </br>
                      <button type="button" class="badge bg-purple" onclick="asignarMotorista({{ $dato->id }})">
                      <i class="fas fa-eye" title="Asignar Motorista"></i>&nbsp; Asignar Motorista
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