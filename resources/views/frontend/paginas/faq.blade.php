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

        <div class="text-center" style="margin-top:30px">
            <h2>Preguntas Frecuentes</h2>
        </div>

        <div class="container py-3">
            <div class="row">
                <div class="col-10 mx-auto">
                    <div class="accordion" id="faqExample">

                        <div class="card">
                            <div class="card-header p-2" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    ¿Donde descargar la aplicación?
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqExample">
                                <div class="card-body">
                                    Puedes buscar nuestra aplicación en Google Play por nombre Tatan Express. Proximamente para dispositivos Apple.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header p-2" id="headingTwo">
                                <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                ¿Qué hago si mi producto no viene y ya paso el tiempo que me dijo el establecimiento?
                                </button>
                            </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqExample">
                                <div class="card-body">
                                En ese caso debe contactarse con servicio al cliente al número 75825072 y brindar su número de pedido y su nombre completo para empezar el trámite de seguimiento de su orden y de esta forma brindarle toda la atención de las acciones que conllevan la tardanza de más de 20 minutos de la entrega estimada de su orden.                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header p-2" id="headingThree2">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree2" aria-expanded="false" aria-controls="collapseThree2">
                                    Como me puedo afiliar para tener mi negocio en la aplicación TATAN EXPRESS
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseThree2" class="collapse" aria-labelledby="headingThree2" data-parent="#faqExample">
                                <div class="card-body">
                                Si deseas afiliarte a la app deberás mandar un correo a tatanxpress@gmail.com
Con el nombre del negocio, ubicación y nombre de quien solicita unirse además un número de teléfono para concretar una cita y establecer los términos y condiciones.

                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header p-2" id="headingThree3">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree3" aria-expanded="false" aria-controls="collapseThree3">
                                    ¿Qué pasa si algo no es lo que solicite en mi pedido o algo me hizo falta?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseThree3" class="collapse" aria-labelledby="headingThree3" data-parent="#faqExample">
                                <div class="card-body">
                                Lamentamos suceda algo así, en ese caso deberás contactarte con nosotros y brindarnos el número de pedido y nombre del servicio para nosotros poder revisar la orden y contactarnos con el establecimiento y dar una pronta respuesta a su inquietud.
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header p-2" id="headingThree4">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree4" aria-expanded="false" aria-controls="collapseThree4">
                                    ¿Como puedo ser motorista de TATAN EXPRESS?
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseThree4" class="collapse" aria-labelledby="headingThree4" data-parent="#faqExample">
                                <div class="card-body">
                                Escribe a nuestro correo tatanxpress@gmail.com y bríndanos tus datos personales para contactarnos y así solicitar la demás información como solvencia policial, numero de licencia de motocicleta, DUI, etc.                                </div>
                            </div>
                        </div>
 
                    </div>
                </div>
            </div>
        </div>
   
    @include("frontend.menu.footer")
  
  </body>
</html>