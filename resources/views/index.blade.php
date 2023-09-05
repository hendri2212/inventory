<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>inventory</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

  <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
  @vite(['resources/js/app.js','plugins/fontawesome-free/css/all.min.css','resources/css/adminlte.min.css','plugins/overlayScrollbars/css/OverlayScrollbars.min.css','plugins/summernote/summernote-bs4.min.css'])
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>


  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <!-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <span class="brand-text font-weight-light "><b>INVENTORY</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      
    <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          		<img src="Avatar/Ryo.jpeg" class="img-circle elevation-2" alt="User Image">
        	</div>
        	<div class="info">
          		<a href="#" class="d-block">Ryooooo</a>
        </div>
    </div> -->

      <!-- SidebarSearch Form -->
    <div class="form-inline py-2">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          		<div class="input-group-append">
            		<button class="btn btn-sidebar">
              			<i class="fas fa-search fa-fw"></i>
            		</button>
            	</div>
        </div>
    </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
    
        <li class="nav-item">
            <a href="/beranda" class="nav-link bg-light mt-1">
                <i class="nav-icon far fa-image"></i>
                <p>
                    Beranda
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="/good" class="nav-link bg-light mt-1">
                <i class="nav-icon fas fa-th"></i>
                <p>
                    Data Barang
                </p>
            </a>
        </li>

          <li class="nav-item">
            <a href="/transaction" class="nav-link bg-light mt-1">
                <i class="nav-icon fas fa-table"></i>
                <p>
                    Transaksi
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="/supplier" class="nav-link bg-light mt-1">
                <i class="nav-icon fas fa-edit"></i>
                <p>
                    Supply
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{url('room')}}" class="nav-link bg-light mt-1">
                <i class="nav-icon fas fa-columns"></i>
                <p>
                    Rooms
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="/category" class="nav-link bg-light mt-1">
                <i class="nav-icon fas fa-th"></i>
                <p>
                    Kategory
                </p>
            </a>
        </li>

	   <li class="nav-item">
            <a href="{{url('company')}}" class="nav-link bg-light mt-1">
                <i class="nav-icon fas fa-columns"></i>
                <p>
                    Company
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="/user" class="nav-link bg-light mt-1">
                <i class="nav-icon fas fa-th"></i>
                <p>
                    User
                </p>
            </a>
        </li>
    </ul>
            </nav>
        <!-- /.sidebar-menu -->
    </div>

</aside>
<div class="">
    <!-- /.content-header -->
    @yield('tittle')

    <!-- Main content -->
    <section class="content">
    
    @yield('konten')
</div>
    </section>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->

</aside>



<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
</body>
</html>
