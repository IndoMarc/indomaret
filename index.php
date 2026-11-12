<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INDOMARET</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #faf9f5;
            --card-bg: #ffffff;
            --border: #e8e6dc;
            --border-strong: #d6d3c4;
            --text-main: #29261b;
            --text-sub: #6f6d63;
            --accent: #c96442;
            --accent-hover: #b5573a;
            --accent-soft: #f4e9e2;
            --item-bg: #ffffff;
            --item-hover: #f5f4ee;
            --shadow: 0 1px 2px rgba(41, 38, 27, 0.04), 0 4px 16px rgba(41, 38, 27, 0.04);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #262624;
                --card-bg: #30302e;
                --border: #3d3d3a;
                --border-strong: #52514d;
                --text-main: #f0eee6;
                --text-sub: #a8a69b;
                --accent: #d97757;
                --accent-hover: #e48a6b;
                --accent-soft: #3d332e;
                --item-bg: #30302e;
                --item-hover: #3a3a37;
                --shadow: 0 1px 2px rgba(0, 0, 0, 0.2), 0 4px 16px rgba(0, 0, 0, 0.2);
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 20px 16px;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 460px;
            width: 100%;
            display: flex;
            flex-direction: column;
            flex: 1;
            justify-content: center;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        body.leaving .container {
            opacity: 0;
            transform: translateY(-8px);
        }

        header,
        .menu-item,
        footer {
            animation: rise 0.55s cubic-bezier(0.2, 0.7, 0.2, 1) backwards;
        }

        .menu-item:nth-child(1) { animation-delay: 0.08s; }
        .menu-item:nth-child(2) { animation-delay: 0.14s; }
        .menu-item:nth-child(3) { animation-delay: 0.20s; }
        .menu-item:nth-child(4) { animation-delay: 0.26s; }
        .menu-item:nth-child(5) { animation-delay: 0.32s; }
        .menu-item:nth-child(6) { animation-delay: 0.38s; }
        .menu-item:nth-child(7) { animation-delay: 0.44s; }
        .menu-item:nth-child(8) { animation-delay: 0.50s; }
        .menu-item:nth-child(9) { animation-delay: 0.56s; }
        footer { animation-delay: 0.62s; }

        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
            }
        }

        /* HEADER DENGAN GARIS DI BAWAHNYA */
        header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
            width: 100%;
        }

        h1 {
            font-family: 'Source Serif 4', Georgia, serif;
            font-size: 24px;
            font-weight: 500;
            letter-spacing: -0.4px;
            line-height: 1.25;
            color: var(--text-main);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            gap: 8px;
            text-decoration: none;
            background: var(--item-bg);
            color: var(--text-main);
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
            cursor: pointer;
        }

        .menu-item .arrow {
            position: absolute;
            right: 16px;
            top: 50%;
            margin-top: -8px;
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            color: var(--text-sub);
            opacity: 0.6;
            transition: transform 0.2s ease, color 0.2s ease, opacity 0.2s ease;
        }

        .menu-item:hover {
            background: var(--item-hover);
            border-color: var(--border-strong);
        }

        .menu-item:hover .arrow {
            color: var(--accent);
            opacity: 1;
            transform: translateX(2px);
        }

        .menu-item .arrow-left {
            left: 16px;
            right: auto;
        }

        .menu-item:hover .arrow-left {
            transform: translateX(-2px);
        }

        .menu-item:active {
            transform: scale(0.99);
        }

        .menu-item:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        /* FOOTER DENGAN GARIS DI ATASNYA */
        footer {
            text-align: center;
            font-size: 12px;
            color: var(--text-sub);
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            letter-spacing: 0.5px;
            width: 100%;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(41, 38, 27, 0.45);
            justify-content: center;
            align-items: flex-start;
            padding-top: 15vh;
            z-index: 1000;
            padding-left: 16px;
            padding-right: 16px;
        }

        .modal-overlay.active {
            display: flex;
            animation: fade 0.15s ease;
        }

        .modal-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 28px 24px 24px;
            border-radius: 20px;
            max-width: 380px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            animation: pop 0.18s ease;
        }

        .modal-box h3 {
            font-family: 'Source Serif 4', Georgia, serif;
            margin: 0 0 10px 0;
            color: var(--text-main);
            font-size: 20px;
            font-weight: 500;
        }

        .modal-box p {
            margin: 0 0 16px 0;
            color: var(--text-sub);
            font-size: 14px;
            line-height: 1.6;
        }

        .modal-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border-strong);
            background: var(--item-bg);
            color: var(--text-main);
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 16px;
            font-family: inherit;
        }

        .modal-input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .error-msg {
            color: #d9534f;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 12px;
            display: none;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
        }

        .modal-btn {
            font-family: inherit;
            border: 1px solid transparent;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            flex: 1;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .modal-btn-primary {
            background: var(--accent);
            color: #ffffff;
        }

        .modal-btn-primary:hover {
            background: var(--accent-hover);
        }

        .modal-btn-secondary {
            background: transparent;
            color: var(--text-main);
            border-color: var(--border-strong);
        }

        .modal-btn-secondary:hover {
            background: var(--item-hover);
        }

        @keyframes fade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pop {
            from { opacity: 0; transform: translateY(6px) scale(0.98); }
            to { opacity: 1; transform: none; }
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>F8YF - GRAND MUTIARA</h1>
        </header>

        <div class="menu-grid">
            <a href="cek_harga.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Cek Harga Produk</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="scan_itt.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Scan ITT PJR</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="qr_code.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Buat QRCode</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="stock_opname.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Stock Opname</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="sales_harian.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Laporan Sales Harian</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="sales_palmur.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Laporan Paling Murah</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="pos_manualan.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>POS Kasir Manualan</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="http://192.168.137.1:3000/index2.html" class="menu-item" id="btnRekapKas"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Rekap Kas Induk &amp; Anak</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="database.php" class="menu-item" id="btnKelolaDb"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Pengaturan Database</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
        </div>
    </div>

    <footer>
       ~ M.H.R ~
    </footer>

    <!-- Modal Pemberitahuan (Rekap Kas) -->
    <div class="modal-overlay" id="modalNotice">
        <div class="modal-box">
            <h3>Pemberitahuan</h3>
            <p>Sebelum klik OKE pastikan jaringan hotspot di komputer kasir sudah aktif, dan sambung ke jaringan wifi nya agar aplikasi bisa di akses .. </p>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" id="btnCancel">Batal</button>
                <button class="modal-btn modal-btn-primary" id="btnConfirm">OKE</button>
            </div>
        </div>
    </div>

    <!-- Modal Kode Akses (Pengaturan Database MySQL) -->
    <div class="modal-overlay" id="modalAuth">
        <div class="modal-box">
            <h3>Kode Akses</h3>
            <p>Silakan masukkan kode akses untuk masuk ke menu Pengaturan Database MySQL.</p>
            <input type="password" id="accessCode" class="modal-input" placeholder="Masukkan kode akses...">
            <div class="error-msg" id="errorMsg">Kode akses salah!</div>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-secondary" id="btnCancelAuth">Batal</button>
                <button class="modal-btn modal-btn-primary" id="btnSubmitAuth">Masuk</button>
            </div>
        </div>
    </div>

    <script>
        const btnRekapKas = document.getElementById('btnRekapKas');
        const modalNotice = document.getElementById('modalNotice');
        const btnConfirm = document.getElementById('btnConfirm');
        const btnCancel = document.getElementById('btnCancel');

        const btnKelolaDb = document.getElementById('btnKelolaDb');
        const modalAuth = document.getElementById('modalAuth');
        const accessCodeInput = document.getElementById('accessCode');
        const errorMsg = document.getElementById('errorMsg');
        const btnSubmitAuth = document.getElementById('btnSubmitAuth');
        const btnCancelAuth = document.getElementById('btnCancelAuth');

        let targetUrl = '';

        // Modal Rekap Kas
        btnRekapKas.addEventListener('click', function(e) {
            e.preventDefault();
            targetUrl = this.getAttribute('href');
            modalNotice.classList.add('active');
        });

        btnCancel.addEventListener('click', function() {
            modalNotice.classList.remove('active');
        });

        modalNotice.addEventListener('click', function(e) {
            if (e.target === modalNotice) modalNotice.classList.remove('active');
        });

        btnConfirm.addEventListener('click', function() {
            modalNotice.classList.remove('active');
            if (targetUrl && targetUrl !== '#') {
                navigateWithTransition(targetUrl);
            }
        });

        // Modal Pengaturan Database MySQL
        btnKelolaDb.addEventListener('click', function(e) {
            e.preventDefault();
            targetUrl = this.getAttribute('href');
            accessCodeInput.value = '';
            errorMsg.style.display = 'none';
            modalAuth.classList.add('active');
            accessCodeInput.focus();
        });

        btnCancelAuth.addEventListener('click', function() {
            modalAuth.classList.remove('active');
        });

        modalAuth.addEventListener('click', function(e) {
            if (e.target === modalAuth) modalAuth.classList.remove('active');
        });

        function verifyCode() {
            const enteredCode = accessCodeInput.value;
            if (enteredCode === '$$&&@@') {
                modalAuth.classList.remove('active');
                navigateWithTransition(targetUrl);
            } else {
                errorMsg.style.display = 'block';
                accessCodeInput.focus();
                accessCodeInput.select();
            }
        }

        btnSubmitAuth.addEventListener('click', verifyCode);

        accessCodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifyCode();
            }
        });

        function navigateWithTransition(url) {
            document.body.classList.Async = true;
            document.body.classList.add('leaving');
            setTimeout(function() {
                window.location.href = url;
            }, 250);
        }

        // Menu lain selain Rekap Kas & Pengaturan Database
        document.querySelectorAll('.menu-item:not(#btnRekapKas):not(#btnKelolaDb)').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                navigateWithTransition(this.getAttribute('href'));
            });
        });

        window.addEventListener('pageshow', function() {
            document.body.classList.remove('leaving');
        });
    </script>

</body>
</html>