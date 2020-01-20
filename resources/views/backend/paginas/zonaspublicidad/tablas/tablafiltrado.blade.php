<section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr> 
                        <th style="width: 20%">Nombre publicación</th>
                        <th style="width: 15%">Publicación identificador</th>
                        <th style="width: 15%">Fecha</th>
                        <th style="width: 15%">Posición</th>
                        </tr>
                    </thead>
                    <tbody id="tablecontents">
                        @foreach($publicacion as $dato)
                        <tr class="row1" data-id="{{ $dato->id }}"> 
                        
                          <td>{{ $dato->nombre }}</td> 
                          <td>{{ $dato->identificador }}</td> 
                          <td>{{ $dato->fecha }}</td> 
                          <td>{{ $dato->posicion }}</td>

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

        idzona = {{ $idzona }};

        var order = [];
        $('tr.row1').each(function(index,element) {
          order.push({
            id: $(this).attr('data-id'),
            posicion: index+1
          });
        });

        var spinHandle = loadingOverlay().activate();

        let formData = new FormData();
        formData.append('[order]', order);

        axios.post('/admin/zonapublicidad/ordenar',{ 
            'order': order,
            'idzona': idzona,
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