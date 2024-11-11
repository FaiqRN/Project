<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a href="{{ url('/profile') }}" class="btn btn-primary mr-2">PROFILE</a>
        </li>
        <li class="nav-item">
            <a href="{{ url('/logout') }}" class="btn btn-danger">LOGOUT</a>
        </li>
    </ul>
</nav>