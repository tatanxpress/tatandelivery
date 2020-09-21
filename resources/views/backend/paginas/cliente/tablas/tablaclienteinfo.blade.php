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
                  <th style="width: 10%">Area</th>
                  <th style="width: 15%">Fecha Registro</th>
                  <th style="width: 15%">Credi Puntos</th>
                  <th style="width: 15%">Opciones</th>            
                </tr>
                </thead>
                <tbody> 
                @foreach($info as $dato)
                <tr>
                  <td>{{ $dato->name }}</td> 
                  <td>{{ $dato->phone }}</td> 
                  <td>{{ $dato->email }}</td>
                  <td>{{ $dato->nombrezona }}</td>
                  <td> 
                    @if($dato->activo == 0)
                    <span class="badge bg-danger">Inactivo</span>
                    @else
                    <span class="badge bg-success">Activo</span>
                    @endif 
                  </td> 
                  <td>{{ $dato->area }}</td>
                  <td>{{ $dato->fecha }}</td> 
                  <td>${{ $dato->monedero }}</td> 
                  <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar  
                    </button>
                    </br></br>
                    <button type="button" class="btn btn-info btn-xs" onclick="verDirecciones({{ $dato->id }})">
                      <i class="fas fa-map" title="Direccion"></i>&nbsp; Dirección
                    </button>     

                    </br></br>

                    <button type="button" class="btn btn-success btn-xs" onclick="verHistorial({{ $dato->id }})">
                      <i class="fas fa-map" title="Historia"></i>&nbsp; Historial
                    </button>
                    </br></br>
                      <button type="button" class="btn btn-success btn-xs" onclick="verPDF({{ $dato->id }})">
                      <i class="fas fa-map" title="PDF"></i>&nbsp; PDF Historial
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