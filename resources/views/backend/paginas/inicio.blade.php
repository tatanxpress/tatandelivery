<!DOCTYPE html>
<html lang="es">

<head>
  <title>Panel de control</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="{{ asset('fontawesome/css/all.min.css') }}" type="text/css" rel="stylesheet" />
  <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link href="{{ asset('css/backend/dataTables.bootstrap4.css') }}" type="text/css" rel="stylesheet" />

</head>

<body class="hold-transition sidebar-mini">
   
  <!-- Main content -->
  <div class="wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-12"> 
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Pantalla Principal</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Inicio</a></li>
              <li class="breadcrumb-item active">Resumen</li>
            </ol>
          </div>
        </div>
      </div>
    </div>


    <section class="content-header">
      <div class="container-fluid">
          <div class="col-sm-12">    

            <p>Total productos agregado: <strong>{{ $totalproducto }}</strong></p>
                       
          </div>  
      </div>
    </section>
  
  <script src="{{ asset('js/backend/jquery.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('js/backend/bootstrap.bundle.min.js') }}" type="text/javascript"></script> 
  <script src="{{ asset('js/backend/adminlte.min.js') }}" type="text/javascript"></script>


  @yield('content-admin-js')

</body>

</html>