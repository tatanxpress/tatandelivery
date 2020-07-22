@extends('backend.menus.superior')
 
 @section('content-admin-css')
 <link href="{{ asset('css/backend/adminlte.min.css') }}" type="text/css" rel="stylesheet" /> 
 
 @stop
 
 <section class="content-header">
     <div class="container-fluid">
         <div class="col-sm-12">
         <h1>Ubicación del usuario</h1>
         </div>        
     </div>
 </section>
    
   <!-- seccion mapa -->
 <section class="content">
     <div class="container-fluid">
         <div class="card card-primary">  
             <div class="card-header">
                 <h3 class="card-title">Mapa</h3>
             </div>
             <div class="card-body">
                 <div class="row">                  
                     <div class="container">
                         <div class="col-md-12">
                             <iframe width="100%" height="400" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q={{$latitud}},{{$longitud}}&amp;key={{$api}}"></iframe>
                         </div>
                     </div>             
                 </div>
             </div>
         </div>
     </div>
 </section>
 
 @extends('backend.menus.inferior')
 @section('content-admin-js')
 
 @stop