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
                <th style="width: 15%">Negocio</th>
                <th style="width: 15%">Categoria</th>
                <th style="width: 15%">Estado</th> 
                <th style="width: 15%">Posicion</th> 
                <th style="width: 15%">Opciones</th>   
              </tr>
            </thead> 
            <tbody id="tablecontents">
              @foreach($lista as $dato)
              <tr class="row1" data-id="{{ $dato->id }}">
              
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->negocio }}</td>
                <td>{{ $dato->categoria }}</td> 
 
                @if($dato->activo == 0)
                <td><span class="badge bg-danger">Inactivo</span></td>  
                @else
                <td><span class="badge bg-green">Activo</span></td>  
                @endif   

                <td>{{ $dato->posicion }}</td>
        
              
              <td>
                  <button type="button" class="btn btn-primary btn-xs" onclick="modalEditar({{ $dato->id }})">
                    <i class="fas fa-eye" title="Editar"></i>&nbsp; Editar
                  </button>   
                  <button type="button" class="btn btn-success btn-xs" onclick="productos({{ $dato->id }})">
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

          axios.post('/admin/encargos/lista/ordenar',  {  
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