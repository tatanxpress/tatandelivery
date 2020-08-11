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
                    <th style="width: 15%">Nombre</th>
                    <th style="width: 10%">Precio</th>
                    <th style="width: 15%">Disponibilidad</th>
                    <th style="width: 10%">Activo</th>
                    <th style="width: 15%">Usa Cantidad</th>
                    <th style="width: 10%">Promocion</th>
                    <th style="width: 10%">Posicion</th>
                    <th style="width: 15%">Opciones</th>
                    </tr>
                  </thead>
                  <tbody id="tablecontents">
                    @foreach($producto as $dato)
                    <tr class="row1" data-id="{{ $dato->id }}">
                    
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->precio }}</td>
                    <td> 
                      @if($dato->disponibilidad == 0)
                      <span class="badge bg-danger">No disponible</span>
                      @else
                      <span class="badge bg-success">Activado</span>
                      @endif
                    </td>
                    <td> 
                      @if($dato->activo == 0)
                      <span class="badge bg-danger">Desactivado</span>
                      @else
                      <span class="badge bg-success">Activado</span>
                      @endif
                    </td>
                    <td> 
                      @if($dato->utiliza_cantidad == 0)
                      <span class="badge bg-danger">No</span>
                      @else
                      <span class="badge bg-success">Si</span>
                      @endif
                    </td>

                    <td> 
                      @if($dato->es_promocion == 0)
                      <span class="badge bg-danger">No</span>
                      @else
                      <span class="badge bg-success">Si</span>
                      @endif
                    </td>

                    <td>{{ $dato->posicion }}</td> 
                
                    <td>
                      <button type="button" class="btn btn-primary btn-xs" onclick="informacion({{ $dato->id }})">
                      <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                      </button> 
                      </br>
                      </br>
                      <button type="button" class="btn btn-warning btn-xs" onclick="modalVideo({{ $dato->id }})">
                      <i class="fas fa-eye" title="Video"></i>&nbsp; Video
                      </button> 
                      </br>
                      </br>
                      <button type="button" class="btn btn-success btn-xs" onclick="modalFoto({{ $dato->id }})">
                      <i class="fas fa-eye" title="Fotos Extras"></i>&nbsp; Fotos Extras
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

          axios.post('/admin/productos/ordenar',  {
          'order': order 
          })
          .then((response) => {
            loadingOverlay().cancel(spinHandle);
           
            toastr.success('Actualizado');
          })
          .catch((error) => {           
            loadingOverlay().cancel(spinHandle);
            toastr.error('Error');
          }); 
      }

    });
</script>