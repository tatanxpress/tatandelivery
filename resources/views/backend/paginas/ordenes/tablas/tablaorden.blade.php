

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
                    <th style="width: 20%">Identificador servicio</th> 
                    <th style="width: 15%">Sub total</th>                     
                    <th style="width: 15%">Fecha orden</th>
                    <th style="width: 15%">Estado</th>
                    <th style="width: 10%">Cupon</th>
                    <th style="width: 15%">Opción</th>
                                             
                </tr> 
                </thead>
                <tbody> 
                @foreach($orden as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->identificador }}</td>
                    <td>{{ $dato->precio_total }}</td>
                    <td>{{ $dato->fecha_orden }}</td>
                    <td>{{ $dato->motorista }}</td> 
                    <td>{{ $dato->cupon }}</td>

                  
                    <td>
                      <button type="button" class="btn btn-info btn-xs" onclick="verModal({{ $dato->id }})">
                      <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                      </button> 
                    </td> 

                    </tr>    

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