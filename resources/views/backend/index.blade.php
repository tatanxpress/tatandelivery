<!DOCTYPE html>
<html lang="es">
<head>
<title>Tatan Express - Panel</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="{{ asset('fontawesome/css/all.min.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" />  
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">	
	
</head>
<body class="hold-transition sidebar-mini">
    
<div class="wrapper" >
   @include("backend.menus.navbar")
   @include("backend.menus.sidebar")
		
   <div class="content-wrapper" style=" background-color: #fff;">  
      <!-- pantalla inicial que carga -->
      <iframe style="width: 100%; resize: initial; overflow: hidden; min-height: 83vh" frameborder="0"  scrolling="" id="frameprincipal" src="{{ url('/admin/inicio') }}" name="frameprincipal"> 
      </iframe>
   </div>

   @include("backend.menus.footer")
  
</div>

	<script src="{{ asset('js/backend/jquery.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('js/backend/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/backend/adminlte.min.js') }}" type="text/javascript"></script>


	@yield('content-admin-js')

</body>
</html>