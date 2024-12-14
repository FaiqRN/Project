<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SDM') }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .video-container {
            position: relative;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            background-color: #03346E; /* Fallback color jika video belum load */
        }

        #myVideo {
            position: absolute;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .content {
            position: relative;
            height: 100vh;
            width: 100vw;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(3, 52, 110, 0.7);
            color: white;
            text-align: center;
            padding: 20px;
            z-index: 1;
        }

        .logo {
            width: 150px;
            height: 150px;
            margin-bottom: 30px;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .subtitle {
            font-size: 1.8rem;
            margin-bottom: 40px;
            color: white;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .start-button {
            padding: 15px 50px;
            font-size: 1.4rem;
            background-color: #FF6500;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px; /* Spacing antara icon dan text */
        }

        .start-button i {
            font-size: 1.2em; /* Ukuran icon sedikit lebih besar dari text */
            margin-right: 5px;
        }

        

        .start-button:hover {
            background-color: #ff8533;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
            color: white;
            text-decoration: none;
        }

        .description {
            max-width: 800px;
            margin: 0 auto 40px;
            font-size: 1.2rem;
            line-height: 1.6;
            color: #f8f9fa;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .start-button {
                padding: 12px 40px;
                font-size: 1.2rem;
            }
            .start-button i {
                font-size: 1.1em;
            }
        }


    </style>
</head>
<body>
    <div class="video-container">
        <video id="myVideo" autoplay muted loop playsinline preload="auto">
            <source src="{{ asset('videos/landing.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        
        <div class="content">
            <img src="{{ asset('adminlte/dist/img/logo_kampus.png') }}" alt="Logo Kampus" class="logo">
            <h1 class="title">POLINEMA</h1>
            <p class="subtitle">Sistem Informasi Manajemen SDM</p>
            <p class="description">
                Sistem Aplikasi Manajemen Terpadu Untuk Pengelolaan Sumber Daya Manusia 
                Yang Efektif dan Efisien di Lingkungan JTI Polinema
            </p>
            <a href="{{ route('login') }}" class="start-button">
                 <i class="fas fa-play-circle"></i> Mulai
            </a>
        </div>
    </div>

    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
    
    <script>
        // Ensure video autoplays
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('myVideo');
            video.play().catch(function(error) {
                console.log("Video autoplay failed:", error);
            });
        });
    </script>
</body>
</html>