

     <!-- Main content -->
 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
            <table id="table" class="table table-bordered">
                  <thead>
                    <tr>
                    <th style="width: 15%">Servicio identificador</th>
                    <th style="width: 15%">Categoria</th>
                    <th style="width: 10%">Posicion</th>
                    <th style="width: 10%">Activo</th>   
                    <th style="width: 15%">Activo Admin</th>
                    <th style="width: 15%">Opciones</th>     
                    </tr>
                  </thead> 
                  <tbody id="tablecontents">
                    @foreach($servicio as $dato)
                    <tr class="row1" data-id="{{ $dato->id }}">
                    
                    <td>{{ $dato->identificador }}</td> 
                    <td>{{ $dato->nombre }}</td> 
                    <td>{{ $dato->posicion }}</td> 
            
                    <td> 
                      @if($dato->activo == 0)
                      <span class="badge bg-danger">Desactivado</span>
                      @else
                      <span class="badge bg-success">Activado</span>
                      @endif
                    </td>
                    
                    <td> 
                      @if($dato->activo_admin == 0)
                      <span class="badge bg-danger">Desactivado</span>
                      @else
                      <span class="badge bg-success">Activado</span>
                      @endif
                    </td>
                    <td>
                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                      <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
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


      $( "#tablecontents" ).sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        update: function() {
            sendOrderToServer();
        }
      });


      function sendOrderToServer() {

        var order = [];
        $('tr.row1').each(function(index,element) {
          order.push({
            id: $(this).attr('data-id'),
            posicion: index+1
          });
        });

          var spinHandle = loadingOverlay().activate();

          axios.post('/admin/categorias/ordenar',  {  
          'order': order 
          })
          .then((response) => {
            loadingOverlay().cancel(spinHandle);
           
            recargarVista();

          })
          .catch((error) => {           
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
          }); 
      }

    });
</script>