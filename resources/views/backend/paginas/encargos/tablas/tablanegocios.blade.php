

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
                <th style="width: 15%">Fecha Ingreso</th> 
                <th style="width: 15%">Opciones</th>            
                </tr>
                </thead>
                <tbody> 
                @foreach($negocios as $dato)
      
                <tr>
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->identificador }}</td>
                <td>{{ $dato->nombre }}</td>
                <td>{{ $dato->fecha }}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                    </button>   
                    <button type="button" class="btn btn-success btn-xs" onclick="categorias({{ $dato->id }})">
                    <i class="fas fa-eye" title="Categorias"></i>&nbsp; Categorias
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