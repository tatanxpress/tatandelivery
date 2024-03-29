 <!-- pie de pagina -->
    <footer id="colorlib-footer" class="section footer-classic">
        <div class="container">
          <div class="row row-50 justify-content-between">
            <div class="col-xl-3 col-md-6">
              <a class="brand" href="#">
              <!-- logotipo -->
                <img class="brand-logo-dark" src="{{ asset('images/tatanlogo.jpg') }}" alt="" width="181" height="50"/>
              </a>
              <p class="rights">
              <span>&copy;&nbsp;</span>
              <span class="copyright-yeasr">
              2020</span><span>.&nbsp;Todos los derechos reservados</span></p>
            </div>
            <div class="col-xl-3 col-md-6">
              <p class="footer-classic-title">Contacto</p>
              <ul class="footer-classic-list">
                <li>
                  <ul> 
                
                    <li> 
                      <dl class="footer-classic-dl">
                        <dt>Co.</dt>
                        <dd><a href="mailto:#">tatanxpress@gmail.com</a></dd>
                       
                      </dl>
                    </li>
                  </ul>
                </li>
                <li><a>Redes Sociales</a></li>
                <li>
                  <ul class="group group-sm footer-classic-social-list">

                    <a href="https://www.facebook.com/tatanexpress/"><img src="{{ asset('images/facebook.png') }}" width="50px" height="50px" title="Facebook"> </a>

                    <a href="https://www.instagram.com/tatanexpress/"><img src="{{ asset('images/instagram.png') }}" width="50px" height="50px" title="Instagram"> </a>

                  
                  </ul>
                </li>
              </ul>
            </div>
            <div class="col-xl-2 col-md-6">
              <p class="footer-classic-title">Información</p>
              <ul class="footer-classic-nav">
                <li><a href="{{ url('/preguntas-frecuentes') }}">Preguntas frecuentes</a></li>
                <li><a href="{{ url('/terminos-condiciones') }}">Términos y Condiciones</a></li>
              </ul>
            </div>
        
          </div> 
        </div>
      </footer>

    <script src="{{ asset('js/frontend/jquery.min.js') }}"></script>
    <script src="{{ asset('js/frontend/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/frontend/core.min.js') }}"></script>
    <script src="{{ asset('js/frontend/script.js') }}"></script>    
    <script src="{{ asset('js/frontend/axios.min.js') }}"></script>
    <script src="{{ asset('js/frontend/toastr.min.js') }}"></script>
    <script src="{{ asset('js/frontend/loadingOverlay.js') }}"></script>

    @yield('java')

  
    </div>

    