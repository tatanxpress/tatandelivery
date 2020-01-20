<section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr> 
                        <th style="width: 10%">Nombre</th>
                        <th style="width: 15%">Descripcion</th>
                    </tr>
                    </thead>
                    <tbody id="tablecontents">
                        @foreach($servicio as $dato)
                        <tr class="row1" data-id="{{ $dato->id }}">
                        
                        <td>{{ $dato->nombre }}</td> 
                        <td>{{ $dato->descripcion }}</td> 
                    
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

        axios.post('/admin/tiposerviciozona/ordenar',{ 
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