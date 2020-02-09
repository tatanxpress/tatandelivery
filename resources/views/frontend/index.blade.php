@include('frontend.menu.superior')

  @include("frontend.menu.navbar")

  <style>

    .img1 {
      max-width: 150px;
      height: 50px;
    }

  </style>

    <!-- iconos de carga -->
    <div class="preloader">
      <div class="loader">
        <div class="ball"></div>
        <div class="ball"></div>
        <div class="ball"></div>
      </div>
    </div>

    <div class="page">

    <section class="" >
        <div class="container">           
            <img src="{{ asset('images/Presentacion.jpg') }}" name="img1">
        </div>
    </section>

      <!-- seccion link paystore -->
      <section class="section novi-bg novi-bg-img section-md-4 bg-primary">
        <div class="container">
          <div class="text-center">
            <h2>Descarga la Aplicación</h2>
          </div>
          <br>

          <div class="text-center">
            <img src="{{ asset('images/logogoogle.png') }}">
          </div>         
        </div>
      </section>

      <!-- Seccion pasos de uso -->
      <section class="section novi-bg novi-bg-img section-md-2 bg-default">
        <div class="container">
          <div class="text-center">
            <h3>COMPRA LO QUE QUIERAS</h3>
            <h2>EN 3 SENCILLOS PASOS</h2>
          </div>
          <div class="row row-50 post-classic-counter justify-content-lg-between justify-content-center">
            <div class="col-lg-4 col-sm-6">
              <div class="post-classic novi-bg bg-primary">
                <h3 class="post-classic-title"><font color="white">Elige <br> un producto que te guste</font></h3>
                <p class="post-classic-text"><font color="white">Entre los diversos tipos de negocios de Emprendedores Metapanecos.</font></p>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="post-classic novi-bg bg-primary">
                <h3 class="post-classic-title"><font color="white">Agregalo al carrito</font></h3>
                <p class="post-classic-text"><font color="white">Selecciona los productos y revisalos en el Carrito de Compras, donde se detallan los Productos a Adquirir y el Total de la Compra.</font></p>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="post-classic novi-bg bg-primary">
                <h3 class="post-classic-title"><font color="white">Espera la Confirmación del <br> Servicio</font></h3>
                <p class="post-classic-text"><font color="white">En breves instantes estaras hablanco con el propietario del Negocio, el cual te indicara el tiempo de llegada de tus productos.</font></p>
              </div>
            </div>
          </div>
        </div>
      </section>
 

   
    
   
    @include("frontend.menu.footer")

  
  
  </body>
</html>