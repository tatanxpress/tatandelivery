<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Reporte cobro</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<style> 

.titulo{
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    margin-top: 11px;
    font-family: "Times New Roman", Times, serif;   
}

@page { margin: 2cm;  }
    .firstpage { 
      position: absolute;
      page-break-after: always; 
      top: -50px; // compensating for @page top margin
      width: 100%;
      margin: 0;
    }
  
  .otherpages{ margin: 4cm; }

.logotitulo{
    float:left;
    padding-left: 25px; 
    margin-left: 15px;  
    width: 65px;
    height: 85px;
}

#customers {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-top: 55px;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd;}

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #f2f2f2;
  color: #1E1E1E;
} 

.oficina{
    margin-top: 150px;
    font-size: 16px;   
    padding-right: 0px;    
    font-family: "Times New Roman", Times, serif;
    text-align: left;
}

</style>
    <!-- cabecera -->
    <div class="row"> 
            <center><p class="titulo">
            PAGO DE ORDENES<br>
            <p><font size="3">Del: {{ $f1 }}  Al: {{ $f2 }}</font></p>
            {{ $nombre }}
            </p>
           </center>           
    </div>  

        <table id="customers">
          <tr>
            <th># Orden</th>   
            <th>Total</th> 
            <th>Fecha</th>
            <th>Cupón</th>
            <th>Paga a P.</th>
            <th>Método</th>
            <th>Área</th>
          </tr>

          @foreach($orden as $dato)
            <tr>
              <td>{{$dato->id }}</td>             
              <td>${{$dato->precio_total}}</td>
              <td>{{$dato->fecha_orden}}</td>
              <td>{{$dato->cupon}}</td>
                @if($dato->pago_a_propi == 1)
                <td>Si</td>
                @else
                <td>No</td>
                @endif

                @if($dato->tipo_pago == 0)
                <td>Efectivo</td>
                @else
                <td>Credito</td>
                @endif

                <td>{{ $dato->area }}</td>
           
            </tr> 
          @endforeach  
 
          <tr>
            <td>Total:</td>
            <td>${{ $totalDinero }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          
          <tr>
            <td>Comisión: {{ $comision }}%</td>
            <td>${{ $suma }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>

          <tr>
            <td>Pagar:</td>
            <td>${{ $pagar }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        
        </table>


        <p class="oficina">
        ___________________________   &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;  &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;  &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;   _________________________ <br>
        &nbsp; &nbsp;  Administrador de Cobro  &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;   &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;          Responsable del Servicio         
        </p>

        

</body>

</html> 