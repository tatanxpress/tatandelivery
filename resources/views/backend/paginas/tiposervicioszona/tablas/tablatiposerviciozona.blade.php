 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr> 
                  <th style="width: 15%">Nombre zona</th>
                  <th style="width: 20%">Descripción</th>
                  <th style="width: 10%">Identificador</th>
                  <th style="width: 15%">Activo</th>  
                  <th style="width: 20%">Servicio</th>               
                  <th style="width: 20%">Opciones</th>            
                </tr>
                </thead>
                <tbody>
                @foreach($tipo as $dato)
                <tr>
                  <td>{{ $dato->nombre }}</td> 
                  <td>{{ $dato->descripcion }}</td>
                  <td>{{ $dato->identificador }}</td>
                  <td> 
                    @if($dato->activo == 0)
                    <span class="badge bg-danger">Inactivo</span>
                    @else
                    <span class="badge bg-success">Activo</span>
                    @endif                  
                  </td>                  
                  <td>{{ $dato->nombreServicio }}</td>  
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