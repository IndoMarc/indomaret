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
            height: 100vh;
            height: 100dvh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        /* ANIMASI MASUK (RISE) */
        @keyframes rise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }

        header,
        .menu-item,
        footer {
            animation: rise 0.55s cubic-bezier(0.2, 0.7, 0.2, 1) backwards;
        }

        /* Delay animasi berurutan tiap elemen */
        header { animation-delay: 0.04s; }
        .menu-item:nth-child(1) { animation-delay: 0.10s; }
        .menu-item:nth-child(2) { animation-delay: 0.16s; }
        .menu-item:nth-child(3) { animation-delay: 0.22s; }
        .menu-item:nth-child(4) { animation-delay: 0.28s; }
        .menu-item:nth-child(5) { animation-delay: 0.34s; }
        .menu-item:nth-child(6) { animation-delay: 0.40s; }
        .menu-item:nth-child(7) { animation-delay: 0.46s; }
        .menu-item:nth-child(8) { animation-delay: 0.52s; }
        .menu-item:nth-child(9) { animation-delay: 0.58s; }
        footer { animation-delay: 0.64s; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
            }
        }

        /* HEADER POSISI TETAP DI ATAS */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: var(--bg-color);
            text-align: center;
            padding: 16px;
            border-bottom: 1px solid var(--border);
            z-index: 100;
            width: 100%;
        }

        h1 {
            font-family: 'Source Serif 4', Georgia, serif;
            font-size: 22px;
            font-weight: 500;
            letter-spacing: -0.4px;
            line-height: 1.25;
            color: var(--text-main);
        }

        /* CONTAINER UTAMA / AREA MENU DI TENGAH (Bisa di-scroll) */
        .container {
            max-width: 460px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-top: 75px;
            margin-bottom: 55px;
            flex: 1;
            overflow-y: auto;
            padding: 4px 0;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        body.leaving .container {
            opacity: 0;
            transform: translateY(-8px);
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
            padding: 12px 16px;
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

        /* FOOTER POSISI TETAP DI BAWAH */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: var(--bg-color);
            text-align: center;
            font-size: 12px;
            color: var(--text-sub);
            padding: 12px;
            border-top: 1px solid var(--border);
            letter-spacing: 0.5px;
            z-index: 100;
            width: 100%;
        }
    </style>
</head>
<body>

    <header>
        <h1>F8YF - GRAND MUTIARA</h1>
    </header>

    <div class="container">
        <div class="menu-grid">
            <a href="cek_harga.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Cek Harga Produk</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="scan_itt.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Scan ITT PJR</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="qr_code.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Buat QRCode</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="stock_opname.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Stock Opname</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="sales_harian.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Laporan Sales Harian</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="sales_palmur.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Laporan Paling Murah</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="pos_manualan.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>POS Kasir Manualan</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="http://192.168.137.1:3000/index2.html" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Rekap Kas Induk &amp; Anak</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
            <a href="database.php" class="menu-item"><svg class="arrow arrow-left" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3L5 8l5 5"/></svg><span>Pengaturan Database</span><svg class="arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3l5 5-5 5"/></svg></a>
        </div>
    </div>

    <footer>
       ~ M.H.R ~
    </footer>

    <script>
        function navigateWithTransition(url) {
            document.body.classList.add('leaving');
            setTimeout(function() {
                window.location.href = url;
            }, 250);
        }

        document.querySelectorAll('.menu-item').forEach(function(link) {
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