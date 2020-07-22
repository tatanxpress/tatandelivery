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
                <th style="width: 15%">Precio</th>
                <th style="width: 15%">Posicion</th>
                <th style="width: 15%">Activo</th>
               
              </tr>
            </thead> 
            <tbody id="tablecontents">
              @foreach($productos as $dato)
              <tr class="row1" data-id="{{ $dato->id }}">
              
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->nombre }}</td>
                <td>{{ $dato->precio }}</td>
                <td>{{ $dato->posicion }}</td>
                <td>
                @if($dato->activo == 0)
                  <button type="button" class="btn btn-success btn-xs" onclick="modalActivar({{ $dato->id }})">
                    <i class="fas fa-edit" title="Activar"></i>&nbsp; Activar
                  </button>  
                @else
                  <button type="button" class="btn btn-danger btn-xs" onclick="modalDesactivar({{ $dato->id }})">
                    <i class="fas fa-edit" title="Desactivar"></i>&nbsp; Desactivar
                  </button> 
                @endif   
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

          axios.post('/admin/encargos/lista/ordenar-productos',  {  
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