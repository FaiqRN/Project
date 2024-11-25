<footer class="main-footer">
    <div class="content">
        <!-- Left Column -->
        <div class="left">
            <div class="institution-text">Jurusan Teknologi Informasi (JTI)</div>
            <div class="institution-text">D-IV Sistem Informasi Bisnis</div>
            <div class="institution-text">Politeknik Negeri Malang</div>
        </div>

        <!-- Center Column -->
        <div class="center">
            <span>Alamat</span>
            <div>Jl. Soekarno Hatta No. 9, Jatimulyo, Kec<br>
            Lowokwaru, Kota Malang, Jawa Timur 65141</div>
            <span>Telepon</span>
            <div>(0341) 404424</div>
        </div>

        <!-- Right Column -->
        <div class="right">
            <div class="social">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>

    <div class="copyright">
        Copyright © {{ date('Y') }} Polinema. All rights reserved.
    </div>

    <style>
        .main-footer {
            background-color: #021526;
            color: white;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            box-sizing: border-box;
            overflow: hidden;
        }

        .content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px 30px;
            box-sizing: border-box;
        }

        .left {
            flex: 1;
            padding-right: 20px;
        }

        .institution-text {
            font-size: 13px;
            margin-bottom: 2px;
            font-weight: normal;
        }

        .center {
            flex: 1.5;
            text-align: center;
            padding: 0 25px;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 12px;
        }

        .center span {
            display: block;
            margin-bottom: 3px;
            font-weight: 500;
        }

        .center div {
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .right {
            flex: 1;
            padding-left: 20px;
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
        }

        .social {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            padding-top: 5px;
        }

        .social a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .social a:hover {
            opacity: 0.8;
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }

        .copyright {
            background-color: #000;
            text-align: center;
            padding: 5px 0;
            margin: 0;
            box-sizing: border-box;
            width: 100%;
            font-size: 11px;
        }

        @media (max-width: 768px) {
            .content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .left, .center, .right {
                width: 100%;
                padding: 10px 0;
                border: none;
            }

            .social {
                justify-content: center;
                padding-top: 10px;
            }
        }
    </style>
</footer>