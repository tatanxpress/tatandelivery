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
                    <th style="width: 15%">Imagen</th>
                    <th style="width: 15%">Opciones</th>
                    </tr>
                  </thead>
                  <tbody id="tablecontents">
                    @foreach($foto as $dato)
                    <tr class="row1" data-id="{{ $dato->id }}">
                    
                    <td>{{ $dato->id }}</td>
                    <td><center><img alt="Servicios" src="{{ url('storage/productos/'.$dato->imagen_extra) }}" width="150px" height="150px" /></center></td>
                
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

          axios.post('/admin/productos/imagenes-extra/ordenar',  {
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