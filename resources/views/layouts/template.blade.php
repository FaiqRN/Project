<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name','SDM')}}</title>

    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- DataTables-->
    <link rel="stylesheet" href="{{asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/plugins/sweetalert2/sweetalert2.min.css')}}">

    <!-- Custom CSS untuk mengubah warna -->
    <style>
        /* Mengubah warna sidebar */
        .main-sidebar {
            background-color: #03346E !important;
        }
        
        /* Mengubah warna teks sidebar */
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link,
        .sidebar a {
            color: white !important;
        }

        /* Mengubah warna hover pada menu sidebar */
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover {
        background-color: #FF6500 !important;
        color: white !important;
}

        /* Mengubah warna header/navbar */
        .main-header.navbar {
            background-color: #FF6500 !important;
        }

        /* Mengubah warna teks dan ikon pada header */
        .main-header.navbar .nav-link,
        .main-header.navbar .nav-link i {
            color: white !important;
        }

        /* Mengubah warna background content/body */
        .content-wrapper {
            background-color: white !important;
        }

        /* Modifikasi tampilan logo POLINEMA */
        .brand-link {
            background-color: #3868a3 !important;
            color: white !important;
            height: 70px !important;
            display: flex !important;
            align-items: center !important;
            padding: 10px 15px !important;
        }

        .brand-link .brand-image {
            height: 45px !important;
            width: 45px !important;
            max-height: 45px !important;
            margin-left: 0 !important;
            margin-right: 10px !important;
            margin-top: 0 !important;
        }

        .brand-link .brand-text {
            font-size: 22px !important;
            font-weight: bold !important;
            letter-spacing: 0.5px !important;
            color: white !important;
            margin-left: 2px !important;
        }

        /* Mengubah warna active menu di sidebar */
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #FF6500 !important;
            color: white !important;
        }

        /* Mengubah style tombol pada header */
        .main-header .btn-primary {
            background-color: #03346E !important;
            border-color: #03346E !important;
            padding: 8px 20px !important;
            font-weight: 500 !important;
            margin-right: 10px !important;
        }

        /* Style khusus untuk tombol logout */
        .main-header .btn-danger {
            background-color: #E60026 !important;
            border-color: #E60026 !important;
            color: white !important;
            font-weight: bold !important;
            padding: 8px 25px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
            margin-right: 15px !important;
        }

        .main-header .btn-danger:hover {
            background-color: #FF0033 !important;
            border-color: #FF0033 !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
        }

        /* Style untuk navbar buttons container */
        .main-header .navbar-nav {
            margin-right: 10px !important;
        }

        /* Memberikan space antara tombol */
        .main-header .navbar-nav > li {
            margin: 0 5px !important;
        }

        /* Style untuk semua tombol di header */
        .main-header .btn {
            border-radius: 4px !important;
            transition: all 0.3s ease !important;
        }
    </style>

    @stack('css')
</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
    <!-- Navbar -->
    @include('layouts.header')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{url('/')}}" class="brand-link">
            <img src="{{asset('adminlte/dist/img/logo_kampus.png')}}" alt="logo kampus" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">POLINEMA</span>
        </a>

        <!-- Sidebar -->
        @if(session('level_nama') == 'Admin')
            @include('layouts.sidebar-admin')
        @elseif(session('level_nama') == 'Kaprodi')
            @include('layouts.sidebar-kaprodi')
        @else
            @include('layouts.sidebar-dosen')
        @endif
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @include('layouts.breadcrumb')

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    @include('layouts.footer')
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- DataTable & Plugins -->
<script src="{{asset('adminlte/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.colvis.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('adminlte/dist/js/adminlte.min.js')}}"></script>

<script>
    $.ajaxSetup({headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
</script>
<script src="{{asset('adminlte/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
@stack('js')
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Mencegah back button setelah logout
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
</script>
</body>
</html>