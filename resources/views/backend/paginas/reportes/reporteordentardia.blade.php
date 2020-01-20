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
    margin-top: 175px;
    font-size: 16px;   
    padding-right: 75px;    
    font-family: "Times New Roman", Times, serif;
    text-align: center;
}

</style>
    <!-- cabecera -->
    <div class="row"> 
            <center><p class="titulo">
            REPORTE DE ORDEN TIEMPO EXCEDIDO<br>
            SERVICIO {{ $nombre }}
            </p><p><small>De: {{ $f1 }}  Hasta: {{ $f2 }}</small></p></center>      
    </div>  

        <table id="customers">
          <tr>
            <th># Orden</th>           
            <th>Fecha orden</th>
            <th>Hora entrega</th>
            <th>Fecha Exedido</th>
            <th>Precio orden</th>
          </tr>

          @foreach($orden as $dato)
            <tr>
              <td>{{ $dato->idorden }}</td>             
              <td>{{ $dato->fecha_orden }}</td>
              <td>{{ $dato->horaEstimada }}</td>
              <td>{{ $dato->fecha_tardio }}</td>
              <td>${{ $dato->precio_total }}</td>
            </tr> 
          @endforeach  

          <tr>
            <td>Total:</td>
            <td></td>
            <td></td>
            <td></td>
            <td>${{ $totalDinero }}</td>
          </tr>
          
          <tr>
            <td>Total ordenes:</td>
            <td></td>
            <td></td>
            <td></td>
            <td> {{ $conteo }} </td>
          </tr>

          <tr>
            <td>Multa:</td>
            <td></td>
            <td></td>
            <td></td>
            <td> {{ $conteo }} * {{ $multa }}</td>
          </tr> 

          <tr>
            <td>Pagar:</td>
            <td></td>
            <td></td>
            <td></td>
            <td>${{ $pagar }}</td>
          </tr>
        
        </table>

          <!-- oficina -->
       
              <p class="oficina">
                  Tatan Express
              </p>
        

</body>
</html> 