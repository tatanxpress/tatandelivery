@include('frontend.menu.superior')

  @include("frontend.menu.navbar")

    <!-- iconos de carga -->
    <div class="preloader">
      <div class="loader">
        <div class="ball"></div>
        <div class="ball"></div>
        <div class="ball"></div>
      </div>
    </div>

    <div class="page">

    <section class="" id="about">
        <div class="container">
            <img src="{{ asset('images/tatanlogo.png') }}" style="width:150px; heigh:150px">
        </div>
    </section>

      <!-- seccion link paystore -->
      <section class="section novi-bg novi-bg-img section-md-4 bg-primary">
        <div class="container">
          <div class="text-center">
            <h2>Descarga la Aplicación</h2>
          </div>

          <div class="text-center">
            <img src="{{ asset('images/logoplay.png') }}">
          </div>         
        </div>
      </section>

      <!-- Seccion pasos de uso -->
      <section class="section novi-bg novi-bg-img section-md-2 bg-default">
        <div class="container">
          <div class="text-center">
            <h3>Como funciona</h3>
            <h2>en 3 sencillos pasos</h2>
          </div>
          <div class="row row-50 post-classic-counter justify-content-lg-between justify-content-center">
            <div class="col-lg-4 col-sm-6">
              <div class="post-classic novi-bg bg-secondary-1">
                <h3 class="post-classic-title">Elige <br> un producto que te guste</h3>
                <p class="post-classic-text">En más de 20 comercios afiliados</p>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="post-classic novi-bg bg-secondary-2">
                <h3 class="post-classic-title">Agregalo al carrito</h3>
                <p class="post-classic-text">ordena facil y rápido</p>
              </div>
            </div>
            <div class="col-lg-4 col-sm-6">
              <div class="post-classic novi-bg bg-secondary-3">
                <h3 class="post-classic-title">Espera la confirmación del <br> Servicio</h3>
                <p class="post-classic-text">Tu producto llegara en el tiempo estimado</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- testimonios-->
      <section class="section novi-bg novi-bg-img section-md-3 bg-default" id="clients">
        <div class="container">
          <div class="row row-40 align-items-center">
            <div class="col-lg-6">
              <div class="owl-pagination-custom" id="owl-pagination-custom">
                <div class="data-dots-custom" data-owl-item="0"><img src="images/shutter-testimonials-01-179x179.png" alt="" width="179" height="89"/>
                </div>
                <div class="data-dots-custom" data-owl-item="1"><img src="images/shutter-testimonials-02-306x306.png" alt="" width="306" height="153"/>
                </div>
                <div class="data-dots-custom" data-owl-item="2"><img src="images/testimonials-03-179x179.png" alt="" width="179" height="89"/>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <h3>what Our clients say</h3>
              <h2>testimonials</h2>
              <!-- Owl Carousel-->
              <div class="quote-classic-wrap">
                <div class="quote-classic-img"><img src="images/quote-37x29.png" alt="" width="37" height="14"/>
                </div>
                <div class="owl-carousel owl-carousel-classic" data-items="1" data-dots="true" data-loop="false" data-autoplay="false" data-mouse-drag="false" data-dots-custom="#owl-pagination-custom">
                  <div class="quote-classic">
                    <p class="big">I have tried a lot of food delivery services but Plate is something out of this world! Their food is really healthy and it tastes great, which is why I recommend this company to all my friends!</p>
                    <h3 class="quote-classic-name">Sophie Smith</h3>
                  </div>
                  <div class="quote-classic">
                    <p class="big">Both the food and your customer service are excellent in every way, and I just wanted to express how happy I am with your company. Wishing you all the best!</p>
                    <h3 class="quote-classic-name">Ann peters</h3>
                  </div>
                  <div class="quote-classic">
                    <p class="big">Thank you so much for your Balanced menu, it has been such a big help to me and I feel the food I am eating from you has really helped boost my immune system.</p>
                    <h3 class="quote-classic-name">peter lee</h3>
                  </div>
                </div>
              </div><a class="button button-primary button-sm" href="#">Send Your Review</a>
            </div>
          </div>
        </div>
      </section>


   
   
   
    @include("frontend.menu.footer")

  
  
  </body>
</html>