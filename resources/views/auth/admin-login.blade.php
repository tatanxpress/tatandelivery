@include('frontend.menu.superior')

  @include("frontend.menu.navbar")
	<div class="container" style="margin-top:25px; margin-bottom:25px">
		<div class="d-flex justify-content-center h-100">

			<div class="card " style="height: 450px;">
				<div class="card-header text-center">

					
						<div class="col-md-12">
							<img src="{{ asset('images/tatanlogo.png') }}" width="100" height="20px" srcset="">
						</div>
					
					<h3 style="position: relative; margin-top:10px">ADMINISTRADOR</h3>
				</div>
				<div class="card-body" >
				<form class=" validate-form">
					<div class="input-group form-group" style="margin-top:25px">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-envelope"></i></span>
						</div>
						<input id="correo" type="email" class="form-control" placeholder="Correo electrónico" required >
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-lock"></i></span>
						</div>
						<input id="password" type="password" class="form-control" placeholder="Contraseña" required >
					</div>
					<br>
					<div class="form-group text-center">
						<input type="button" value="Entrar" id="btnLogin" onclick="login()" class="btn btn-primary">
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>

    @include("frontend.menu.footer")
  
    <script type="text/javascript">

    // onkey Enter 
    var input = document.getElementById("password");	
        input.addEventListener("keyup", function(event) {	
        if (event.keyCode === 13) {			
            event.preventDefault();			
            login();
        }
    });

    function login() {
     
        var correo = document.getElementById('correo').value;
        var password = document.getElementById('password').value;

        var retorno = validaciones(correo, password);

        if (retorno) {

            let me = this;
            let formData = new FormData();
            formData.append('correo', correo);
            formData.append('password', password);

            var spinHandle = loadingOverlay().activate();

            axios.post('/admin', formData, {
            })
                .then((response) => {
                    loadingOverlay().cancel(spinHandle);
                   
                    verificar(response);
                })
                .catch((error) => {
                    toastr.error('Error del servidor');

                });
        }
    }

    // mensajes para verificar respuesta
    function verificar(response) {

        if (response.data.success == 0) {
            toastr.error('Validacion incorrecta');
        } else if (response.data.success == 1) {
            window.location = response.data.message;           
        } else if (response.data.success == 2) {
            toastr.error('Datos incorrectos');
        } else {
            toastr.error('Error desconocido');
        }
    }

    // validaciones frontend
    function validaciones(correo, password) {			
        if (correo === '') {
            toastr.error("El correo es requerido");
            return false;
        }
       
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(correo)){
            // valido
        }else{
            toastr.error("El correo es invalido");
            return false;
        }
        
        if (password === '') {
            toastr.error("La contraseña es requerida");
            return false;
        }
        
        return true;
    }

</script>
  
  </body>
</html>

