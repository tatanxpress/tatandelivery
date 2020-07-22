 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                  <th style="width: 15%">Nombre</th>
                  <th style="width: 10%">Teléfono</th>
                  <th style="width: 20%">Correo</th>
                  <th style="width: 10%">Zona actual</th>
                  <th style="width: 10%">Activo</th>
                  <th style="width: 15%">Fecha Registro</th>
                     
                </tr>
                </thead>
                <tbody>
                @foreach($cliente as $dato)
                <tr>
                  <td>{{ $dato->nombre }}</td> 
                  <td>{{ $dato->telefono }}</td> 
                  <td>{{ $dato->correo }}</td>
                  <td>{{ $dato->identificador }}</td>
                  <td> 
                    @if($dato->activo == 0)
                    <span class="badge bg-danger">Inactivo</span>
                    @else
                    <span class="badge bg-success">Activo</span>
                    @endif                  
                  </td> 
                  <td>{{ $dato->fecha }}</td>
                
                                
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