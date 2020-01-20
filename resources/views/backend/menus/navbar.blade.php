 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
    <!-- Messages Dropdown Menu -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="{{ url('/admin/editarinfo') }}"><i class="fas fa-user-alt"></i> Cuenta
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="{{ url('/admin/editarinfo') }}" target="frameprincipal"  class="dropdown-item">Editar Usuario </a>
          <div class="dropdown-divider"></div>
          <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); 
          document.getElementById('frm-logout').submit();" class="dropdown-item">Cerrar Sesión</a>

          <form id="frm-logout" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
              {{ csrf_field() }}
          </form>
        </div>
    </li>
     
    </ul>
  </nav>
  <!-- /.navbar -->

