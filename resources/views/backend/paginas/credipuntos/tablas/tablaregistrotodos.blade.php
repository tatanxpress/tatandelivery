  
<!-- Main content -->
<section class="content">
    <div class="row">
    <div class="col-12">
        <div class="card">         
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
            <thead>             
            <tr>
                <th style="width: 10%">ID</th> 
                <th style="width: 20%">Cliente</th> 
                <th style="width: 15%">Número</th>                     
                <th style="width: 15%">Fecha</th>
                <th style="width: 15%">Credito</th>
                <th style="width: 15%">Pago total</th>
                <th style="width: 15%">Comision</th>
                <th style="width: 15%">id transaccion</th>
                <th style="width: 15%">codigo</th>
                <th style="width: 15%">Real</th>
                <th style="width: 15%">Aprobada</th>
                <th style="width: 15%">Nota</th>
            </tr>   
            </thead>
            <tbody> 
            @foreach($cliente as $dato)
            <tr>
                <td>{{ $dato->id }}</td>
                <td>{{ $dato->name }}</td>
                <td>{{ $dato->phone }}</td>
                <td>{{ $dato->fecha }}</td>
                <td>{{ $dato->credi_puntos }}</td> 
                <td>{{ $dato->pago_total }}</td> 
                <td>{{ $dato->comision }}</td> 
                <td>{{ $dato->idtransaccion }}</td>
                <td>{{ $dato->codigo }}</td>
                <td>
                    @if($dato->esreal == 1)
                        Si
                    @else
                        No
                    @endif
                </td>
                <td>
                    @if($dato->esaprobada == 1)
                        Si
                    @else
                        No
                    @endif
                </td>
                <td>{{ $dato->nota }}</td>
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