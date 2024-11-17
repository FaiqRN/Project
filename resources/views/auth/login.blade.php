<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Login - POLINEMA</title>
    
    <style>
        body {
            background-color: #2b71e8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        
        .login-container {
            background-color: #d3d3d3;
            padding: 2rem;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        
        .select-user {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            margin-bottom: 1rem;
            appearance: none;
            background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E") no-repeat calc(100% - 1rem) center;
            box-sizing: border-box;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background-color: #ff0000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-bottom: 1rem;
            transition: background-color 0.2s;
        }
        
        .btn-login:hover {
            background-color: #cc0000;
        }
        
        .forgot-password {
            text-align: center;
        }
        
        .forgot-password a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        .alert-danger {
            background-color: #fff3f3;
            color: #dc3545;
            border: 1px solid #f8d7da;
        }
        
        .alert-success {
            background-color: #f3fff3;
            color: #28a745;
            border: 1px solid #d7f8db;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            
            <img src="{{ asset('images/logo_kampus.png') }}" alt="Logo Polinema" class="logo">
        </div>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            
            <div class="form-group">
                <label>User</label>
                <select name="level_id" class="select-user" required>
                    <option value="">Pilih User</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->level_id }}" {{ old('level_id') == $level->level_id ? 'selected' : '' }}>
                            {{ $level->level_nama }}
                        </option>
                    @endforeach
                </select>
                @error('level_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" 
                       placeholder="Enter username" value="{{ old('username') }}" required>
                @error('username')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" 
                       placeholder="Enter password" required>
                @error('password')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            @error('login')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn-login">Login</button>

            <div class="forgot-password">
                <a href="#" onclick="alert('Fitur lupa password akan segera tersedia')">Forgot Password?</a>
            </div>
        </form>
    </div>
    <script>
        // Mencegah back button
        window.onload = function() {
            if(typeof history.pushState === "function") {
                history.pushState("jibberish", null, null);
                window.onpopstate = function () {
                    history.pushState('newjibberish', null, null);
                };
            }
        }

        // Mencegah akses halaman yang di-cache
        window.onpageshow = function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        };

        // mematikan back button
        function disableBack() { 
            window.history.forward() 
        }
        disableBack();
        window.onload = disableBack;
        window.onpageshow = function(evt) { 
            if (evt.persisted) disableBack() 
        }
        window.onunload = function() { void (0) }
</body>
</html>