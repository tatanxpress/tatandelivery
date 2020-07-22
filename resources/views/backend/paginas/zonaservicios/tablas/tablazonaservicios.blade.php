 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr> 
                  <th style="width: 15%">Zona identificador</th>
                  <th style="width: 20%">Servicio identificador</th>
                  <th style="width: 10%">Activo</th>
                  <th style="width: 15%">Precio envío</th>  
                  <th style="width: 10%">Servicio</th>
                  <th style="width: 15%">Ganancia Motorista</th>   
                  <th style="width: 10%">Envio Gratis</th>     
                  <th style="width: 10%">Mitad Precio</th> 
                  <th style="width: 12%">Min envio gratis</th>         
                  <th style="width: 20%">Opciones</th>            
                </tr>
                </thead>
                <tbody>
                @foreach($servicio as $dato)
                <tr>
                  <td>{{ $dato->identificador }}</td>
                  <td>{{ $dato->idenServicio }}</td>
                  <td> 
                    @if($dato->activo == 0)
                    <span class="badge bg-danger">Inactivo</span>
                    @else
                    <span class="badge bg-success">Activo</span> 
                    @endif                  
                  </td>                                 
                  <td>{{ $dato->precio_envio }}</td>
                  <td> 
                    @if($dato->privado == 0)
                    <span class="badge bg-success">Publico</span>
                    @else
                    <span class="badge bg-danger">Privado</span> 
                    @endif                  
                  </td> 
                  <td>{{ $dato->ganancia_motorista }}</td>  
                  <td> 
                    @if($dato->zona_envio_gratis == 0)
                    <span class="badge bg-success">NO</span>
                    @else
                    <span class="badge bg-warning">SI</span> 
                    @endif                  
                  </td>   
                  <td> 
                    @if($dato->mitad_precio == 0)
                    <span class="badge bg-success">NO</span>
                    @else
                    <span class="badge bg-warning">SI</span> 
                    @endif                  
                  </td>

                  <td> 
                    @if($dato->min_envio_gratis == 0)
                    <span class="badge bg-success">NO</span>
                    @else
                    <span class="badge bg-warning">SI</span> 
                    @endif                  
                  </td>

                  <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="verInformacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar                  
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