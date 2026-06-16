<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INDOMARET</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            overflow: hidden;
            height: 100vh;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100%;
        }

        header {
            height: 60px;
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid #e2e8f0;
            z-index: 10000;
            box-sizing: border-box;
        }

        .logo-area {
            display: flex;
            align-items: center;
            height: 100%;
            flex: 1;
        }

        .logo-area.center {
            justify-content: center;
        }

        .logo-area.right {
            justify-content: flex-end;
        }

        .logo-area img {
            height: 35px;
            width: auto;
            object-fit: contain;
        }

        .logo-area.center img {
            height: 80px;
        }

        main {
            position: absolute;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 75px;
            overflow: hidden;
            background-color: #ffffff;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            background-color: #ffffff;
            display: none;
        }

        iframe.active-iframe {
            display: block;
        }

        nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 75px;
            background: #ffffff;
            box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.06);
            border-top: 1px solid #e2e8f0;
            z-index: 9999;
            display: flex;
            align-items: center;
            box-sizing: border-box;
            padding: 0 16px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 12px;
            width: 100%;
        }

        nav ul li {
            flex: 1;
            max-width: 140px;
        }

        nav ul li a {
            text-decoration: none;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 4px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 12px;
            background: transparent;
            text-align: center;
            white-space: nowrap;
            transition: all 0.25s ease;
            position: relative;
            cursor: pointer;
        }

        nav ul li a i {
            font-size: 20px;
            transition: transform 0.2s ease;
        }

        nav ul li a span {
            display: block;
            font-size: 11px;
            letter-spacing: 0.2px;
        }

        nav ul li a:hover {
            color: #2563eb;
            background: #f1f5f9;
        }

        nav ul li a:hover i {
            transform: translateY(-2px);
        }
        
        nav ul li a.active-tab {
            color: #ffffff;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        @media (max-width: 480px) {
            header {
                padding: 0 12px;
            }
            .logo-area img {
                height: 28px;
            }
            .logo-area.center img {
                height: 40px;
            }
            nav {
                padding: 0 8px;
                height: 70px;
            }
            nav ul {
                gap: 4px;
            }
            nav ul li a span {
                font-size: 10px;
            }
            nav ul li a i {
                font-size: 18px;
            }
            main {
                top: 60px;
                bottom: 70px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="logo-area">
                <img src="indomaret.PNG" alt="Logo Indomaret">
            </div>
            <div class="logo-area center">
                <img src="mhr.jpg" alt="Logo Tengah">
            </div>
            <div class="logo-area right">
                <img src="cinta.PNG" alt="Logo Cinta">
            </div>
        </header>

        <main>
            <iframe src="chat.php" id="iframe-chat" class="active-iframe"></iframe>
            <iframe src="cek_harga.html" id="iframe-harga"></iframe>
            <iframe src="scan_pjr.html" id="iframe-pjr"></iframe>
            <iframe src="qr_klik.html" id="iframe-qr"></iframe>
        </main>

        <nav>
            <ul>
                <li>
                    <a class="active-tab" onclick="switchTab(this, 'iframe-chat')">
                        <i class="fas fa-comments"></i>
                        <span>Room Chat</span>
                    </a>
                </li>
                <li>
                    <a onclick="switchTab(this, 'iframe-harga')">
                        <i class="fas fa-tags"></i>
                        <span>Cek Harga</span>
                    </a>
                </li>
                <li>
                    <a onclick="switchTab(this, 'iframe-pjr')">
                        <i class="fas fa-barcode"></i>
                        <span>Scan ITT PJR</span>
                    </a>
                </li>
                <li>
                    <a onclick="switchTab(this, 'iframe-qr')">
                        <i class="fas fa-qrcode"></i>
                        <span>QRCode Klik</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <script>
        function switchTab(element, iframeId) {
            const links = document.querySelectorAll('nav ul li a');
            links.forEach(link => link.classList.remove('active-tab'));
            element.classList.add('active-tab');

            const iframes = document.querySelectorAll('main iframe');
            iframes.forEach(iframe => iframe.classList.remove('active-iframe'));
            
            document.getElementById(iframeId).classList.add('active-iframe');
        }
    </script>
</body>
</html>