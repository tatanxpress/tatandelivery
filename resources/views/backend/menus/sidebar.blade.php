
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <span class="brand-text font-weight-light">Panel de Control</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a>{{ $nombre }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">


        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Mapa Zonas
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/zona/mapa/zona') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Zona</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/admin/usuario/ciudades') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Ciudades</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/tipos/lista-tipos') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tipos</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/admin/tiposervicio/lista-tipo-servicio') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tipo servicios</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('/admin/tiposerviciozona/lista-tipo-servicio-zona') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tipo servicios por Zona</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/tiposerviciozona/posiciones-globales') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Posición Global</p>
                </a>
              </li>



            </ul>
          </li>



          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Clientes
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/cliente/lista-clientes') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Clientes Hoy</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/cliente/lista-clientes-todos') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Clientes Todos</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/cliente/vista-buscar-cliente') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Buscar Cliente</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/numeros/temporales') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Numero Registro</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/extranjeros/extranjeros') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Extranjeros No Verificado</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/usuario/credipuntos') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Credi Puntos No Verificados</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/lista/credito/para/quitar') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Quitar Credito</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="{{ url('/admin/registro/credipuntos') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Credi Puntos Todos</p>
                </a>
              </li>

                <li class="nav-item">
                    <a href="{{ url('/admin/ver/toda/direccion-extranjero') }}" target="frameprincipal" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Dirección Extranjeros</p>
                    </a>
                </li>

            </ul>
          </li>


          <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa fa-globe"></i>
                <p>
                  Servicios
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>

            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/servicios/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Lista Servicios</p>
                </a>
              </li>
              @can('completo')
              <li class="nav-item">
                <a href="{{ url('/admin/zonaservicios/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Zona Servicios</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/zonaservicio/posiciones-globales') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Posición Global</p>
                </a>
              </li>

              @endcan
              <li class="nav-item">
                <a href="{{ url('/admin/propietarios/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Propietarios</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/pagoservicios/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reporte Pago a servicios</p>
                </a>
              </li>

            </ul>
          </li>


            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="nav-icon fa fa fa-globe"></i>
                <p>
                  Motoristas
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="{{ url('/admin/motoristas/lista') }}" target="frameprincipal" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Motorista</p>
                  </a>
                </li>
                @can('completo')
                <li class="nav-item">
                  <a href="{{ url('/admin/motoristasservicio/lista') }}" target="frameprincipal" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Motorista asignados</p>
                  </a>
                </li>
                @endcan

              </ul>
            </li>


          @can('completo')
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Revisadores
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/revisador/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Revisador</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/revisadormoto/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Revisador motorista</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/ordenrevisada/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Orden revisadas</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/adminrevisador/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Crear Admin App</p>
                </a>
              </li>

            </ul>
          </li>
        @endcan


        <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Ordenes
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="{{ url('/admin/control/lista/ordeneshoy') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Ordenes HOY</p>
                </a>
              </li>


              <li class="nav-item">
                <a href="{{ url('/admin/ordenes/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Ultimas 100</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/buscar/numero/orden') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Buscar # Orden</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/motoexpe/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Motorista calificaciones</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/buscar/moto/ordenes') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reporte Pago Motorista</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Configuración
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/dinero/limite') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Extras</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/control/lista/notificacion') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Notificacion Otros</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/control/lista/notificacioncliente') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Notificacion Cliente</p>
                </a>
              </li>

            </ul>
          </li>


            <li class="nav-item">
                <a href="{{ url('/admin/cupones/lista/enviogratis') }}" target="frameprincipal" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Cupón Envío Gratis</p>
                </a>
            </li>

        </ul>
      </nav>
    </div>
  </aside>

