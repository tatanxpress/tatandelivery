@extends('backend.menus.superior') 
@section('content-admin-css')
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" /> 
    <link href="{{ asset('css/frontend/toastr.min.css') }}" type="text/css" rel="stylesheet" />
    
    
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
@stop

<div id="map"></div>

@extends('backend.menus.inferior')
@section('content-admin-js')	

<script src="{{ asset('js/backend/jquery.dataTables.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/backend/dataTables.bootstrap4.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/axios.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/frontend/loadingOverlay.js') }}" type="text/javascript"></script>

 <script>
    
    var map;
    var infoWindow;

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: {lat: 14.331033, lng: -89.442309},
            mapTypeId: 'terrain'
        });
    
        var sites = {!! $poligono !!}
      
        var AreaCoords = new Array();
        $.each( sites, function( key, val ){  
            console.log(val.longitud);              
            AreaCoords.push({lat: parseFloat(val.latitud), lng: parseFloat(val.longitud)});
        });
    
        var bermudaTriangle = new google.maps.Polygon({
            paths: AreaCoords,
            strokeColor: '#404040',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#1976D2',
            fillOpacity: 0.35
        });
        bermudaTriangle.setMap(map);

        // Add a listener for the click event.
        bermudaTriangle.addListener('click', showArrays);

        infoWindow = new google.maps.InfoWindow;
    }

    function showArrays(event) {
        var vertices = this.getPath();

        var contentString = '<b>Bermuda Triangle polygon</b><br>' +
            'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() +
            '<br>';

        // Iterate over the vertices.
        for (var i =0; i < vertices.getLength(); i++) {
            var xy = vertices.getAt(i);
            contentString += '<br>' + 'Coordinate ' + i + ':<br>' + xy.lat() + ',' +
                xy.lng();
        }

        // Replace the info window's content and position.
        infoWindow.setContent(contentString);
        infoWindow.setPosition(event.latLng);

        infoWindow.open(map);
    }

</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-Iz6I6GtO09PaXGSQxZCjIibU_Li7yOM&callback=initMap">
</script>


@endsection