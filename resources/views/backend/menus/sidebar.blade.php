
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
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
     
        <li class="nav-item has-treeview">
            @can('completo')
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Mapa Zonas
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            @endcan
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/zona/mapa/zona') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Zona</p>
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
                  <p>Ver Clientes</p>
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

              <li class="nav-item">
                <a href="{{ url('/admin/zonaservicios/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Zona Servicios</p>
                </a>
              </li>

              
              <li class="nav-item">
                <a href="{{ url('/admin/propietarios/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Propietarios</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/pagoservicios/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pago a servicios</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/serviciopago/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Registro pagos</p>
                </a>
              </li>
                           
            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Publicidad
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/publicidad/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Publicidad Activa</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/publicidad/lista-inactivo') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Publicidad Inactiva</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/zonapublicidad/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Zona publicidad</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/registropromo/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Registro</p>
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
              <li class="nav-item">
                <a href="{{ url('/admin/motoristasservicio/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Motorista asignados</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/motopago/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Registro de pagos</p>
                </a>
              </li>
             
                           
            </ul>
          </li>

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
                <a href="{{ url('/admin/revisadorbitacora/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Bitacora revisador</p>
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
                  <p>Admin revisador</p>
                </a>
              </li>
                           
            </ul>
          </li>

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
                <a href="{{ url('/admin/ordenes/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Orden</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ url('/admin/motoorden/lista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Motorista ordenes</p>
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
                  <p>Buscador Orden Motorista</p>
                </a>
              </li>
   
            </ul> 
          </li> 

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fa fa fa-globe"></i>
              <p>
                Generales
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ url('/admin/notificacion/vista') }}" target="frameprincipal" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Notificaciones Global</p>
                </a>
              </li>

            
                           
            </ul>
          </li>

         
        </ul>
      </nav>
    </div>
  </aside>

