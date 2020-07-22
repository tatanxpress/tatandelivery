

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
                <th style="width: 15%">Cliente</th>
                <th style="width: 15%">Fecha Orden</th>              
                <th style="width: 15%">Precio SubTotal</th>
                <th style="width: 15%">Estado</th>
                <th style="width: 15%">Opciones</th>
                </tr>
                </thead>
                <tbody> 
                @foreach($ordenes as $dato)
      
                <tr>
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->nombre }}</td>
                <td>{{ $dato->fecha_orden }}</td>
                <td>{{ $dato->precio_subtotal }}</td>

                @if($dato->revisado == 1)
                    <td><span class="badge bg-warning">Pendiente</span></td>
                    @elseif($dato->revisado == 2)
                    <td><span class="btn btn-primary btn-xs">En Proceso</span></td>
                    @elseif($dato->revisado == 3)
                    <td><span class="badge bg-purple">En Entrega</span></td> 
                    @elseif($dato->revisado == 4)
                    <td><span class="badge bg-green">Entregado</span></td>  
                    @elseif($dato->revisado == 5)
                    <td><span class="badge bg-red">Cancelado</span>
                      </br>
                        <label>{{ $dato->mensaje_cancelado }} </label>
                    </td>
                @endif
 
                <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="modalInformacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Info"></i>&nbsp; Info
                    </button> 

                     <button type="button" class="btn btn-primary btn-xs" onclick="productos({{ $dato->id }})">
                    <i class="fas fa-eye" title="Productos"></i>&nbsp; Productos
                    </button>

                      </br>
                      </br>

                      <button type="button" class="btn btn-danger btn-xs" onclick="modalCancelar({{ $dato->id }})">
                      <i class="fas fa-eye" title="Cancelar"></i>&nbsp; Cancelar
                      </button>  
                    
                    @if($dato->revisado == 1)
                      <button type="button" class="btn btn-success btn-xs" onclick="modalConfirmar({{ $dato->id }})">
                      <i class="fas fa-eye" title="Confirmar"></i>&nbsp; Confirmar
                      </button>     
                    @endif   
                    <br/>
                    <br/>
                    <button type="button" class="badge bg-purple" onclick="modalMotorista({{ $dato->id }})">
                      <i class="fas fa-eye" title="Motorista"></i>&nbsp; Motorista
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