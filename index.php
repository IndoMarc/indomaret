<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="referrer" content="no-referrer">
    <title>Indomaret App</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            padding-top: 50px;
            padding-bottom: 50px;
            min-height: 100vh;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #ffffff;
            color: #333333;
            display: flex;
            align-items: center;
            padding: 0 15px;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            border-bottom: 1px solid #e2e8f0;
        }

        .hamburger-btn {
            background: none;
            border: none;
            color: #333333;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            outline: none;
            padding: 4px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .hamburger-btn:hover {
            background-color: #f1f5f9;
        }

        .header-title {
            font-size: 18px;
            font-weight: 600;
            flex-grow: 1;
            text-align: center;
            color: #1e293b;
        }

        .external-link-btn {
            background: none;
            border: none;
            color: #333333;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            outline: none;
            padding: 4px;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }

        .external-link-btn:hover {
            background-color: #f1f5f9;
            color: #0284c7;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #ffffff;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 500;
            z-index: 1000;
            box-shadow: 0 -1px 4px rgba(0,0,0,0.05);
            border-top: 1px solid #e2e8f0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100%;
            background-color: #ffffff;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            transform: translate3d(-100%, 0, 0);
            transition: transform 0.25s cubic-bezier(0, 0, 0.2, 1);
            will-change: transform;
            z-index: 1100;
            display: flex;
            flex-direction: column;
        }

        .sidebar.active {
            transform: translate3d(0, 0, 0);
        }

        .sidebar-header {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-header img {
            max-height: 45px;
            max-width: 100%;
            object-fit: contain;
        }

        .sidebar-menu-container {
            flex: 1;
            overflow-y: auto;
            padding: 15px 10px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            padding: 12px 16px;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sidebar-menu li a:hover {
            background-color: #f8fafc;
            color: #0284c7;
        }

        .sidebar-menu li a.active {
            background-color: #f0f9ff;
            color: #0284c7;
            font-weight: 600;
        }

        .menu-icon-svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid #f1f5f9;
            background-color: #fafafa;
            text-align: center;
        }

        .timestamp-date {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .timestamp-time {
            font-size: 13px;
            color: #0f172a;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s cubic-bezier(0, 0, 0.2, 1);
            will-change: opacity;
            z-index: 1050;
        }

        .overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .container {
            padding: 15px;
            max-width: 1440px;
            margin: 0 auto;
        }

        .menu-content {
            display: none;
        }

        .menu-content.active {
            display: block;
        }

        .search-container {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .search-input {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-textarea {
            width: 100%;
            height: 80px;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            outline: none;
            resize: vertical;
            transition: border-color 0.2s;
        }

        .search-input:focus, .search-textarea:focus, .filter-select:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .filter-select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            color: #334155;
            outline: none;
        }

        .filter-row-2 {
            display: flex;
            gap: 8px;
        }

        .btn-action-group {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .btn-submit {
            flex: 1;
            background-color: #0284c7;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #0369a1;
        }

        .btn-reset {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-reset:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        .btn-scan-camera {
            background-color: #0284c7;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background-color 0.2s ease;
        }

        .btn-scan-camera:hover {
            background-color: #0369a1;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            align-items: stretch;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: auto;
            box-sizing: border-box;
        }

        .card-body-wrapper {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            width: 100%;
        }

        .card-left-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            text-align: left;
        }

        .card-right-img {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f1f5f9;
            border-radius: 6px;
            overflow: hidden;
            background-color: #fafafa;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-right-img:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .card-right-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .pop-card-top-img {
            width: 110px;
            height: 110px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            overflow: hidden;
            background-color: #fafafa;
            margin: 0 auto;
            cursor: pointer;
        }

        .pop-card-top-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .val-plu {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
        }

        .val-deskripsi {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.3;
        }

        .rak-grid-container {
            display: flex;
            flex-direction: column;
            gap: 6px;
            width: 100%;
            margin-top: 4px;
        }

        .rak-grid-row-modis {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
        }

        .rak-grid-row-detail {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            width: 100%;
        }

        .rak-grid-item {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .rak-grid-label {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .rak-grid-value {
            font-size: 13px;
            font-weight: 700;
            color: #0284c7;
        }

        .barcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            width: 100%;
            margin: 4px 0;
        }

        .barcode-svg {
            max-width: 100%;
            height: auto;
        }

        .btn-toggle-barcode {
            background: none;
            border: none;
            color: #0284c7;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-toggle-barcode:hover {
            text-decoration: underline;
        }

        .extra-barcodes {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            width: 100%;
        }

        .val-harga-normal {
            font-size: 15px;
            font-weight: 700;
            color: #16a34a;
        }

        .val-harga-normal.strikethrough {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
        }

        .val-harga-promo {
            font-size: 15px;
            font-weight: 700;
            color: #dc2626;
        }

        .val-periode-promo {
            font-size: 12px;
            color: #475569;
        }

        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-inactive {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }

        .promo-box {
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            border-radius: 6px;
            padding: 8px;
            margin-top: auto;
            width: 100%;
        }

        .promo-item {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #fde68a;
            font-size: 12px;
            color: #78350f;
            text-align: left;
        }

        .promo-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .extra-promo {
            display: none;
        }

        .btn-toggle-promo {
            background: none;
            border: none;
            color: #0284c7;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            padding: 4px 0 0 0;
            width: 100%;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-toggle-promo:hover {
            text-decoration: underline;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 15px;
            margin-bottom: 5px;
            flex-wrap: wrap;
            width: 100%;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #334155;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            min-width: 36px;
            cursor: pointer;
        }

        .pagination-btn:hover:not(.disabled):not(.active) {
            background-color: #f8fafc;
            border-color: #0284c7;
            color: #0284c7;
        }

        .pagination-btn.active {
            background-color: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
            font-weight: 600;
        }

        .pagination-btn.disabled {
            color: #cbd5e1;
            border-color: #e2e8f0;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 4px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .img-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
            padding: 20px;
            box-sizing: border-box;
        }

        .img-modal.show {
            display: flex;
            opacity: 1;
        }

        .img-modal-content {
            max-width: 90%;
            max-height: 85%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            background-color: #ffffff;
            transform: scale(0.9);
            transition: transform 0.25s ease;
        }

        .img-modal.show .img-modal-content {
            transform: scale(1);
        }

        .img-modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .img-modal-close:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .scanner-modal-body {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            position: relative;
        }

        #reader {
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
        }

        .pop-overlay {
            display: none;
            position: fixed;
            z-index: 1500;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(3px);
            align-items: center;
            justify-content: center;
            padding: 15px;
            box-sizing: border-box;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .pop-overlay.show {
            display: flex;
            opacity: 1;
        }

        .pop-card-content {
            background: #ffffff;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            height: 540px;
            padding: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 10px;
            position: relative;
            transform: scale(0.9);
            transition: transform 0.25s ease;
            box-sizing: border-box;
        }

        .pop-card-body {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            overflow-y: auto;
        }

        .pop-overlay.show .pop-card-content {
            transform: scale(1);
        }

        .pop-close-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f1f5f9;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            z-index: 10;
        }

        .pop-close-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .qrcode-display-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }

        .qrcode-result-card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
        }

        #qrcode_box {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
        }

        #qrcode_box img {
            display: block;
        }

        #barcode_box {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
            width: 100%;
            overflow-x: auto;
        }

        .qrcode-label-text {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            word-break: break-all;
            text-align: center;
        }

        ul {
            margin: 0;
            padding-left: 15px;
            text-align: right;
        }

        .error {
            color: #dc2626;
            font-weight: 500;
            padding: 10px;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            text-align: center;
            font-size: 13px;
        }

        .info {
            padding: 10px;
            background-color: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            text-align: center;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <?php 
    $page = $_REQUEST['page'] ?? 'cek_harga';
    $keyword = trim($_GET['keyword'] ?? '');
    $modis_filter = trim($_GET['modis'] ?? '');
    $dari_shelfing = trim($_GET['dari_shelfing'] ?? '');
    $sampai_shelfing = trim($_GET['sampai_shelfing'] ?? '');

    $current_p = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    $nama_file = 'data_produk.json';
    $produk_list = [];

    if (file_exists($nama_file)) {
        $json_data = file_get_contents($nama_file);
        $produk_list = json_decode($json_data, true) ?? [];
    }

    $modis_options = [];
    $shelfing_options = [];

    foreach ($produk_list as $p_item) {
        if (!empty($p_item['modis']) && is_array($p_item['modis'])) {
            foreach ($p_item['modis'] as $m) {
                if ($m !== '') $modis_options[$m] = $m;
            }
        }
        if (!empty($p_item['shelfing']) && is_array($p_item['shelfing'])) {
            foreach ($p_item['shelfing'] as $s) {
                if ($s !== '') $shelfing_options[$s] = $s;
            }
        }
        if (!empty($p_item['lokasi_rak_detail']) && is_array($p_item['lokasi_rak_detail'])) {
            foreach ($p_item['lokasi_rak_detail'] as $rak) {
                if (!empty($rak['modis'])) $modis_options[$rak['modis']] = $rak['modis'];
                if (!empty($rak['shelfing'])) $shelfing_options[$rak['shelfing']] = $rak['shelfing'];
            }
        }
    }

    sort($modis_options, SORT_NATURAL);
    sort($shelfing_options, SORT_NATURAL);

    $page_titles = [
        'cek_harga' => 'Cek Harga Produk',
        'scan_itt' => 'Scan ITT PJR',
        'generate_qr' => 'Generate QRCode',
        'generate_barcode' => 'Generate Barcode'
    ];
    $current_title = $page_titles[$page] ?? 'Cek Harga Produk';
    ?>

    <header>
        <button class="hamburger-btn" id="hamburgerBtn">
            <svg class="menu-icon-svg" viewBox="0 0 24 24"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>
        <div class="header-title" id="headerTitle">
            <?= htmlspecialchars($current_title) ?>
        </div>
        <a href="https://indomarc.github.io/index/" class="external-link-btn" title="Open Link">
            <svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        </a>
    </header>

    <div class="overlay" id="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="indomaret.PNG" alt="Logo Indomaret" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span style="display:none; font-weight:700; color:#0284c7;">INDOMARET</span>
        </div>
        <div class="sidebar-menu-container">
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="menu-link <?= $page === 'cek_harga' ? 'active' : '' ?>" data-target="cek_harga" data-title="Cek Harga Produk">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                        <span>Cek Harga Produk</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link <?= $page === 'scan_itt' ? 'active' : '' ?>" data-target="scan_itt" data-title="Scan ITT PJR">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="5" height="5" x="7" y="7" rx="1"/><rect width="5" height="5" x="12" y="12" rx="1"/></svg>
                        <span>Scan ITT PJR</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link <?= $page === 'generate_qr' ? 'active' : '' ?>" data-target="generate_qr" data-title="Generate QRCode">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                        <span>Generate QRCode</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link <?= $page === 'generate_barcode' ? 'active' : '' ?>" data-target="generate_barcode" data-title="Generate Barcode">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M3 5v14"/><path d="M8 5v14"/><path d="M12 5v14"/><path d="M17 5v14"/><path d="M21 5v14"/></svg>
                        <span>Generate Barcode</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidebar-footer">
            <div class="timestamp-date" id="rtDate">-</div>
            <div class="timestamp-time" id="rtTime">00:00:00 WIB</div>
        </div>
    </div>

    <!-- Modal Zoom Gambar -->
    <div class="img-modal" id="imgModal" onclick="closeImageModal()">
        <button class="img-modal-close" onclick="closeImageModal()"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        <img class="img-modal-content" id="imgModalSrc" src="" alt="Zoom Gambar Produk" referrerpolicy="no-referrer">
    </div>

    <!-- Modal Camera Scanner -->
    <div class="img-modal" id="scannerModal">
        <div class="scanner-modal-body">
            <button class="img-modal-close" style="top:10px; right:10px; color:#333; background:#e2e8f0;" onclick="closeScannerModal()"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            <div style="font-weight:600; font-size:15px; color:#0f172a; margin-top:5px;">Arahkan Kamera ke Barcode</div>
            <div id="reader"></div>
        </div>
    </div>

    <div class="container">
        
        <!-- Menu Cek Harga Produk -->
        <div id="cek_harga" class="menu-content <?= $page === 'cek_harga' ? 'active' : '' ?>">
            <form method="GET" action="" class="search-container search-form" data-menu="cek_harga" id="form_cek_harga">
                <input type="hidden" name="page" value="cek_harga">
                <div class="search-input-group">
                    <input 
                        type="text" 
                        name="keyword" 
                        id="input_cek_harga"
                        class="search-input" 
                        placeholder="Ketik PLU, Barcode, Nama Produk..." 
                        autocomplete="off"
                        value="<?= $page === 'cek_harga' ? htmlspecialchars($keyword) : '' ?>"
                    >
                    <button type="button" class="btn-scan-camera" onclick="openScannerModal('form_cek_harga', 'input_cek_harga')" title="Scan via Kamera">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                    </button>
                </div>
            </form>

            <div class="result-area">
                <?php
                if ($page === 'cek_harga' && !empty($keyword)) {
                    render_results($produk_list, $keyword, $nama_file, 'cek_harga', $current_p, '', '', '');
                } else {
                    echo "<p class='info'>Silakan scan barcode atau masukkan kata kunci lalu tekan Enter untuk menampilkan data.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Menu Scan ITT PJR -->
        <div id="scan_itt" class="menu-content <?= $page === 'scan_itt' ? 'active' : '' ?>">
            <form method="GET" action="" class="search-container search-form" data-menu="scan_itt" id="form_scan_itt">
                <input type="hidden" name="page" value="scan_itt">
                
                <textarea 
                    name="keyword" 
                    id="input_scan_itt"
                    class="search-textarea" 
                    placeholder="Ketik PLU / Barcode ( pisahkan dengan spasi atau enter )..."
                    autocomplete="off"
                ><?= $page === 'scan_itt' ? htmlspecialchars($keyword) : '' ?></textarea>

                <div class="filter-group">
                    <select name="modis" class="filter-select">
                        <option value="">Pilih Modis</option>
                        <?php foreach ($modis_options as $m_opt): ?>
                            <option value="<?= htmlspecialchars($m_opt) ?>" <?= $modis_filter === $m_opt ? 'selected' : '' ?>>
                                <?= htmlspecialchars($m_opt) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="filter-row-2">
                        <select name="dari_shelfing" class="filter-select">
                            <option value="">Dari Shelfing</option>
                            <?php foreach ($shelfing_options as $s_opt): ?>
                                <option value="<?= htmlspecialchars($s_opt) ?>" <?= $dari_shelfing === $s_opt ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s_opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="sampai_shelfing" class="filter-select">
                            <option value="">Sampai Shelfing</option>
                            <?php foreach ($shelfing_options as $s_opt): ?>
                                <option value="<?= htmlspecialchars($s_opt) ?>" <?= $sampai_shelfing === $s_opt ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s_opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="btn-action-group">
                    <button type="submit" class="btn-submit">Tampilkan Data</button>
                    <a href="?page=scan_itt" class="btn-reset">Reset</a>
                </div>
            </form>

            <div class="result-area">
                <?php
                $is_scan_itt_submitted = !empty($keyword) || !empty($modis_filter) || !empty($dari_shelfing) || !empty($sampai_shelfing);
                if ($page === 'scan_itt' && $is_scan_itt_submitted) {
                    render_results($produk_list, $keyword, $nama_file, 'scan_itt', $current_p, $modis_filter, $dari_shelfing, $sampai_shelfing);
                } else {
                    echo "<p class='info'>Silakan isi kata kunci atau pilih filter lalu tekan Tampilkan Data.</p>";
                }
                ?>
            </div>
        </div>

        <!-- Menu Generate QRCode -->
        <div id="generate_qr" class="menu-content <?= $page === 'generate_qr' ? 'active' : '' ?>">
            <div class="search-container">
                <textarea 
                    id="qr_text_input" 
                    class="search-textarea" 
                    placeholder="Ketik teks disini ( pisahkan dengan spasi atau enter )..."
                    autocomplete="off"
                ></textarea>

                <div class="btn-action-group">
                    <button type="button" class="btn-submit" onclick="generateQRCode()">Generate</button>
                    <button type="button" class="btn-reset" onclick="resetQRCode()">Reset</button>
                </div>
            </div>

            <div class="result-area">
                <div class="qrcode-display-wrapper" id="qr_display_wrapper" style="display: none;">
                    <div class="qrcode-result-card">
                        <div id="qrcode_box"></div>
                        <div class="qrcode-label-text" id="qrcode_label_text"></div>
                    </div>
                    <div class="pagination-container" id="qr_pagination_container" style="display: none; margin-top: 0; padding-top: 0; border-top: none;"></div>
                </div>
            </div>
        </div>

        <!-- Menu Generate Barcode -->
        <div id="generate_barcode" class="menu-content <?= $page === 'generate_barcode' ? 'active' : '' ?>">
            <div class="search-container">
                <textarea 
                    id="barcode_text_input" 
                    class="search-textarea" 
                    placeholder="Ketik teks disini ( pisahkan dengan spasi atau enter )..."
                    autocomplete="off"
                ></textarea>

                <div class="btn-action-group">
                    <button type="button" class="btn-submit" onclick="generateBarcode()">Generate</button>
                    <button type="button" class="btn-reset" onclick="resetBarcode()">Reset</button>
                </div>
            </div>

            <div class="result-area">
                <div class="qrcode-display-wrapper" id="barcode_display_wrapper" style="display: none;">
                    <div class="qrcode-result-card">
                        <div id="barcode_box">
                            <svg id="barcode_output_svg" class="barcode-svg"></svg>
                        </div>
                        <div class="qrcode-label-text" id="barcode_label_text"></div>
                    </div>
                    <div class="pagination-container" id="barcode_pagination_container" style="display: none; margin-top: 0; padding-top: 0; border-top: none;"></div>
                </div>
            </div>
        </div>

    </div>

    <footer>
        ~ m.h.r ~
    </footer>

    <?php
    function render_results($produk_list, $keyword, $nama_file, $menu_type = 'cek_harga', $current_p = 1, $modis_filter = '', $dari_shelfing = '', $sampai_shelfing = '') {
        if (!file_exists($nama_file)) {
            echo "<p class='error'>File <strong>$nama_file</strong> tidak ditemukan!</p>";
            return;
        }

        $keywords = [];
        if (!empty($keyword)) {
            $raw_tokens = preg_split('/[\s\r\n]+/', strtolower($keyword));
            foreach ($raw_tokens as $t) {
                $t = trim($t);
                if ($t !== '') $keywords[] = $t;
            }
        }

        $filtered_results = [];

        foreach ($produk_list as $item) {
            $plu = strtolower($item['plu'] ?? '');
            $deskripsi = strtolower($item['deskripsi'] ?? '');
            $barcodes = array_map('strtolower', $item['barcode'] ?? []);

            $cocok_keyword = true;
            if (!empty($keywords)) {
                $found_match = false;
                foreach ($keywords as $kw) {
                    $cocok_plu = strpos($plu, $kw) !== false;
                    $cocok_deskripsi = strpos($deskripsi, $kw) !== false;
                    $cocok_barcode = false;

                    foreach ($barcodes as $bc) {
                        if (strpos($bc, $kw) !== false) {
                            $cocok_barcode = true;
                            break;
                        }
                    }

                    if ($cocok_plu || $cocok_deskripsi || $cocok_barcode) {
                        $found_match = true;
                        break;
                    }
                }
                $cocok_keyword = $found_match;
            }

            if (!$cocok_keyword) {
                continue;
            }

            if ($menu_type === 'cek_harga') {
                $filtered_results[] = $item;
                continue;
            }

            $item_rak_list = [];
            if (!empty($item['lokasi_rak_detail']) && is_array($item['lokasi_rak_detail'])) {
                $item_rak_list = $item['lokasi_rak_detail'];
            } else {
                $modis_arr = $item['modis'] ?? [''];
                $shelf_arr = $item['shelfing'] ?? [''];
                $baris_arr = $item['baris'] ?? [''];
                
                $max_len = max(count($modis_arr), count($shelf_arr), count($baris_arr));
                for ($i = 0; $i < $max_len; $i++) {
                    $item_rak_list[] = [
                        'modis' => $modis_arr[$i] ?? ($modis_arr[0] ?? ''),
                        'shelfing' => $shelf_arr[$i] ?? ($shelf_arr[0] ?? ''),
                        'baris' => $baris_arr[$i] ?? ($baris_arr[0] ?? '')
                    ];
                }
            }

            $matched_raks = [];

            foreach ($item_rak_list as $rak) {
                $r_modis = $rak['modis'] ?? '';
                $r_shelf = (int)($rak['shelfing'] ?? 0);

                $cocok_m = empty($modis_filter) || ($r_modis === $modis_filter);

                $from_val = !empty($dari_shelfing) ? (int)$dari_shelfing : 0;
                $to_val = !empty($sampai_shelfing) ? (int)$sampai_shelfing : 999999;
                $cocok_s = (empty($dari_shelfing) && empty($sampai_shelfing)) || ($r_shelf >= $from_val && $r_shelf <= $to_val);

                if ($cocok_m && $cocok_s) {
                    $matched_raks[] = $rak;
                }
            }

            if (!empty($matched_raks)) {
                $item_copy = $item;
                $item_copy['matched_raks'] = $matched_raks;
                $filtered_results[] = $item_copy;
            }
        }

        if (!empty($filtered_results)) {
            $hasil_pencarian = array_values($filtered_results);

            if ($menu_type === 'scan_itt') {
                usort($hasil_pencarian, function($a, $b) {
                    $a_modis = $a['matched_raks'][0]['modis'] ?? '';
                    $b_modis = $b['matched_raks'][0]['modis'] ?? '';
                    if ($a_modis !== $b_modis) {
                        return strnatcmp($a_modis, $b_modis);
                    }

                    $a_shelf = (int)($a['matched_raks'][0]['shelfing'] ?? 0);
                    $b_shelf = (int)($b['matched_raks'][0]['shelfing'] ?? 0);
                    if ($a_shelf !== $b_shelf) {
                        return $a_shelf <=> $b_shelf;
                    }

                    $a_baris = (int)($a['matched_raks'][0]['baris'] ?? 0);
                    $b_baris = (int)($b['matched_raks'][0]['baris'] ?? 0);
                    return $a_baris <=> $b_baris;
                });
            }

            $per_page = ($menu_type === 'scan_itt') ? 1 : 10;
            $total_items = count($hasil_pencarian);
            $total_pages = ceil($total_items / $per_page);
            $current_p = min(max(1, $current_p), $total_pages);
            
            $offset = ($current_p - 1) * $per_page;
            $paged_items = array_slice($hasil_pencarian, $offset, $per_page);

            if ($menu_type === 'scan_itt') {
                echo '<div class="pop-overlay show" id="popOverlayScan">';
                echo '<div class="pop-card-content">';
                echo '<button class="pop-close-btn" onclick="closePopOverlay()"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>';
                echo '<div class="pop-card-body">';
            } else {
                echo '<div class="card-grid">';
            }

            foreach ($paged_items as $index => $produk) {
                $plu = $produk['plu'] ?? '';
                $img_url = "https://cdn-klik.klikindomaret.com/klik-catalog/product/{$plu}_1.jpg";
                $has_promo = isset($produk['harga_promo']) && $produk['harga_promo'] !== null;
                $card_id = "card_" . $index;
                ?>
                <?php if ($menu_type === 'cek_harga'): ?>
                    <div class="card" id="<?= $card_id ?>">
                        <div class="card-body-wrapper">
                            <div class="card-left-info">
                                <div class="val-plu"><?= htmlspecialchars($plu ?: '-') ?></div>
                                <div class="val-deskripsi"><?= htmlspecialchars($produk['deskripsi'] ?? '-') ?></div>
                                <div class="val-harga-normal <?= $has_promo ? 'strikethrough' : '' ?>">
                                    Rp <?= number_format($produk['harga_normal'] ?? 0, 0, ',', '.') ?>
                                </div>
                                <?php if ($has_promo): ?>
                                    <div class="val-harga-promo">Rp <?= number_format($produk['harga_promo'], 0, ',', '.') ?></div>
                                <?php endif; ?>
                                <?php if (!empty($produk['periode_promo'])): ?>
                                    <div class="val-periode-promo"><?= htmlspecialchars($produk['periode_promo']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="card-right-img" onclick="openImageModal('<?= htmlspecialchars($img_url) ?>')">
                                <img src="<?= htmlspecialchars($img_url) ?>" alt="Gambar Produk" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23cbd5e1\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect width=\'18\' height=\'18\' x=\'3\' y=\'3\' rx=\'2\' ry=\'2\'/><circle cx=\'9\' cy=\'9\' r=\'2\'/><path d=\'m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21\'/></svg>';">
                            </div>
                        </div>

                        <?php if (!empty($produk['detail_promo']) && is_array($produk['detail_promo'])): ?>
                            <div class="promo-box">
                                <?php 
                                $detail_promos = $produk['detail_promo'];
                                $first_promo = $detail_promos[0];
                                $extra_promos = array_slice($detail_promos, 1);
                                
                                $status_badge_first = !empty($first_promo['is_active']) 
                                    ? "<span class='badge-active'>Aktif</span>" 
                                    : "<span class='badge-inactive'>Non-Aktif</span>";
                                ?>
                                <div class="promo-item">
                                    <div><?= htmlspecialchars($first_promo['mekanisme'] ?? '-') ?></div>
                                    <div style="margin-top: 3px;">
                                        <small><?= htmlspecialchars($first_promo['tanggal_awal'] ?? '-') ?> - <?= htmlspecialchars($first_promo['tanggal_akhir'] ?? '-') ?> <?= $status_badge_first ?></small>
                                    </div>
                                </div>

                                <?php if (!empty($extra_promos)): ?>
                                    <div class="extra-promo" id="extra_<?= $card_id ?>">
                                        <?php foreach ($extra_promos as $promo): ?>
                                            <?php 
                                            $status_badge = !empty($promo['is_active']) 
                                                ? "<span class='badge-active'>Aktif</span>" 
                                                : "<span class='badge-inactive'>Non-Aktif</span>";
                                            ?>
                                            <div class="promo-item">
                                                <div><?= htmlspecialchars($promo['mekanisme'] ?? '-') ?></div>
                                                <div style="margin-top: 3px;">
                                                    <small><?= htmlspecialchars($promo['tanggal_awal'] ?? '-') ?> - <?= htmlspecialchars($promo['tanggal_akhir'] ?? '-') ?> <?= $status_badge ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button type="button" class="btn-toggle-promo" onclick="toggleExtraPromo('<?= $card_id ?>', this)">
                                        <span>Tampilkan Detail Promo Lainnya (<?= count($extra_promos) ?>)</span>
                                        <svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:14px; height:14px;"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <div class="pop-card-top-img" onclick="openImageModal('<?= htmlspecialchars($img_url) ?>')">
                        <img src="<?= htmlspecialchars($img_url) ?>" alt="Gambar Produk" referrerpolicy="no-referrer" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23cbd5e1\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect width=\'18\' height=\'18\' x=\'3\' y=\'3\' rx=\'2\' ry=\'2\'/><circle cx=\'9\' cy=\'9\' r=\'2\'/><path d=\'m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21\'/></svg>';">
                    </div>

                    <div class="val-plu"><?= htmlspecialchars($plu ?: '-') ?></div>
                    <div class="val-deskripsi"><?= htmlspecialchars($produk['deskripsi'] ?? '-') ?></div>

                    <div class="barcode-container">
                        <?php 
                        $all_barcodes = $produk['barcode'] ?? [];
                        if (!empty($all_barcodes) && is_array($all_barcodes)) {
                            $first_bc = $all_barcodes[0];
                            $extra_bc = array_slice($all_barcodes, 1);

                            $svg_id_first = "bc_pop_" . $index . "_0";
                            echo "<svg id='{$svg_id_first}' class='barcode-svg' data-barcode='" . htmlspecialchars($first_bc) . "'></svg>";

                            if (!empty($extra_bc)) {
                                echo "<div class='extra-barcodes' id='extra_bc_box_{$card_id}'>";
                                foreach ($extra_bc as $eb_idx => $ebc) {
                                    $svg_id_extra = "bc_pop_" . $index . "_" . ($eb_idx + 1);
                                    echo "<svg id='{$svg_id_extra}' class='barcode-svg' data-barcode='" . htmlspecialchars($ebc) . "'></svg>";
                                }
                                echo "</div>";

                                echo "<button type='button' class='btn-toggle-barcode' onclick='toggleExtraBarcode(\"{$card_id}\", this)'>";
                                echo "<span>Tampilkan Barcode Lainnya (" . count($extra_bc) . ")</span>";
                                echo "<svg class='menu-icon-svg' viewBox='0 0 24 24' style='width:14px; height:14px;'><path d='m6 9 6 6 6-6'/></svg>";
                                echo "</button>";
                            }
                        } else {
                            echo "<span style='font-size:12px; color:#94a3b8;'>-</span>";
                        }
                        ?>
                    </div>

                    <?php
                    $matched_raks = $produk['matched_raks'] ?? [];
                    
                    $m_unique = [];
                    $s_unique = [];
                    $b_unique = [];

                    foreach ($matched_raks as $rak) {
                        if (!empty($rak['modis'])) $m_unique[] = $rak['modis'];
                        if (isset($rak['shelfing'])) $s_unique[] = $rak['shelfing'];
                        if (isset($rak['baris'])) $b_unique[] = $rak['baris'];
                    }

                    $modis_txt = !empty($m_unique) ? implode(', ', array_unique($m_unique)) : '-';
                    $shelf_txt = !empty($s_unique) ? implode(', ', array_unique($s_unique)) : '-';
                    $baris_txt = !empty($b_unique) ? implode(', ', array_unique($b_unique)) : '-';
                    ?>

                    <div class="rak-grid-container">
                        <div class="rak-grid-row-modis">
                            <div class="rak-grid-item">
                                <span class="rak-grid-label">Modis</span>
                                <span class="rak-grid-value"><?= htmlspecialchars($modis_txt) ?></span>
                            </div>
                        </div>
                        <div class="rak-grid-row-detail">
                            <div class="rak-grid-item">
                                <span class="rak-grid-label">Shelfing</span>
                                <span class="rak-grid-value"><?= htmlspecialchars($shelf_txt) ?></span>
                            </div>
                            <div class="rak-grid-item">
                                <span class="rak-grid-label">Baris</span>
                                <span class="rak-grid-value"><?= htmlspecialchars($baris_txt) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
            }

            if ($menu_type === 'scan_itt') {
                echo '</div>';
            }

            if ($total_pages > 1) {
                $query_params = $_GET;
                echo '<div class="pagination-container">';
                
                $query_params['p'] = $current_p - 1;
                $prev_url = '?' . http_build_query($query_params);
                if ($current_p > 1) {
                    echo "<a href='{$prev_url}' class='pagination-btn'><svg class='menu-icon-svg' viewBox='0 0 24 24' style='width:16px;height:16px;'><path d='m15 18-6-6 6-6'/></svg></a>";
                } else {
                    echo "<span class='pagination-btn disabled'><svg class='menu-icon-svg' viewBox='0 0 24 24' style='width:16px;height:16px;'><path d='m15 18-6-6 6-6'/></svg></span>";
                }

                if ($total_pages <= 3) {
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $query_params['p'] = $i;
                        $page_url = '?' . http_build_query($query_params);
                        $active_class = ($i === $current_p) ? 'active' : '';
                        echo "<a href='{$page_url}' class='pagination-btn {$active_class}'>{$i}</a>";
                    }
                } else {
                    if ($current_p <= 2) {
                        for ($i = 1; $i <= 3; $i++) {
                            $query_params['p'] = $i;
                            $page_url = '?' . http_build_query($query_params);
                            $active_class = ($i === $current_p) ? 'active' : '';
                            echo "<a href='{$page_url}' class='pagination-btn {$active_class}'>{$i}</a>";
                        }
                        echo "<span class='pagination-ellipsis'>...</span>";
                    } elseif ($current_p >= $total_pages - 1) {
                        $query_params['p'] = 1;
                        $page_url = '?' . http_build_query($query_params);
                        echo "<a href='{$page_url}' class='pagination-btn'>1</a>";
                        echo "<span class='pagination-ellipsis'>...</span>";

                        for ($i = $total_pages - 2; $i <= $total_pages; $i++) {
                            $query_params['p'] = $i;
                            $page_url = '?' . http_build_query($query_params);
                            $active_class = ($i === $current_p) ? 'active' : '';
                            echo "<a href='{$page_url}' class='pagination-btn {$active_class}'>{$i}</a>";
                        }
                    } else {
                        $query_params['p'] = 1;
                        $page_url = '?' . http_build_query($query_params);
                        echo "<a href='{$page_url}' class='pagination-btn'>1</a>";
                        echo "<span class='pagination-ellipsis'>...</span>";

                        $query_params['p'] = $current_p;
                        $page_url = '?' . http_build_query($query_params);
                        echo "<a href='{$page_url}' class='pagination-btn active'>{$current_p}</a>";

                        echo "<span class='pagination-ellipsis'>...</span>";
                    }
                }

                $query_params['p'] = $current_p + 1;
                $next_url = '?' . http_build_query($query_params);
                if ($current_p < $total_pages) {
                    echo "<a href='{$next_url}' class='pagination-btn'><svg class='menu-icon-svg' viewBox='0 0 24 24' style='width:16px;height:16px;'><path d='m9 18 6-6-6-6'/></svg></a>";
                } else {
                    echo "<span class='pagination-btn disabled'><svg class='menu-icon-svg' viewBox='0 0 24 24' style='width:16px;height:16px;'><path d='m9 18 6-6-6-6'/></svg></span>";
                }

                echo '</div>';
            }

            if ($menu_type === 'scan_itt') {
                echo '</div>';
                echo '</div>';
            } else {
                echo '</div>';
            }
        } else {
            echo "<p class='info'>Data produk tidak ditemukan berdasarkan kriteria pencarian/filter.</p>";
        }
    }
    ?>

    <script>
        let qrList = [];
        let currentQrIndex = 0;

        let barcodeList = [];
        let currentBarcodeIndex = 0;

        function formatCodeText(str) {
            if (str.includes(':')) {
                const parts = str.split(':');
                const part1 = parts[0].trim();
                const part2 = parts[1].trim();
                const paddedPart2 = part2.padStart(4, '0');
                return 'B' + part1 + paddedPart2;
            }
            return str;
        }

        function generateQRCode() {
            const rawText = document.getElementById('qr_text_input').value;
            const qrWrapper = document.getElementById('qr_display_wrapper');

            const tokens = rawText.split(/[\s\r\n]+/).map(t => t.trim()).filter(t => t !== '');
            qrList = tokens.map(t => formatCodeText(t));

            if (qrList.length === 0) {
                alert('Silakan masukkan teks terlebih dahulu.');
                return;
            }

            currentQrIndex = 0;
            qrWrapper.style.display = 'flex';
            renderCurrentQRCode();
        }

        function renderCurrentQRCode() {
            const qrBox = document.getElementById('qrcode_box');
            const qrLabel = document.getElementById('qrcode_label_text');
            const navContainer = document.getElementById('qr_pagination_container');

            qrBox.innerHTML = '';
            const textToRender = qrList[currentQrIndex];
            qrLabel.textContent = textToRender;

            new QRCode(qrBox, {
                text: textToRender,
                width: 180,
                height: 180,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            if (qrList.length > 1) {
                navContainer.style.display = 'flex';
                renderQrPagination(navContainer);
            } else {
                navContainer.style.display = 'none';
            }
        }

        function renderQrPagination(container) {
            let html = '';
            const total = qrList.length;

            if (currentQrIndex > 0) {
                html += `<button type="button" class="pagination-btn" onclick="goToQrPage(${currentQrIndex - 1})"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m15 18-6-6 6-6"/></svg></button>`;
            } else {
                html += `<span class="pagination-btn disabled"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m15 18-6-6 6-6"/></svg></span>`;
            }

            if (total <= 3) {
                for (let i = 0; i < total; i++) {
                    const activeClass = (i === currentQrIndex) ? 'active' : '';
                    html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToQrPage(${i})">${i + 1}</button>`;
                }
            } else {
                if (currentQrIndex <= 1) {
                    for (let i = 0; i < 3; i++) {
                        const activeClass = (i === currentQrIndex) ? 'active' : '';
                        html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToQrPage(${i})">${i + 1}</button>`;
                    }
                    html += `<span class="pagination-ellipsis">...</span>`;
                } else if (currentQrIndex >= total - 2) {
                    html += `<button type="button" class="pagination-btn" onclick="goToQrPage(0)">1</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                    for (let i = total - 3; i < total; i++) {
                        const activeClass = (i === currentQrIndex) ? 'active' : '';
                        html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToQrPage(${i})">${i + 1}</button>`;
                    }
                } else {
                    html += `<button type="button" class="pagination-btn" onclick="goToQrPage(0)">1</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                    html += `<button type="button" class="pagination-btn active" onclick="goToQrPage(${currentQrIndex})">${currentQrIndex + 1}</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                }
            }

            if (currentQrIndex < total - 1) {
                html += `<button type="button" class="pagination-btn" onclick="goToQrPage(${currentQrIndex + 1})"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m9 18 6-6-6-6"/></svg></button>`;
            } else {
                html += `<span class="pagination-btn disabled"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m9 18 6-6-6-6"/></svg></span>`;
            }

            container.innerHTML = html;
        }

        function goToQrPage(index) {
            currentQrIndex = index;
            renderCurrentQRCode();
        }

        function resetQRCode() {
            document.getElementById('qr_text_input').value = '';
            document.getElementById('qrcode_box').innerHTML = '';
            document.getElementById('qrcode_label_text').textContent = '';
            document.getElementById('qr_pagination_container').innerHTML = '';
            document.getElementById('qr_pagination_container').style.display = 'none';
            document.getElementById('qr_display_wrapper').style.display = 'none';
            qrList = [];
            currentQrIndex = 0;
        }

        function generateBarcode() {
            const rawText = document.getElementById('barcode_text_input').value;
            const barcodeWrapper = document.getElementById('barcode_display_wrapper');

            const tokens = rawText.split(/[\s\r\n]+/).map(t => t.trim()).filter(t => t !== '');
            barcodeList = tokens.map(t => formatCodeText(t));

            if (barcodeList.length === 0) {
                alert('Silakan masukkan teks terlebih dahulu.');
                return;
            }

            currentBarcodeIndex = 0;
            barcodeWrapper.style.display = 'flex';
            renderCurrentBarcode();
        }

        function renderCurrentBarcode() {
            const barcodeSvg = document.getElementById('barcode_output_svg');
            const barcodeLabel = document.getElementById('barcode_label_text');
            const navContainer = document.getElementById('barcode_pagination_container');

            const textToRender = barcodeList[currentBarcodeIndex];
            barcodeLabel.textContent = textToRender;

            try {
                JsBarcode(barcodeSvg, textToRender, {
                    format: "CODE128",
                    width: 2,
                    height: 70,
                    displayValue: false,
                    margin: 10,
                    background: "#ffffff",
                    lineColor: "#0f172a"
                });
            } catch (e) {
                console.error("Gagal generate barcode:", e);
            }

            if (barcodeList.length > 1) {
                navContainer.style.display = 'flex';
                renderBarcodePagination(navContainer);
            } else {
                navContainer.style.display = 'none';
            }
        }

        function renderBarcodePagination(container) {
            let html = '';
            const total = barcodeList.length;

            if (currentBarcodeIndex > 0) {
                html += `<button type="button" class="pagination-btn" onclick="goToBarcodePage(${currentBarcodeIndex - 1})"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m15 18-6-6 6-6"/></svg></button>`;
            } else {
                html += `<span class="pagination-btn disabled"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m15 18-6-6 6-6"/></svg></span>`;
            }

            if (total <= 3) {
                for (let i = 0; i < total; i++) {
                    const activeClass = (i === currentBarcodeIndex) ? 'active' : '';
                    html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToBarcodePage(${i})">${i + 1}</button>`;
                }
            } else {
                if (currentBarcodeIndex <= 1) {
                    for (let i = 0; i < 3; i++) {
                        const activeClass = (i === currentBarcodeIndex) ? 'active' : '';
                        html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToBarcodePage(${i})">${i + 1}</button>`;
                    }
                    html += `<span class="pagination-ellipsis">...</span>`;
                } else if (currentBarcodeIndex >= total - 2) {
                    html += `<button type="button" class="pagination-btn" onclick="goToBarcodePage(0)">1</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                    for (let i = total - 3; i < total; i++) {
                        const activeClass = (i === currentBarcodeIndex) ? 'active' : '';
                        html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToBarcodePage(${i})">${i + 1}</button>`;
                    }
                } else {
                    html += `<button type="button" class="pagination-btn" onclick="goToBarcodePage(0)">1</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                    html += `<button type="button" class="pagination-btn active" onclick="goToBarcodePage(${currentBarcodeIndex})">${currentBarcodeIndex + 1}</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                }
            }

            if (currentBarcodeIndex < total - 1) {
                html += `<button type="button" class="pagination-btn" onclick="goToBarcodePage(${currentBarcodeIndex + 1})"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m9 18 6-6-6-6"/></svg></button>`;
            } else {
                html += `<span class="pagination-btn disabled"><svg class="menu-icon-svg" viewBox="0 0 24 24" style="width:16px;height:16px;"><path d="m9 18 6-6-6-6"/></svg></span>`;
            }

            container.innerHTML = html;
        }

        function goToBarcodePage(index) {
            currentBarcodeIndex = index;
            renderCurrentBarcode();
        }

        function resetBarcode() {
            document.getElementById('barcode_text_input').value = '';
            document.getElementById('barcode_label_text').textContent = '';
            document.getElementById('barcode_pagination_container').innerHTML = '';
            document.getElementById('barcode_pagination_container').style.display = 'none';
            document.getElementById('barcode_display_wrapper').style.display = 'none';
            barcodeList = [];
            currentBarcodeIndex = 0;
        }

        function renderBarcodes() {
            const svgElements = document.querySelectorAll('.barcode-svg');
            svgElements.forEach(svg => {
                if (svg.id === 'barcode_output_svg') return;
                const val = svg.getAttribute('data-barcode');
                if (val) {
                    try {
                        JsBarcode(svg, val, {
                            format: "CODE128",
                            width: 1.5,
                            height: 38,
                            displayValue: true,
                            fontSize: 12,
                            margin: 2,
                            background: "#ffffff",
                            lineColor: "#0f172a"
                        });
                    } catch (e) {
                        console.error("Gagal generate barcode:", e);
                    }
                }
            });
        }

        function toggleExtraBarcode(cardId, btn) {
            const extraBox = document.getElementById('extra_bc_box_' + cardId);
            const spanText = btn.querySelector('span');

            if (extraBox.style.display === 'flex') {
                extraBox.style.display = 'none';
                spanText.textContent = spanText.textContent.replace('Sembunyikan', 'Tampilkan Barcode Lainnya');
            } else {
                extraBox.style.display = 'flex';
                spanText.textContent = spanText.textContent.replace('Tampilkan Barcode Lainnya', 'Sembunyikan');
            }
        }

        function closePopOverlay() {
            const popOverlay = document.getElementById('popOverlayScan');
            if (popOverlay) {
                popOverlay.classList.remove('show');
            }
        }

        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const headerTitle = document.getElementById('headerTitle');
        const menuLinks = document.querySelectorAll('.menu-link');
        const menuContents = document.querySelectorAll('.menu-content');
        const imgModal = document.getElementById('imgModal');
        const imgModalSrc = document.getElementById('imgModalSrc');
        const scannerModal = document.getElementById('scannerModal');

        let html5QrcodeScanner = null;
        let activeTargetFormId = null;
        let activeTargetInputId = null;

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        hamburgerBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        menuLinks.forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                const target = link.getAttribute('data-target');
                const title = link.getAttribute('data-title');

                menuLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                menuContents.forEach(content => {
                    if (content.id === target) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });

                headerTitle.textContent = title;

                const activeInput = document.querySelector(`#${target} .search-input`) || document.querySelector(`#${target} .search-textarea`) || document.querySelector(`#${target} #qr_text_input`) || document.querySelector(`#${target} #barcode_text_input`);
                if (activeInput) {
                    activeInput.focus();
                }

                toggleSidebar();
            });
        });

        function toggleExtraPromo(cardId, btn) {
            const extraContainer = document.getElementById('extra_' + cardId);
            const spanText = btn.querySelector('span');

            if (extraContainer.style.display === 'block') {
                extraContainer.style.display = 'none';
                spanText.textContent = spanText.textContent.replace('Sembunyikan', 'Tampilkan Detail Promo Lainnya');
            } else {
                extraContainer.style.display = 'block';
                spanText.textContent = spanText.textContent.replace('Tampilkan Detail Promo Lainnya', 'Sembunyikan');
            }
        }

        function openImageModal(url) {
            if (imgModal && imgModalSrc) {
                imgModalSrc.src = url;
                imgModal.classList.add('show');
            }
        }

        function closeImageModal() {
            if (imgModal) {
                imgModal.classList.remove('show');
            }
        }

        function openScannerModal(formId, inputId) {
            activeTargetFormId = formId;
            activeTargetInputId = inputId;
            scannerModal.classList.add('show');

            html5QrcodeScanner = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 250, height: 150 } };

            html5QrcodeScanner.start(
                { facingMode: "environment" }, 
                config, 
                onScanSuccess
            ).catch(err => {
                alert("Gagal mengakses kamera: " + err);
                closeScannerModal();
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (activeTargetInputId && activeTargetFormId) {
                const targetInput = document.getElementById(activeTargetInputId);
                const targetForm = document.getElementById(activeTargetFormId);
                if (targetInput && targetForm) {
                    targetInput.value = decodedText;
                    closeScannerModal();
                    targetForm.submit();
                }
            }
        }

        function closeScannerModal() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                    scannerModal.classList.remove('show');
                }).catch(() => {
                    scannerModal.classList.remove('show');
                });
            } else {
                scannerModal.classList.remove('show');
            }
        }

        function updateRealtimeClock() {
            const now = new Date();

            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const dayName = days[now.getDay()];
            const dayNum = String(now.getDate()).padStart(2, '0');
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const dateElem = document.getElementById('rtDate');
            const timeElem = document.getElementById('rtTime');

            if (dateElem && timeElem) {
                dateElem.textContent = `${dayName}, ${dayNum} ${monthName} ${year}`;
                timeElem.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }

        setInterval(updateRealtimeClock, 1000);
        updateRealtimeClock();

        window.addEventListener('DOMContentLoaded', () => {
            renderBarcodes();

            const activeContent = document.querySelector('.menu-content.active');
            if (activeContent) {
                const activeInput = activeContent.querySelector('.search-input') || activeContent.querySelector('.search-textarea') || activeContent.querySelector('#qr_text_input') || activeContent.querySelector('#barcode_text_input');
                if (activeInput) {
                    activeInput.focus();
                    if (activeInput.value) {
                        activeInput.select();
                    }
                }
            }
        });
    </script>

</body>
</html>