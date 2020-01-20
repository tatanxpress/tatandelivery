 <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>             
                <tr>
                  <th style="width: 10%">Zona identificador</th>
                  <th style="width: 15%">Publicacion identificador</th>
                  <th style="width: 15%">Fecha ingreso</th>
                  <th style="width: 20%">Opciones</th>            
                </tr>
                </thead>
                <tbody>
                @foreach($publicaciones as $dato)
                <tr>
                  <td>{{ $dato->identificador }}</td>
                  <td>{{ $dato->identiPubli }}</td>
                  <td>{{ $dato->fecha }}</td>
                 
                  <td>
                    <button type="button" class="btn btn-danger btn-xs" onclick="modalBorrar({{ $dato->id }})">
                    <i class="fas fa-eye" title="Borrar"></i>&nbsp; Borrar
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