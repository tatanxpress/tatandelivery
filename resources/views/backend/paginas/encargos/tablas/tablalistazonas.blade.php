<!-- Main content -->
<section class="content">
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
      <table id="table" class="table table-bordered">
            <thead>
              <tr>
              <th style="width: 10%">ID</th>
                <th style="width: 15%">Identi Zona</th>
                <th style="width: 15%">Precio Envio</th>
                <th style="width: 15%">Ganancia Motorista</th>
                <th style="width: 15%">Opciones</th> 
              </tr>
            </thead>  
            <tbody id="tablecontents">
              @foreach($zona as $dato)
                <tr class="row1" data-id="{{ $dato->id }}">
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->identificador }}</td>
                <td>{{ $dato->precio_envio }}</td>
                <td>{{ $dato->ganancia_motorista }}</td>

              <td>
              <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                    <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                    </button>   

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
