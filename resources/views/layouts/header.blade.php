<!-- resources/views/layouts/header.blade.php -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        @if(session()->has('user_id'))
            <li class="nav-item">
                <a href="{{ route('profile') }}" class="btn btn-primary mr-2">PROFILE</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('logout') }}" class="btn btn-danger">LOGOUT</a>
            </li>
        @endif
    </ul>
</nav>