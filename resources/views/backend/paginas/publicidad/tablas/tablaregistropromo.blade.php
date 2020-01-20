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
                    <th style="width: 10%">Ingreso</th>
                    <th style="width: 10%">Desde</th>
                    <th style="width: 10%">Hasta</th>
                    <th style="width: 15%">Tipo</th>
                    <th style="width: 15%">Pago</th> 
                    <th style="width: 15%">Opciones</th>
                </tr> 
                </thead>
                <tbody> 
                @foreach($registro as $dato)
      
                <tr>                    
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->fecha }}</td>
                    <td>{{ $dato->fecha1 }}</td>
                    <td>{{ $dato->fecha2 }}</td>
                    <td> 
                      @if($dato->tipo == 1)
                      <span class="badge bg-info">Publicidad</span>
                      @else
                      <span class="badge bg-primary">Promocion</span>
                      @endif
                    </td>

                    <td>{{ $dato->pago }}</td>                    
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