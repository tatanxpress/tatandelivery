<section class="content">
      <div class="row">
        <div class="col-12">
          <div class="card">         
            <div class="card-body">
            <table id="table" class="table table-bordered">
                <thead>
                    <tr> 
                        <th style="width: 30%">Identificador</th>
                        <th style="width: 30%">Nombre</th>
                        <th style="width: 40%">En cuantas zonas esta</th>
                    </tr>
                    </thead>
                    <tbody id="tablecontents">
                        @foreach($servicios as $dato)
                        <tr class="row1" data-id="{{ $dato->id }}">
                        
                        <td>{{ $dato->identificador }}</td> 
                        <td>{{ $dato->nombre }}</td>
                        <td>{{ $dato->cuantas }}</td>
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

        ordenarPosiciones(order);     
      }

    });
</script>