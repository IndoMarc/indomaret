<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INDOMARET APP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            background-size: 200% 200%;
            animation: gradientBg 12s ease infinite;
            margin: 0;
            padding: 32px 16px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #475569;
        }

        @keyframes gradientBg {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 40px 28px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 15px 35px rgba(148, 163, 184, 0.15), inset 0 1px 1px rgba(255, 255, 255, 1);
            max-width: 440px;
            width: 100%;
            animation: fadeIn 0.8s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
            width: 100%;
        }

        .profile-img {
            display: block;
            width: 100%;
            max-width: 200px;
            height: auto;
            object-fit: contain;
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 20px rgba(148, 163, 184, 0.2);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin: 0 auto;
        }

        .container:hover .profile-img {
            transform: scale(1.03);
        }

        h1 {
            color: #334155;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 28px 0;
            letter-spacing: -0.5px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: #f8fafc;
            color: #475569;
            padding: 15px 16px;
            border-radius: 16px;
            font-size: 14.5px;
            font-weight: 600;
            letter-spacing: 0.2px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            animation: slideUp 0.5s ease-out forwards;
            cursor: pointer;
        }

        .menu-item.full-width {
            grid-column: span 2;
        }

        .menu-item:nth-child(1) { animation-delay: 0.1s; }
        .menu-item:nth-child(2) { animation-delay: 0.15s; }
        .menu-item:nth-child(3) { animation-delay: 0.2s; }
        .menu-item:nth-child(4) { animation-delay: 0.25s; }
        .menu-item:nth-child(5) { animation-delay: 0.3s; }
        .menu-item:nth-child(6) { animation-delay: 0.35s; }
        .menu-item:nth-child(7) { animation-delay: 0.4s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-item:hover {
            background: #e0e7ff;
            color: #4338ca;
            border-color: #c7d2fe;
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 10px 20px -5px rgba(199, 210, 254, 0.5);
        }

        .menu-item:active {
            transform: translateY(-1px) scale(0.99);
        }

        .footer-text {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 16px;
            font-weight: 500;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #ffffff;
            padding: 24px;
            border-radius: 20px;
            max-width: 360px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        @keyframes modalPop {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-box h3 {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-size: 18px;
            font-weight: 700;
        }

        .modal-box p {
            margin: 0 0 20px 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
        }

        .modal-btn {
            background: #4338ca;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s ease;
        }

        .modal-btn:hover {
            background: #3730a3;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="profile-wrapper">
            <img src="indomaret.PNG" alt="mhr" class="profile-img">
        </div>
        <h1></h1>
        
        <div class="menu-grid">
            <a href="cek_harga.php" class="menu-item">Cek Harga Produk</a>
            <a href="scan_itt.php" class="menu-item">Scan ITT PJR</a>
            <a href="qr_code.php" class="menu-item">Buat QRCode</a>
            <a href="stock_opname.php" class="menu-item">Stock Opname</a>
            <a href="sales_harian.php" class="menu-item">Laporan Sales Harian</a>
            <a href="sales_palmur.php" class="menu-item">Laporan Paling Murah</a>
            <a href="http://192.168.137.1:3000/index2.html" class="menu-item full-width" id="btnRekapKas">Rekapan Kas Induk & Anak</a>
        </div>

        <div class="footer-text">
            ~ m.h.r ~
        </div>
    </div>

    <div class="modal-overlay" id="modalNotice">
        <div class="modal-box">
            <h3>Pemberitahuan</h3>
            <p>Sebelum klik OKE pastikan jaringan hotspot di komputer kasir sudah aktif, dan sambung ke jaringan wifi nya agar aplikasi bisa di akses .. </p>
            <button class="modal-btn" id="btnConfirm">OKE</button>
        </div>
    </div>

    <script>
        const btnRekapKas = document.getElementById('btnRekapKas');
        const modalNotice = document.getElementById('modalNotice');
        const btnConfirm = document.getElementById('btnConfirm');

        let targetUrl = '';

        btnRekapKas.addEventListener('click', function(e) {
            e.preventDefault();
            targetUrl = this.getAttribute('href');
            modalNotice.classList.add('active');
        });

        btnConfirm.addEventListener('click', function() {
            modalNotice.classList.remove('active');
            if (targetUrl && targetUrl !== '#') {
                window.location.href = targetUrl;
            }
        });
    </script>

</body>
</html>