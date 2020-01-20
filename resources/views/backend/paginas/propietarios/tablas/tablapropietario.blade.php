 <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                <th style="width: 15%">Identificador servicio</th>
                    <th style="width: 20%">Nombre</th> 
                    <th style="width: 10%">Fecha ingreso</th>                     
                    <th style="width: 10%">Activo</th>  
                    <th style="width: 15%">Teléfono</th>
                    <th style="width: 15%">Opciones</th>            
                </tr>
                </thead>
                <tbody> 
                @foreach($propi as $dato)
      
                <tr>
                    
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombrePropi }}</td>
                    <td>{{ $dato->fecha }}</td>
                    <td> 
                      @if($dato->activo == 0)
                      <span class="badge bg-danger">Inactivo</span>
                      @else
                      <span class="badge bg-primary">Activo</span>
                      @endif
                    </td>
                    <td>{{ $dato->telefono }}</td>
                   
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