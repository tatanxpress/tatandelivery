

     <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                    <th style="width: 10%">ID orden</th> 
                    <th style="width: 20%">Identificador</th> 
                    <th style="width: 18%">Nombre</th>
                    <th style="width: 14%">Fecha</th>  
                    <th style="width: 40%">Opciones</th>            
                </tr>
                </thead>
                <tbody> 
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->fecha_orden }}</td>

                    <td>
                      <button type="button" class="btn btn-info btn-xs" onclick="infocliente({{ $dato->id }})">
                      <i class="fas fa-eye" title="Informacion"></i>&nbsp; Info Cliente
                      </button> 

                      <button type="button" class="btn btn-info btn-xs" onclick="infoorden({{ $dato->id }})">
                      <i class="fas fa-eye" title="Informacion Orden"></i>&nbsp; Info Orden
                      </button>

                      <button type="button" class="btn btn-info btn-xs" onclick="infotipo({{ $dato->id }})">
                      <i class="fas fa-eye" title="Tipo Cargo"></i>&nbsp; Tipo Cargo
                      </button>

                      <button type="button" class="btn btn-info btn-xs" onclick="infocargo({{ $dato->id }})">
                      <i class="fas fa-eye" title="Informacion Cargo"></i>&nbsp; Cargos
                      </button>

                      <button type="button" class="btn btn-info btn-xs" onclick="infomotorista({{ $dato->id }})">
                      <i class="fas fa-eye" title="Motorista"></i>&nbsp; Motorista
                      </button>

                      <button type="button" class="btn btn-primary btn-xs" onclick="producto({{ $dato->id }})">
                      <i class="fas fa-eye" title="Productos"></i>&nbsp; Productos
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