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
            height: 100dvh;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            height: 100dvh;
            width: 100%;
            position: relative;
        }

        header {
            height: 60px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid #e2e8f0;
            z-index: 999;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        .toggle-btn-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .toggle-btn {
            width: 36px;
            height: 36px;
            background: #f1f5f9;
            border: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s ease;
            font-size: 18px;
        }

        .toggle-btn:hover {
            color: #2563eb;
            background: #e2e8f0;
        }

        .hint-arrow {
            position: absolute;
            left: 50px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #2563eb;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            animation: bounceLeft 2s infinite;
            z-index: 1000;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .hint-arrow::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 50%;
            transform: translateY(-50%);
            border-width: 6px 6px 6px 0;
            border-style: solid;
            border-color: transparent #2563eb transparent transparent;
        }

        .hint-arrow.opened {
            background: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            animation: none;
            transform: none;
        }

        .hint-arrow.opened::before {
            border-color: transparent #10b981 transparent transparent;
        }

        @keyframes bounceLeft {
            0%, 20%, 50%, 80%, 100% {
                transform: translateX(0);
            }
            40% {
                transform: translateX(8px);
            }
            60% {
                transform: translateX(4px);
            }
        }

        .header-logo-right {
            display: flex;
            align-items: center;
            height: 100%;
        }

        .header-logo-right img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        nav {
            position: fixed;
            top: 0;
            left: -290px;
            width: 280px;
            height: 100vh;
            height: 100dvh;
            background: #ffffff;
            box-shadow: 4px 0 30px rgba(15, 23, 42, 0.08);
            border-right: 1px solid #e2e8f0;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-sizing: border-box;
            padding: 20px;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(0);
            will-change: transform;
        }

        nav.open {
            transform: translateX(290px);
        }

        .sidebar-top {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .sidebar-top::-webkit-scrollbar {
            display: none;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .logo-top {
            display: flex;
            align-items: center;
        }

        .logo-top img {
            height: 35px;
            width: auto;
            object-fit: contain;
        }

        .close-btn {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 20px;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.8;
            transition: opacity 0.2s, color 0.2s;
        }

        .close-btn:hover {
            opacity: 1;
            color: #ef4444;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        nav ul li {
            width: 100%;
        }

        nav ul li a {
            text-decoration: none;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            background: transparent;
            transition: all 0.2s ease;
            cursor: pointer;
            box-sizing: border-box;
        }

        nav ul li a .menu-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        nav ul li a i.menu-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        nav ul li a i.arrow-icon {
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        nav ul li a:hover {
            color: #2563eb;
            background: #f1f5f9;
        }
        
        nav ul li a.active-tab {
            color: #ffffff;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        nav ul li a.active-tab i.arrow-icon {
            color: #ffffff;
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding-left: 16px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            box-sizing: border-box;
        }

        .submenu.show {
            max-height: 300px;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .submenu li a {
            padding: 10px 16px;
            font-size: 13.5px;
            font-weight: 500;
        }

        .submenu li a.active-sub-tab {
            color: #2563eb;
            background: #eff6ff;
            font-weight: 600;
        }

        li.has-submenu.open-menu > a i.arrow-icon {
            transform: rotate(180deg);
        }

        .sidebar-bottom {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px 0 5px 0;
            width: 100%;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .timestamp {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-align: center;
            line-height: 1.5;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            background: rgba(15, 23, 42, 0.3);
            z-index: 9999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        main {
            flex: 1;
            background-color: #ffffff;
            position: relative;
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

        footer {
            height: 40px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="toggle-btn-container">
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="hint-arrow" id="hint-arrow">
                    <i class="fas fa-arrow-left" id="hint-icon"></i> <span id="hint-text">Klik untuk melihat daftar menu</span>
                </div>
            </div>
            <div class="header-logo-right">
                <img src="mhr.jpg" alt="Logo Kanan">
            </div>
        </header>

        <div class="sidebar-overlay" id="overlay" onclick="toggleSidebar()"></div>

        <nav id="sidebar">
            <div class="sidebar-top">
                <div class="sidebar-header">
                    <div class="logo-top">
                        <img src="indomaret.PNG" alt="Logo Indomaret">
                    </div>
                    <button class="close-btn" onclick="toggleSidebar()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <ul>
                    <li>
                        <a class="active-tab" onclick="switchTab(this, 'iframe-chat')">
                            <span class="menu-content">
                                <i class="fas fa-comments align-icon menu-icon"></i>
                                <span>Room Chat</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a onclick="switchTab(this, 'iframe-harga')">
                            <span class="menu-content">
                                <i class="fas fa-tags menu-icon"></i>
                                <span>Cek Harga</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a onclick="switchTab(this, 'iframe-pjr')">
                            <span class="menu-content">
                                <i class="fas fa-barcode menu-icon"></i>
                                <span>Scan ITT PJR</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a onclick="switchTab(this, 'iframe-plano')">
                            <span class="menu-content">
                                <i class="fas fa-layer-group menu-icon"></i>
                                <span>Cek Planogram</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a onclick="switchTab(this, 'iframe-qr')">
                            <span class="menu-content">
                                <i class="fas fa-qrcode menu-icon"></i>
                                <span>QRCode Klik</span>
                            </span>
                        </a>
                    </li>
                    <li class="has-submenu" id="menu-tutorial">
                        <a onclick="checkTutorialAccess(this)">
                            <span class="menu-content">
                                <i class="fas fa-book menu-icon"></i>
                                <span>Daftar Tutorial</span>
                            </span>
                            <i class="fas fa-chevron-down arrow-icon"></i>
                        </a>
                        <ul class="submenu" id="submenu-tutorial">
                            <li>
                                <a onclick="switchSubTab(this, 'iframe-app-pc')">
                                    <span class="menu-content">
                                        <i class="fas fa-desktop menu-icon"></i>
                                        <span>Buka Program Via PC</span>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a onclick="switchSubTab(this, 'iframe-app-hp')">
                                    <span class="menu-content">
                                        <i class="fas fa-mobile-alt menu-icon"></i>
                                        <span>Buka Program Via HP</span>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a onclick="switchSubTab(this, 'iframe-cetak')">
                                    <span class="menu-content">
                                        <i class="fas fa-print menu-icon"></i>
                                        <span>Cetak SO Via Program</span>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a onclick="switchSubTab(this, 'iframe-cmd')">
                                    <span class="menu-content">
                                        <i class="fas fa-terminal menu-icon"></i>
                                        <span>Buka/Unlock CMD</span>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a onclick="switchSubTab(this, 'iframe-task')">
                                    <span class="menu-content">
                                        <i class="fas fa-tasks menu-icon"></i>
                                        <span>Buka Task Manager</span>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a onclick="switchSubTab(this, 'iframe-jam')">
                                    <span class="menu-content">
                                        <i class="fas fa-clock menu-icon"></i>
                                        <span>Ubah Jam Untuk Absen</span>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="sidebar-bottom">
                <div class="timestamp" id="live-timestamp">SENIN, 00:00:00</div>
            </div>
        </nav>

        <main>
            <iframe src="chat.php" id="iframe-chat" class="active-iframe"></iframe>
            <iframe src="cek_harga.html" id="iframe-harga"></iframe>
            <iframe src="scan_pjr.html" id="iframe-pjr"></iframe>
            <iframe src="plano.html" id="iframe-plano"></iframe>
            <iframe src="qr_klik.html" id="iframe-qr"></iframe>
            <iframe src="app_pc.html" id="iframe-app-pc"></iframe>
            <iframe src="app_hp.html" id="iframe-app-hp"></iframe>
            <iframe src="cetak.html" id="iframe-cetak"></iframe>
            <iframe src="cmd.html" id="iframe-cmd"></iframe>
            <iframe src="task.html" id="iframe-task"></iframe>
            <iframe src="jam.html" id="iframe-jam"></iframe>
        </main>

        <footer>
            <div class="footer-text">&copy; Made With 🖤</div>
        </footer>
    </div>

    <script>
        function removeActiveStates() {
            const links = document.querySelectorAll('nav ul li a');
            links.forEach(link => {
                link.classList.remove('active-tab');
                link.classList.remove('active-sub-tab');
            });
        }

        function switchTab(element, iframeId) {
            removeActiveStates();
            element.classList.add('active-tab');

            const parentMenu = document.getElementById('menu-tutorial');
            const submenu = document.getElementById('submenu-tutorial');
            parentMenu.classList.remove('open-menu');
            submenu.classList.remove('show');
            submenu.style.maxHeight = null;

            const iframes = document.querySelectorAll('main iframe');
            iframes.forEach(iframe => iframe.classList.remove('active-iframe'));
            
            document.getElementById(iframeId).classList.add('active-iframe');
            
            toggleSidebar();
        }

        function getDynamicPassword() {
            const now = new Date();
            const date = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            return `${date}${month}${year}`;
        }

        function checkTutorialAccess(element) {
            const submenu = document.getElementById('submenu-tutorial');
            
            if (submenu.classList.contains('show')) {
                toggleSubmenu(element);
                return;
            }

            const correctPassword = getDynamicPassword();
            const userInput = prompt("Masukkan password untuk membuka menu :");
            
            if (userInput === correctPassword) {
                toggleSubmenu(element);
            } else if (userInput !== null) {
                alert("Password salah! Akses ditolak.");
            }
        }

        function toggleSubmenu(element) {
            const parent = element.parentElement;
            const submenu = document.getElementById('submenu-tutorial');
            
            parent.classList.toggle('open-menu');
            submenu.classList.toggle('show');
            
            if (submenu.classList.contains('show')) {
                submenu.style.maxHeight = submenu.scrollHeight + "px";
            } else {
                submenu.style.maxHeight = null;
            }
        }

        function switchSubTab(element, iframeId) {
            removeActiveStates();
            
            const parentMenu = document.getElementById('menu-tutorial');
            parentMenu.querySelector('a').classList.add('active-tab');
            element.classList.add('active-sub-tab');

            const iframes = document.querySelectorAll('main iframe');
            iframes.forEach(iframe => iframe.classList.remove('active-iframe'));
            
            document.getElementById(iframeId).classList.add('active-iframe');
            
            toggleSidebar();
        }

        function updateGreeting() {
            const now = new Date();
            const hour = now.getHours();
            const hintText = document.getElementById('hint-text');
            const hintIcon = document.getElementById('hint-icon');
            
            let greeting = "";
            let iconClass = "";
            
            if (hour >= 4 && hour < 10) {
                greeting = "Selamat Pagi";
                iconClass = "fas fa-cloud-sun";
            } else if (hour >= 10 && hour < 15) {
                greeting = "Selamat Siang";
                iconClass = "fas fa-sun";
            } else if (hour >= 15 && hour < 18) {
                greeting = "Selamat Sore";
                iconClass = "fas fa-cloud-moon-rain";
            } else {
                greeting = "Selamat Malam";
                iconClass = "fas fa-moon";
            }
            
            if (hintText && hintIcon) {
                hintText.textContent = greeting;
                hintIcon.className = iconClass;
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const hintArrow = document.getElementById('hint-arrow');
            
            if (!sidebar.classList.contains('open')) {
                overlay.classList.add('show');
                sidebar.classList.add('open');
                if (hintArrow) {
                    hintArrow.classList.add('opened');
                    updateGreeting();
                }
            } else {
                overlay.classList.remove('show');
                sidebar.classList.remove('open');
            }
        }

        function updateTimestamp() {
            const now = new Date();
            const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
            const dayName = days[now.getDay()];
            
            const options = { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric',
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: false 
            };
            
            const dateTimeString = now.toLocaleString('id-ID', options).replace(/\./g, ':');
            document.getElementById('live-timestamp').innerHTML = `${dayName}<br>${dateTimeString}`;
        }
        
            setInterval(updateTimestamp, 1000);
            updateTimestamp();
    </script>
</body>
</html>