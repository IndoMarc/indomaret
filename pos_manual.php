<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'get_products') {
        $jsonFile = __DIR__ . '/data_produk.json';
        $produkList = [];

        if (file_exists($jsonFile)) {
            $jsonData = file_get_contents($jsonFile);
            $parsedData = json_decode($jsonData, true);

            if (is_array($parsedData)) {
                if (isset($parsedData['data']) && is_array($parsedData['data'])) {
                    $produkList = $parsedData['data'];
                } elseif (isset($parsedData['produk']) && is_array($parsedData['produk'])) {
                    $produkList = $parsedData['produk'];
                } elseif (isset($parsedData['products']) && is_array($parsedData['products'])) {
                    $produkList = $parsedData['products'];
                } else {
                    $produkList = $parsedData;
                }
            }
        }
        
        $formattedProducts = [];
        foreach ($produkList as $index => $item) {
            if (!is_array($item)) continue;

            $pluRaw = $item['plu'] ?? '';
            $plu = !empty($pluRaw) ? (string)$pluRaw : 'ITEM_' . ($index + 1);
            
            $deskripsi = $item['deskripsi'] ?? 'Tanpa Nama';
            
            $hargaNormalRaw = preg_replace('/[^0-9.]/', '', (string)($item['harga_normal'] ?? '0'));
            $hargaPromoRaw  = preg_replace('/[^0-9.]/', '', (string)($item['harga_promo'] ?? ''));
            
            $hargaNormal = floatval($hargaNormalRaw);
            $hargaPromo  = !empty($hargaPromoRaw) ? floatval($hargaPromoRaw) : null;
            
            $harga = (!empty($hargaPromo) && $hargaPromo > 0) ? $hargaPromo : $hargaNormal;

            $rawBarcode = $item['barcode'] ?? '';
            $barcodes = is_array($rawBarcode) ? implode(',', $rawBarcode) : (string)$rawBarcode;

            $formattedProducts[] = [
                'plu' => $plu,
                'deskripsi' => $deskripsi,
                'harga' => $harga,
                'harga_normal' => $hargaNormal,
                'harga_promo' => $hargaPromo,
                'barcode' => $barcodes
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $formattedProducts]);
        exit;
    }

    if ($_POST['action'] === 'set_account') {
        $akun = trim($_POST['akun'] ?? '');
        if (in_array($akun, ['Kasir 1', 'Kasir 2'])) {
            $_SESSION['akun'] = $akun;
            echo json_encode(['status' => 'success', 'akun' => $akun]);
        } elseif ($akun === 'RESET') {
            unset($_SESSION['akun']);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Pilihan akun tidak valid.']);
        }
        exit;
    }

    $host   = 'db.fr-roub1.bengt.wasmernet.com';
    $port   = '20184';
    $dbname = 'stock_opname';
    $dbuser = 'user_a2e7c23a';
    $dbpass = 'pw_XVc32h58LGUKszLr1XCGg8R8FVDzTAcy';

    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Koneksi Database Gagal: ' . $e->getMessage()]);
        exit;
    }

    if ($_POST['action'] === 'save_transaction') {
        $akun = $_SESSION['akun'] ?? 'Kasir 1';
        $cart = json_decode($_POST['cart'] ?? '[]', true);
        $total = $_POST['total'] ?? 0;

        if (empty($cart)) {
            echo json_encode(['status' => 'error', 'message' => 'Keranjang kosong']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO transaksi (akun, total_bayar) VALUES (?, ?)");
            $stmt->execute([$akun, $total]);
            $transaksi_id = $pdo->lastInsertId();

            $stmtDetail = $pdo->prepare("INSERT INTO transaksi_detail (transaksi_id, plu, deskripsi, harga, qty, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($cart as $item) {
                $subtotal = $item['harga'] * $item['qty'];
                $stmtDetail->execute([
                    $transaksi_id,
                    $item['plu'],
                    $item['deskripsi'],
                    $item['harga'],
                    $item['qty'],
                    $subtotal
                ]);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'transaksi_id' => $transaksi_id]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($_POST['action'] === 'get_account_summary') {
        $akun = $_SESSION['akun'] ?? 'Kasir 1';
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    td.plu, 
                    td.deskripsi, 
                    SUM(td.qty) as total_qty, 
                    SUM(td.subtotal) as total_harga
                FROM transaksi_detail td
                JOIN transaksi t ON td.transaksi_id = t.id
                WHERE t.akun = ?
                GROUP BY td.plu, td.deskripsi
                ORDER BY td.deskripsi ASC
            ");
            $stmt->execute([$akun]);
            $data = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($_POST['action'] === 'clear_transactions') {
        $akun = $_SESSION['akun'] ?? 'Kasir 1';
        try {
            $stmt = $pdo->prepare("DELETE FROM transaksi WHERE akun = ?");
            $stmt->execute([$akun]);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}

$akunAktif = $_SESSION['akun'] ?? 'Kasir 1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>POS Manualan</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --success: #10b981;
            --success-hover: #059669;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            padding-bottom: 90px;
        }

        #toastNotification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            pointer-events: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #toastNotification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        header {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            color: #fff;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .account-selector {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 6px;
            border-radius: 8px;
            backdrop-filter: blur(8px);
        }

        .account-select-dropdown {
            background: transparent;
            border: none;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 4px 6px;
            outline: none;
            cursor: pointer;
        }

        .account-select-dropdown option {
            background: #ffffff;
            color: var(--text-main);
        }

        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 999;
            padding: 16px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: var(--card-bg);
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
        }

        .modal h2 {
            font-size: 1.15rem;
            margin-bottom: 6px;
            color: var(--text-main);
            text-align: center;
            font-weight: 700;
        }

        .modal p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 12px;
            text-align: center;
        }

        .container {
            padding: 16px;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-container {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .search-box {
            flex: 1;
            padding: 12px 16px;
            font-size: 16px;
            border: 2px solid var(--border);
            border-radius: 12px;
            outline: none;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: border-color 0.2s ease;
        }

        .search-box:focus {
            border-color: var(--primary);
        }

        .btn-scan {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0 16px;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #reader {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            gap: 8px;
        }

        .section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .product-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 350px;
            overflow-y: auto;
            margin-top: 10px;
            padding-right: 2px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px;
        }

        .product-card {
            background: var(--card-bg);
            padding: 10px 14px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid var(--border);
            min-height: 70px;
        }

        .product-info {
            flex: 1;
            padding-right: 10px;
        }

        .product-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .product-price {
            font-size: 0.88rem;
            color: var(--primary);
            font-weight: 700;
            margin-top: 2px;
        }

        .product-price .promo {
            color: var(--danger);
            text-decoration: line-through;
            font-size: 0.78rem;
            margin-left: 6px;
            font-weight: normal;
        }

        .btn-add {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .cart-list {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 8px 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 16px;
            border: 1px solid var(--border);
            max-height: 470px;
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding-right: 8px;
        }

        .cart-item-plu {
            font-size: 0.70rem;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.03em;
        }

        .cart-item-title {
            font-size: 0.80rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .cart-item-unit-price {
            font-size: 0.70rem;
            font-weight: 700;
            color: var(--primary);
        }

        .cart-item-unit-price .promo {
            color: var(--danger);
            text-decoration: line-through;
            font-size: 0.70rem;
            margin-left: 4px;
            font-weight: normal;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0 8px;
        }

        .btn-qty {
            background: #f1f5f9;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.95rem;
            color: var(--text-main);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-item-price {
            font-size: 0.80rem;
            font-weight: 700;
            min-width: 75px;
            text-align: right;
            padding-right: 8px;
        }

        .btn-delete-item {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }

        .btn-delete-item:hover {
            background: #fef2f2;
        }

        .action-links {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-link {
            background: none;
            border: none;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-link-primary { color: var(--primary); }
        .btn-link-danger { color: var(--danger); }

        .footer-summary {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: var(--card-bg);
            padding: 14px 20px;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 90;
            border-top: 1px solid var(--border);
        }

        .total-text {
            font-size: 0.72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .total-amount {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
        }

        .btn-pay {
            background-color: var(--success);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-pay:disabled {
            background-color: #cbd5e1;
            cursor: not-allowed;
        }

        .empty-cart {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 16px 0;
        }

        .table-container {
            overflow-y: auto;
            margin-top: 10px;
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            text-align: left;
        }

        .summary-table th {
            background-color: #f8fafc;
            padding: 10px;
            border-bottom: 2px solid var(--border);
            color: var(--text-muted);
            font-weight: 700;
        }

        .summary-table td {
            padding: 10px;
            border-bottom: 1px solid var(--border);
        }

        .summary-table tfoot tr td {
            background-color: #f8fafc;
            font-weight: 700;
            border-top: 2px solid var(--border);
            padding: 12px 10px;
            color: var(--text-main);
        }

        .modal-footer-btns {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }

        .btn-copy-modal {
            background-color: var(--primary);
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-close-modal {
            background-color: #e2e8f0;
            color: var(--text-main);
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
        }

        .pay-info-box {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .pay-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .pay-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .pay-val {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
        }

        .pay-val-change {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--success);
        }

        .input-pay {
            width: 100%;
            padding: 12px 14px;
            font-size: 16px;
            font-weight: 700;
            border: 2px solid var(--border);
            border-radius: 10px;
            outline: none;
            margin-top: 4px;
        }

        .btn-confirm-pay {
            background-color: var(--success);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-confirm-pay:disabled {
            background-color: #cbd5e1;
            cursor: not-allowed;
        }

        .svg-icon {
            width: 18px;
            height: 18px;
            fill: currentColor;
            display: inline-block;
            vertical-align: middle;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

    <div id="toastNotification">
        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        <span id="toastMessage">Berhasil ditambahkan</span>
    </div>

    <!-- Modal Scan Barcode -->
    <div class="modal-overlay" id="scannerModal">
        <div class="modal">
            <h2>Scan Barcode</h2>
            <p>Arahkan kamera ke kode barcode atau QR</p>
            <div id="reader"></div>
            <div class="modal-footer-btns">
                <button class="btn-close-modal" type="button" onclick="closeScannerModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran -->
    <div class="modal-overlay" id="paymentModal">
        <div class="modal">
            <h2>Pembayaran Tunai</h2>
            <p>Masukkan nominal uang tunai</p>
            
            <div class="pay-info-box">
                <div class="pay-row">
                    <span class="pay-label">Total Belanja</span>
                    <span class="pay-val" id="payTotalText">Rp 0</span>
                </div>
                <div style="margin: 12px 0;">
                    <span class="pay-label">Uang Tunai (Rp)</span>
                    <input type="text" id="cashInput" class="input-pay" placeholder="Rp 0" enterkeyhint="go" oninput="formatAndCalculateCash(this)">
                </div>
                <div class="pay-row">
                    <span class="pay-label">Kembalian</span>
                    <span class="pay-val-change" id="changeText">Rp 0</span>
                </div>
            </div>

            <div class="modal-footer-btns">
                <button class="btn-close-modal" type="button" onclick="closePaymentModal()">Batal</button>
                <button class="btn-confirm-pay" type="button" id="confirmPayBtn" onclick="processCheckout()" disabled>
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    Simpan Transaksi
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Daftar Produk -->
    <div class="modal-overlay" id="productListModal">
        <div class="modal" style="max-width: 600px;">
            <h2>Daftar Produk</h2>
            <p>Pilih produk yang tersedia</p>
            
            <input type="text" id="modalSearchInput" class="search-box" placeholder="Cari PLU, Barcode, atau nama produk..." enterkeyhint="search" style="margin-bottom: 8px;" oninput="filterModalProducts()">
            
            <div class="product-list" id="modalProductList" onscroll="handleModalListScroll(this)">
                <div class="spinner"></div>
                <div class="loading-text">Memuat data produk...</div>
            </div>
            <div class="modal-footer-btns">
                <button class="btn-close-modal" onclick="closeProductListModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Daftar Transaksi Akun -->
    <div class="modal-overlay" id="summaryModal">
        <div class="modal">
            <h2>Daftar Transaksi</h2>
            <p>Rekapitulasi seluruh item yang telah ditransaksikan</p>
            <div class="table-container">
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th style="text-align: center; width: 40px;"><input type="checkbox" id="selectAllCheck" onclick="toggleSelectAll(this)"></th>
                            <th>PLU</th>
                            <th>Deskripsi</th>
                            <th style="text-align: center;">QTY</th>
                            <th style="text-align: right;">Harga Total</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: center;" id="grandTotalCell">Jumlah Harga Total: Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer-btns">
                <button class="btn-copy-modal" onclick="copySummaryData()">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>
                    Salin
                </button>
                <button class="btn-close-modal" onclick="closeSummaryModal()">Tutup</button>
            </div>
        </div>
    </div>

    <header>
        <h1>POS Kasir Manualan</h1>
        <div class="account-selector">
            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 2a5 5 0 105 5 5 5 0 00-5-5zm0 8a3 3 0 113-3 3 3 0 01-3 3zm0 4c-4.42 0-8 2.24-8 5v1a1 1 0 001 1h14a1 1 0 001-1v-1c0-2.76-3.58-5-8-5zm-6 5c.22-1.38 2.82-3 6-3s5.78 1.62 6 3z"/></svg>
            <select class="account-select-dropdown" onchange="changeAccountDirectly(this.value)">
                <option value="Kasir 1" <?= $akunAktif === 'Kasir 1' ? 'selected' : '' ?>>Kasir 1</option>
                <option value="Kasir 2" <?= $akunAktif === 'Kasir 2' ? 'selected' : '' ?>>Kasir 2</option>
            </select>
        </div>
    </header>

    <div class="container">
        <div class="search-container">
            <input type="text" id="searchInput" class="search-box" placeholder="Ketik PLU atau Barcode..." enterkeyhint="go" oninput="validateSearchInput(this)" onkeydown="handleSearchKey(event)">
            <button class="btn-scan" type="button" onclick="openScannerModal()" title="Scan Barcode">
                <svg class="svg-icon" viewBox="0 0 24 24"><path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </button>
        </div>

        <div class="section-header">
            <div class="section-title">Keranjang Belanja</div>
            <div class="action-links">
                <button class="btn-link btn-link-primary" onclick="showProductListModal()">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/></svg>
                    Daftar Produk
                </button>
                <button class="btn-link btn-link-primary" onclick="showSummaryModal()">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                    Lihat Data Transaksi
                </button>
                <button class="btn-link btn-link-danger" onclick="clearAccountTransactions()">
                    <svg class="svg-icon" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    Hapus Data Transaksi
                </button>
            </div>
        </div>

        <div class="cart-list" id="cartList">
            <div class="empty-cart">Keranjang masih kosong</div>
        </div>
    </div>

    <div class="footer-summary">
        <div>
            <div class="total-text">Total Bayar</div>
            <div class="total-amount" id="totalAmount">Rp 0</div>
        </div>
        <button class="btn-pay" id="payBtn" onclick="openPaymentModal()" disabled>
            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5-1.5z"/></svg>
            Bayar
        </button>
    </div>

    <script>
        let cart = [];
        let currentTotal = 0;
        let rawCashValue = 0;
        let html5QrCode = null;
        let productsData = [];
        let filteredProductsData = [];
        let productsLoaded = false;
        let toastTimeout = null;
        let filterDebounce = null;
        
        let currentPage = 1;
        const pageSize = 20;

        window.addEventListener('DOMContentLoaded', () => {
            loadProducts();
        });

        function showToast(message) {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            
            toastMessage.innerText = message;
            toast.classList.add('show');

            if (toastTimeout) clearTimeout(toastTimeout);

            toastTimeout = setTimeout(() => {
                toast.classList.remove('show');
            }, 2000);
        }

        function loadProducts() {
            if (productsLoaded) return;

            const formData = new FormData();
            formData.append('action', 'get_products');

            fetch('pos_manual.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    productsData = data.data;
                    filteredProductsData = data.data;
                    productsLoaded = true;
                } else {
                    console.error('Gagal memuat produk:', data.message);
                }
            })
            .catch(err => {
                console.error('Error fetching products:', err);
            });
        }

        function validateSearchInput(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        function formatAndCalculateCash(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            rawCashValue = parseFloat(value) || 0;

            if (value === '' || rawCashValue === 0) {
                input.value = '';
            } else {
                input.value = 'Rp ' + rawCashValue.toLocaleString('id-ID');
            }

            calculateChange();
        }

        function changeAccountDirectly(namaAkun) {
            const formData = new FormData();
            formData.append('action', 'set_account');
            formData.append('akun', namaAkun);

            fetch('pos_manual.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status !== 'success') {
                    alert('Gagal memilih akun: ' + (data.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(err => {
                console.error(err);
            });
        }

        function openScannerModal() {
            document.getElementById('scannerModal').classList.add('active');
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }
            const config = { fps: 10, qrbox: { width: 250, height: 150 } };
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                alert("Gagal mengakses kamera: " + err);
                closeScannerModal();
            });
        }

        function closeScannerModal() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    document.getElementById('scannerModal').classList.remove('active');
                }).catch(err => {
                    console.error(err);
                    document.getElementById('scannerModal').classList.remove('active');
                });
            } else {
                document.getElementById('scannerModal').classList.remove('active');
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            const cleanText = decodedText.replace(/[^0-9]/g, '');
            document.getElementById('searchInput').value = cleanText;
            checkAndAddProductByInput(cleanText);
            closeScannerModal();
        }

        function checkAndAddProductByInput(query) {
            query = query.trim().toLowerCase();
            if (query === '') return;

            const matchedProduct = productsData.find(product => {
                const plu = String(product.plu).toLowerCase();
                const barcodes = String(product.barcode).toLowerCase().split(',');
                return plu === query || barcodes.map(b => b.trim()).includes(query);
            });

            if (matchedProduct) {
                addToCart(matchedProduct);
                document.getElementById('searchInput').value = '';
            } else {
                alert('Produk dengan PLU / Barcode tersebut tidak ditemukan.');
            }
        }

        function handleSearchKey(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const query = document.getElementById('searchInput').value;
                checkAndAddProductByInput(query);
            }
        }

        function showProductListModal() {
            const modalList = document.getElementById('modalProductList');
            document.getElementById('modalSearchInput').value = '';
            document.getElementById('productListModal').classList.add('active');

            if (productsLoaded) {
                filteredProductsData = productsData;
                currentPage = 1;
                renderModalProductList(filteredProductsData, true);
            } else {
                modalList.innerHTML = `
                    <div class="spinner"></div>
                    <div class="loading-text">Memuat data produk...</div>
                `;

                const formData = new FormData();
                formData.append('action', 'get_products');

                fetch('pos_manual.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        productsData = data.data;
                        filteredProductsData = data.data;
                        productsLoaded = true;
                        currentPage = 1;
                        renderModalProductList(filteredProductsData, true);
                    } else {
                        modalList.innerHTML = '<div class="empty-cart">Gagal memuat data produk.</div>';
                    }
                })
                .catch(err => {
                    modalList.innerHTML = '<div class="empty-cart">Terjadi kesalahan koneksi.</div>';
                });
            }
        }

        function filterModalProducts() {
            if (filterDebounce) clearTimeout(filterDebounce);

            filterDebounce = setTimeout(() => {
                const query = document.getElementById('modalSearchInput').value.trim().toLowerCase();
                if (query === '') {
                    filteredProductsData = productsData;
                } else {
                    filteredProductsData = productsData.filter(item => {
                        const plu = String(item.plu).toLowerCase();
                        const desc = String(item.deskripsi).toLowerCase();
                        const barcode = String(item.barcode).toLowerCase();
                        return plu.includes(query) || desc.includes(query) || barcode.includes(query);
                    });
                }
                currentPage = 1;
                renderModalProductList(filteredProductsData, true);
            }, 150);
        }

        function renderModalProductList(products, reset = false) {
            const modalList = document.getElementById('modalProductList');
            
            if (!products || products.length === 0) {
                modalList.innerHTML = '<div class="empty-cart">Data produk tidak ditemukan / kosong.</div>';
                return;
            }

            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const pagedItems = products.slice(start, end);

            let html = '';
            pagedItems.forEach(item => {
                const promoHtml = item.harga_promo && item.harga_promo > 0 
                    ? `<span class="promo">Rp ${item.harga_normal.toLocaleString('id-ID')}</span>` 
                    : '';

                const productJson = JSON.stringify({
                    plu: item.plu,
                    deskripsi: item.deskripsi,
                    harga: item.harga,
                    harga_normal: item.harga_normal,
                    harga_promo: item.harga_promo
                }).replace(/'/g, "&apos;");

                html += `
                    <div class="product-card">
                        <div class="product-info">
                            <div class="product-title">${item.deskripsi}</div>
                            <div class="product-price">
                                Rp ${item.harga.toLocaleString('id-ID')} ${promoHtml}
                            </div>
                        </div>
                        <button class="btn-add" type="button" onclick='addToCartModal(${productJson})'>
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            
                        </button>
                    </div>
                `;
            });

            if (reset) {
                modalList.innerHTML = html;
                modalList.scrollTop = 0;
            } else {
                modalList.insertAdjacentHTML('beforeend', html);
            }
        }

        function handleModalListScroll(container) {
            if (container.scrollTop + container.clientHeight >= container.scrollHeight - 50) {
                if (currentPage * pageSize < filteredProductsData.length) {
                    currentPage++;
                    renderModalProductList(filteredProductsData, false);
                }
            }
        }

        function closeProductListModal() {
            document.getElementById('productListModal').classList.remove('active');
        }

        function addToCartModal(product) {
            addToCart(product);
            showToast('Berhasil menambahkan ' + product.deskripsi + ' ke keranjang!');
        }

        function addToCart(product) {
            if (!product || !product.plu) {
                alert('Data produk rusak.');
                return;
            }

            const pluStr = String(product.plu);
            const hargaNum = parseFloat(product.harga) || 0;
            const existingIndex = cart.findIndex(item => String(item.plu) === pluStr);

            if (existingIndex !== -1) {
                const existingItem = cart.splice(existingIndex, 1)[0];
                existingItem.qty += 1;
                cart.unshift(existingItem);
            } else {
                cart.unshift({
                    plu: pluStr,
                    deskripsi: product.deskripsi || 'Tanpa Nama',
                    harga: hargaNum,
                    harga_normal: product.harga_normal || hargaNum,
                    harga_promo: product.harga_promo || null,
                    qty: 1
                });
            }

            renderCart();
            
            const cartList = document.getElementById('cartList');
            if (cartList) {
                cartList.scrollTop = 0;
            }
        }

        function updateQty(plu, change) {
            const pluStr = String(plu);
            const item = cart.find(i => String(i.plu) === pluStr);
            if (!item) return;

            item.qty += change;

            if (item.qty <= 0) {
                cart = cart.filter(i => String(i.plu) !== pluStr);
            }

            renderCart();
        }

        function removeFromCart(plu) {
            const pluStr = String(plu);
            cart = cart.filter(i => String(i.plu) !== pluStr);
            renderCart();
        }

        function renderCart() {
            const cartList = document.getElementById('cartList');
            const totalAmount = document.getElementById('totalAmount');
            const payBtn = document.getElementById('payBtn');

            if (cart.length === 0) {
                cartList.innerHTML = '<div class="empty-cart">Keranjang masih kosong</div>';
                totalAmount.innerText = 'Rp 0';
                payBtn.disabled = true;
                currentTotal = 0;
                return;
            }

            let html = '';
            let total = 0;

            cart.forEach(item => {
                const subtotal = item.harga * item.qty;
                total += subtotal;

                const promoPriceHtml = item.harga_promo && item.harga_promo > 0
                    ? `Rp ${item.harga.toLocaleString('id-ID')} <span class="promo">Rp ${item.harga_normal.toLocaleString('id-ID')}</span>`
                    : `Rp ${item.harga.toLocaleString('id-ID')}`;

                html += `
                    <div class="cart-item">
                        <div class="cart-item-details">
                            <span class="cart-item-plu">${item.plu}</span>
                            <span class="cart-item-title">${item.deskripsi}</span>
                            <span class="cart-item-unit-price">${promoPriceHtml}</span>
                        </div>
                        <div class="qty-controls">
                            <button class="btn-qty" type="button" onclick="updateQty('${item.plu}', -1)">-</button>
                            <span>${item.qty}</span>
                            <button class="btn-qty" type="button" onclick="updateQty('${item.plu}', 1)">+</button>
                        </div>
                        <div class="cart-item-price">Rp ${subtotal.toLocaleString('id-ID')}</div>
                        <button class="btn-delete-item" type="button" onclick="removeFromCart('${item.plu}')" title="Hapus Item">
                            <svg class="svg-icon" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                    </div>
                `;
            });

            cartList.innerHTML = html;
            totalAmount.innerText = 'Rp ' + total.toLocaleString('id-ID');
            payBtn.disabled = false;
            currentTotal = total;
        }

        function openPaymentModal() {
            if (cart.length === 0) return;
            document.getElementById('payTotalText').innerText = 'Rp ' + currentTotal.toLocaleString('id-ID');
            document.getElementById('cashInput').value = '';
            rawCashValue = 0;
            document.getElementById('changeText').innerText = 'Rp 0';
            document.getElementById('confirmPayBtn').disabled = true;
            document.getElementById('paymentModal').classList.add('active');
            setTimeout(() => document.getElementById('cashInput').focus(), 150);
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
        }

        function calculateChange() {
            const cash = rawCashValue;
            const change = cash - currentTotal;
            const changeText = document.getElementById('changeText');
            const confirmBtn = document.getElementById('confirmPayBtn');

            if (change >= 0) {
                changeText.innerText = 'Rp ' + change.toLocaleString('id-ID');
                changeText.style.color = 'var(--success)';
                confirmBtn.disabled = false;
            } else {
                changeText.innerText = 'Uang Kurang (Rp ' + Math.abs(change).toLocaleString('id-ID') + ')';
                changeText.style.color = 'var(--danger)';
                confirmBtn.disabled = true;
            }
        }

        function processCheckout() {
            if (cart.length === 0) return;

            const formData = new FormData();
            formData.append('action', 'save_transaction');
            formData.append('cart', JSON.stringify(cart));
            formData.append('total', currentTotal);

            fetch('pos_manual.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Transaksi berhasil disimpan ke database!');
                    cart = [];
                    renderCart();
                    closePaymentModal();
                    document.getElementById('searchInput').value = '';
                } else {
                    alert('Gagal menyimpan transaksi: ' + data.message);
                }
            })
            .catch(err => alert('Terjadi kesalahan koneksi ke server.'));
        }

        function showSummaryModal() {
            const formData = new FormData();
            formData.append('action', 'get_account_summary');

            fetch('pos_manual.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const tbody = document.getElementById('summaryTableBody');
                    const grandTotalCell = document.getElementById('grandTotalCell');
                    document.getElementById('selectAllCheck').checked = false;
                    
                    if (data.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:#888;">Belum ada riwayat transaksi.</td></tr>';
                        grandTotalCell.innerText = 'Jumlah Harga Total: Rp 0';
                    } else {
                        let html = '';
                        let grandTotal = 0;

                        data.data.forEach(row => {
                            const subtotal = parseInt(row.total_harga);
                            grandTotal += subtotal;

                            html += `
                                <tr>
                                    <td style="text-align: center;"><input type="checkbox" class="row-check" data-plu="${row.plu}" data-qty="${row.total_qty}"></td>
                                    <td>${row.plu}</td>
                                    <td>${row.deskripsi}</td>
                                    <td style="text-align: center;">${row.total_qty}</td>
                                    <td style="text-align: right;">${subtotal.toLocaleString('id-ID')}</td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                        grandTotalCell.innerText = 'Jumlah Harga Total: Rp ' + grandTotal.toLocaleString('id-ID');
                    }
                    document.getElementById('summaryModal').classList.add('active');
                } else {
                    alert('Gagal memuat rekap transaksi: ' + data.message);
                }
            });
        }

        function toggleSelectAll(master) {
            const checkboxes = document.querySelectorAll('.row-check');
            checkboxes.forEach(cb => cb.checked = master.checked);
        }

        function copySummaryData() {
            const selectedCheckboxes = document.querySelectorAll('.row-check:checked');
            let targetCheckboxes = selectedCheckboxes;

            if (targetCheckboxes.length === 0) {
                targetCheckboxes = document.querySelectorAll('.row-check');
            }

            if (targetCheckboxes.length === 0) {
                alert('Tidak ada data untuk disalin.');
                return;
            }

            let textToCopy = '';
            targetCheckboxes.forEach(cb => {
                const plu = cb.getAttribute('data-plu');
                const qty = cb.getAttribute('data-qty');
                textToCopy += `${plu}:${qty}\n`;
            });

            navigator.clipboard.writeText(textToCopy.trim()).then(() => {
                alert('Daftar PLU:QTY berhasil disalin!');
            }).catch(err => {
                alert('Gagal menyalin teks secara otomatis. Silakan salin manual.');
            });
        }

        function closeSummaryModal() {
            document.getElementById('summaryModal').classList.remove('active');
        }

        function clearAccountTransactions() {
            if (confirm('Apakah kamu yakin ingin menghapus SELURUH riwayat data transaksi untuk akun ini dari database?')) {
                const formData = new FormData();
                formData.append('action', 'clear_transactions');

                fetch('pos_manual.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Seluruh data transaksi untuk akun ini berhasil dihapus.');
                    } else {
                        alert('Gagal menghapus data transaksi: ' + data.message);
                    }
                });
            }
        }
    </script>
</body>
</html>