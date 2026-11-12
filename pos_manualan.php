<?php
/**
 * POS Kasir Sederhana - Mobile Optimized (Fixed Receipt Items Display)
 */

session_start();

const JSON_FILE = __DIR__ . '/data_produk.json';

// Konfigurasi Database MySQL
$host   = 'db.fr-roub1.bengt.wasmernet.com';
$port   = '20184';
$dbname = 'stock_opname';
$dbuser = 'user_a2e7c23a';
$dbpass = 'pw_XVc32h58LGUKszLr1XCGg8R8FVDzTAcy';

function getDbConnection() {
    global $host, $port, $dbname, $dbuser, $dbpass;
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function rp($n): string { return number_format((float)$n, 0, ',', '.'); }

// Handle Login Akun (4 Pilihan)
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $_SESSION['cashier_account'] = trim($_POST['account_name'] ?? 'Induk Shift 1');
    header('Location: ?');
    exit;
}
if (isset($_GET['logout'])) {
    unset($_SESSION['cashier_account']);
    $_SESSION['cart'] = [];
    unset($_SESSION['receipt_data']);
    header('Location: ?');
    exit;
}

// Inisialisasi Keranjang Belanja di Session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['cart_action'] ?? '';
$pluTarget = $_GET['plu'] ?? '';
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

// Handle Aksi Keranjang via AJAX
if ($isAjax && $action) {
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
    } elseif ($action === 'save_transaction') {
        $pdo = getDbConnection();
        if ($pdo && !empty($_SESSION['cart'])) {
            $currentAccount = $_SESSION['cashier_account'] ?? 'Induk Shift 1';
            
            // Simpan data keranjang ke session struk sebelum dikosongkan
            $_SESSION['receipt_data'] = $_SESSION['cart'];
            
            foreach ($_SESSION['cart'] as $item) {
                $plu = $item['plu'];
                $deskripsi = $item['deskripsi'];
                $qtyBaru = $item['qty'];
                $totalHargaBaru = $item['price'] * $item['qty'];

                $stmt = $pdo->prepare("SELECT id, qty, total_harga FROM transaksi_kasir WHERE akun_kasir = ? AND plu = ?");
                $stmt->execute([$currentAccount, $plu]);
                $existing = $stmt->fetch();

                if ($existing) {
                    $newQty = $existing['qty'] + $qtyBaru;
                    $newTotal = $existing['total_harga'] + $totalHargaBaru;
                    
                    $updateStmt = $pdo->prepare("UPDATE transaksi_kasir SET qty = ?, total_harga = ? WHERE id = ?");
                    $updateStmt->execute([$newQty, $newTotal, $existing['id']]);
                } else {
                    $insertStmt = $pdo->prepare("INSERT INTO transaksi_kasir (akun_kasir, plu, deskripsi, qty, total_harga) VALUES (?, ?, ?, ?, ?)");
                    $insertStmt->execute([$currentAccount, $plu, $deskripsi, $qtyBaru, $totalHargaBaru]);
                }
            }
            $_SESSION['cart'] = [];
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit;
    } elseif ($action === 'clear_saved_history') {
        $pdo = getDbConnection();
        if ($pdo) {
            $currentAccount = $_SESSION['cashier_account'] ?? 'Induk Shift 1';
            $stmt = $pdo->prepare("DELETE FROM transaksi_kasir WHERE akun_kasir = ?");
            $stmt->execute([$currentAccount]);
        }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit;
    } elseif (is_file(JSON_FILE)) {
        $allData = json_decode(file_get_contents(JSON_FILE), true);
        $foundProduct = null;
        if (is_array($allData)) {
            foreach ($allData as $row) {
                if (($row['plu'] ?? '') === $pluTarget) {
                    $foundProduct = $row;
                    break;
                }
            }
        }

        if ($foundProduct) {
            $hNorm = $foundProduct['harga_normal'] ?? 0;
            $hPromo = $foundProduct['harga_promo'] ?? 0;
            $price = ($hPromo > 0 && $hPromo != $hNorm) ? $hPromo : $hNorm;

            if ($action === 'add') {
                if (isset($_SESSION['cart'][$pluTarget])) {
                    $_SESSION['cart'][$pluTarget]['qty']++;
                } else {
                    $_SESSION['cart'][$pluTarget] = [
                        'plu' => $foundProduct['plu'],
                        'deskripsi' => $foundProduct['deskripsi'],
                        'price' => $price,
                        'qty' => 1
                    ];
                }
            } elseif ($action === 'minus') {
                if (isset($_SESSION['cart'][$pluTarget])) {
                    $_SESSION['cart'][$pluTarget]['qty']--;
                    if ($_SESSION['cart'][$pluTarget]['qty'] <= 0) {
                        unset($_SESSION['cart'][$pluTarget]);
                    }
                }
            } elseif ($action === 'remove') {
                unset($_SESSION['cart'][$pluTarget]);
            }
        }
    }
    
    $totalCartItems = 0;
    $grandTotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $totalCartItems += $item['qty'];
        $grandTotal += ($item['price'] * $item['qty']);
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'total_items' => $totalCartItems,
        'grand_total' => rp($grandTotal),
        'grand_total_num' => $grandTotal,
        'cart' => $_SESSION['cart']
    ]);
    exit;
}

// Endpoint Ambil Daftar Item Tersimpan di DB untuk Akun Terkait
if (isset($_GET['api_get_saved'])) {
    $pdo = getDbConnection();
    $currentAccount = $_SESSION['cashier_account'] ?? 'Induk Shift 1';
    $savedItems = [];
    $totalSemua = 0;

    if ($pdo) {
        $stmt = $pdo->prepare("SELECT plu, deskripsi, qty, total_harga FROM transaksi_kasir WHERE akun_kasir = ? ORDER BY id DESC");
        $stmt->execute([$currentAccount]);
        $savedItems = $stmt->fetchAll();
        foreach ($savedItems as $si) {
            $totalSemua += $si['total_harga'];
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'items' => $savedItems,
        'grand_total_saved' => rp($totalSemua),
        'account' => $currentAccount
    ]);
    exit;
}

// Endpoint Pencarian Berkelanjutan (Infinite Scroll)
if (isset($_GET['api_search'])) {
    $q = trim($_GET['q'] ?? '');
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 15;
    
    $filtered = [];
    if (is_file(JSON_FILE)) {
        $data = json_decode(file_get_contents(JSON_FILE), true);
        if (is_array($data)) {
            $kata = preg_split('/\s+/', mb_strtolower($q));
            foreach ($data as $row) {
                $hay = mb_strtolower(($row['plu'] ?? '') . ' ' . ($row['barcode'] ?? '') . ' ' . ($row['deskripsi'] ?? ''));
                $ok = true;
                foreach ($kata as $k) {
                    if ($k !== '' && mb_strpos($hay, $k) === false) { $ok = false; break; }
                }
                if ($ok) {
                    $hNorm = $row['harga_normal'] ?? 0;
                    $hPromo = $row['harga_promo'] ?? 0;
                    $isPromoDifferent = ($hPromo > 0 && $hPromo != $hNorm);
                    $filtered[] = [
                        'plu' => $row['plu'] ?? '',
                        'deskripsi' => $row['deskripsi'] ?? '',
                        'harga_jual' => rp($isPromoDifferent ? $hPromo : $hNorm),
                        'harga_norm' => $isPromoDifferent ? rp($hNorm) : null
                    ];
                }
            }
        }
    }

    $totalItems = count($filtered);
    $offset = ($page - 1) * $limit;
    $pagedData = array_slice($filtered, $offset, $limit);
    $hasMore = ($offset + $limit) < $totalItems;

    header('Content-Type: application/json');
    echo json_encode([
        'items' => $pagedData,
        'has_more' => $hasMore,
        'page' => $page
    ]);
    exit;
}

// Hitung Total Keranjang Awal
$totalCartItems = 0;
$grandTotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalCartItems += $item['qty'];
    $grandTotal += ($item['price'] * $item['qty']);
}

$isCheckoutPage = isset($_GET['checkout']) && $_GET['checkout'] == '1';
$isSuccessPage = isset($_GET['success']) && $_GET['success'] == '1';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>POS Kasir Sederhana</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap" rel="stylesheet">
<!-- Library HTML5 QR Code Scanner -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
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
    --danger: #d9534f;
    --danger-bg: #fdf2f2;
    --success: #38a169;
    --success-bg: #f0fff4;
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
        --danger: #e5534b;
        --danger-bg: #3d2726;
        --success: #48bb78;
        --success-bg: #223829;
    }
}

* { box-sizing: border-box; margin: 0; padding: 0; }
body { 
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: var(--bg-color); 
    color: var(--text-main); 
    font-size: 14px; 
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px;
    -webkit-font-smoothing: antialiased;
    transition: opacity 0.25s ease, transform 0.25s ease;
}

body.leaving {
    opacity: 0;
    transform: translateY(-8px);
}

@keyframes rise {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: none; }
}

header, .pos-container, footer {
    animation: rise 0.55s cubic-bezier(0.2, 0.7, 0.2, 1) backwards;
}

/* Halaman Login */
.login-wrap { display: flex; align-items: center; justify-content: center; min-height: 80vh; width: 100%; }
.login-card { background: var(--card-bg); padding: 28px 24px 24px; border-radius: 20px; border: 1px solid var(--border); box-shadow: var(--shadow); width: 100%; max-width: 380px; text-align: center; }
.login-card h2 { font-family: 'Source Serif 4', Georgia, serif; margin-top: 0; margin-bottom: 16px; color: var(--text-main); font-size: 20px; font-weight: 500; }
.login-card select { width: 100%; padding: 11px 14px; border: 1px solid var(--border-strong); background: var(--item-bg); color: var(--text-main); border-radius: 10px; margin-bottom: 16px; font-size: 14px; font-family: inherit; }
.login-card select:focus { outline: none; border-color: var(--accent); }

/* Layout POS Utama */
.pos-container { display: flex; flex-direction: column; height: calc(100vh - 32px); max-height: 800px; width: 100%; max-width: 460px; background: var(--card-bg); border: 1px solid var(--border); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; }

/* Header */
.pos-header { background: var(--card-bg); color: var(--text-main); padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; border-bottom: 1px solid var(--border); }
.pos-header h3 { font-family: 'Source Serif 4', Georgia, serif; margin: 0; font-size: 18px; font-weight: 500; }
.logout-btn { color: var(--text-sub); background: var(--item-bg); border: 1px solid var(--border); padding: 8px; border-radius: 10px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease; }
.logout-btn:hover { background: var(--item-hover); color: var(--accent); border-color: var(--border-strong); }

/* Keranjang Belanja */
.pos-cart { background: var(--card-bg); display: flex; flex-direction: column; flex: 1; overflow: hidden; }
.cart-header { padding: 14px 20px; font-weight: 500; font-family: 'Source Serif 4', Georgia, serif; font-size: 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: var(--card-bg); }
.cart-actions-top { display: flex; align-items: center; gap: 8px; }
.icon-btn { background: var(--item-bg); border: 1px solid var(--border); border-radius: 10px; padding: 7px 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-family: inherit; font-weight: 500; color: var(--text-main); text-decoration: none; transition: all 0.2s ease; }
.icon-btn:hover { background: var(--item-hover); border-color: var(--border-strong); }
.icon-btn.danger { color: var(--danger); border-color: var(--border); background: var(--danger-bg); }
.icon-btn.danger:hover { opacity: 0.85; }

.cart-items { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 10px; background: var(--bg-color); }
.cart-item { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 12px 14px; display: flex; align-items: center; gap: 10px; box-shadow: var(--shadow); }
.cart-remove-btn { color: var(--danger); background: var(--danger-bg); border: 1px solid var(--border); border-radius: 50%; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; font-weight: bold; flex-shrink: 0; transition: transform 0.15s ease; }
.cart-remove-btn:hover { transform: scale(1.05); }
.cart-item-info { flex: 1; min-width: 0; }
.cart-item-plu { font-size: 11px; color: var(--text-sub); font-family: monospace; font-weight: 600; }
.cart-item-title { font-size: 13px; font-weight: 500; color: var(--text-main); margin: 2px 0 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cart-item-sub { font-size: 12px; color: var(--text-sub); }
.cart-controls { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.cart-controls button { width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--border); background: var(--item-bg); color: var(--text-main); cursor: pointer; font-weight: bold; transition: background 0.2s; }
.cart-controls button:hover { background: var(--item-hover); border-color: var(--border-strong); }

/* Ringkasan & Pembayaran */
.cart-footer { padding: 16px 20px; background: var(--card-bg); border-top: 1px solid var(--border); flex-shrink: 0; }
.summary-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; color: var(--text-sub); }
.summary-row.total { font-size: 16px; font-weight: 600; color: var(--text-main); margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border); }
.btn-checkout { width: 100%; background: var(--accent); color: #fff; border: none; padding: 13px 20px; border-radius: 12px; font-size: 14px; font-weight: 500; font-family: inherit; cursor: pointer; margin-top: 10px; text-align: center; text-decoration: none; display: block; transition: background 0.2s ease, transform 0.15s ease; box-shadow: var(--shadow); }
.btn-checkout:hover { background: var(--accent-hover); }
.btn-checkout:active { transform: scale(0.99); }

/* Pop-up Melayang Pencarian (Scroll Load) */
.popup-overlay { position: fixed; inset: 0; background: rgba(41, 38, 27, 0.45); display: flex; flex-direction: column; justify-content: flex-end; z-index: 200; display: none; padding: 0; }
@media(min-width: 768px) { .popup-overlay { align-items: center; justify-content: center; padding: 20px; } }
.popup-content { background: var(--card-bg); width: 100%; max-width: 480px; height: 85vh; border-top-left-radius: 20px; border-top-right-radius: 20px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--border); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); }
@media(min-width: 768px) { .popup-content { border-radius: 20px; height: 80vh; } }

.popup-header-bar { padding: 14px 16px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; background: var(--card-bg); }
.search-input-wrap { flex: 1; display: flex; align-items: center; border: 1px solid var(--border-strong); border-radius: 10px; padding: 0 12px; background: var(--item-bg); }
.search-input-wrap input { flex: 1; border: none; outline: none; padding: 10px 0; font-size: 14px; background: transparent; color: var(--text-main); font-family: inherit; }
.scan-btn { background: none; border: none; cursor: pointer; color: var(--text-sub); padding: 4px; display: flex; align-items: center; transition: color 0.2s; }
.scan-btn:hover { color: var(--accent); }

#scanner-container { width: 100%; max-height: 220px; display: none; background: #000; }
.popup-body-list { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 10px; background: var(--bg-color); }
.prod-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow); }
.prod-title { font-weight: 500; font-size: 13px; color: var(--text-main); margin: 2px 0; }
.prod-meta { font-size: 11px; color: var(--text-sub); font-family: monospace; font-weight: 600; }
.prod-price { font-weight: 600; color: var(--success); margin-top: 4px; font-size: 13px; }
.btn-add-item { background: var(--accent); color: #fff; border: none; border-radius: 8px; padding: 8px 12px; cursor: pointer; font-size: 12px; font-weight: 500; font-family: inherit; transition: background 0.2s; }
.btn-add-item:hover { background: var(--accent-hover); }
.loading-more { text-align: center; padding: 12px; color: var(--text-sub); font-size: 12px; }

/* Halaman Checkout & Success */
.checkout-page { padding: 20px; background: var(--card-bg); flex: 1; overflow-y: auto; display: flex; flex-direction: column; }
.cash-input { width: 100%; padding: 12px 16px; font-size: 22px; font-weight: 600; text-align: right; border: 1px solid var(--border-strong); background: var(--item-bg); color: var(--text-main); border-radius: 12px; margin: 10px 0 14px; font-family: inherit; }
.cash-input:focus { outline: none; border-color: var(--accent); }
.quick-cash { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 16px; }
.quick-cash button { padding: 10px; font-size: 13px; background: var(--item-bg); color: var(--text-main); border: 1px solid var(--border); border-radius: 10px; cursor: pointer; font-weight: 500; font-family: inherit; transition: all 0.2s; }
.quick-cash button:hover { background: var(--item-hover); border-color: var(--border-strong); }
.change-box { background: var(--success-bg); border: 1px solid var(--border); padding: 16px; border-radius: 12px; margin-bottom: 16px; text-align: center; color: var(--success); }
.change-box.short { background: var(--danger-bg); border-color: var(--border); color: var(--danger); }

/* Modal Saved List */
.modal-overlay { position: fixed; inset: 0; background: rgba(41, 38, 27, 0.45); display: flex; align-items: center; justify-content: center; z-index: 300; padding: 16px; display: none; }
.modal-box { background: var(--card-bg); border: 1px solid var(--border); padding: 24px; border-radius: 20px; width: 100%; max-width: 380px; max-height: 85vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15); }
.modal-box h3 { font-family: 'Source Serif 4', Georgia, serif; font-size: 18px; margin-bottom: 8px; font-weight: 500; color: var(--text-main); }
.receipt-box { background: var(--item-bg); border: 1px dashed var(--border-strong); color: var(--text-main); padding: 14px; text-align: left; font-family: monospace; font-size: 12px; margin: 12px 0; max-height: 180px; overflow-y: auto; border-radius: 10px; }
.empty-cart { text-align: center; color: var(--text-sub); padding: 40px 10px; font-size: 13px; }

footer { text-align: center; font-size: 12px; color: var(--text-sub); margin-top: 16px; letter-spacing: 0.5px; width: 100%; flex-shrink: 0; }
</style>
</head>
<body>

<?php if (!isset($_SESSION['cashier_account'])): ?>
<!-- FORM AKUN (4 PILIHAN) -->
<div class="login-wrap">
  <div class="login-card">
    <h2>Pilih Akun Kasir</h2>
    <form method="post">
      <input type="hidden" name="action" value="login">
      <select name="account_name" required>
        <option value="Induk Shift 1">Induk Shift 1</option>
        <option value="Anak Shift 1">Anak Shift 1</option>
        <option value="Induk Shift 2">Induk Shift 2</option>
        <option value="Anak Shift 2">Anak Shift 2</option>
      </select>
      <button type="submit" class="btn-checkout" style="margin-top:0;">Masuk POS</button>
    </form>
  </div>
</div>
<footer></footer>

<?php elseif ($isCheckoutPage): ?>
<!-- HALAMAN PEMBAYARAN KHUSUS (ZERO LAG) -->
<div class="pos-container">
  <div class="pos-header">
    <div>
      <h3>Pembayaran Tunai</h3>
      <span style="font-size: 11px; color: var(--text-sub);">Kasir: <?= e($_SESSION['cashier_account']) ?></span>
    </div>
    <a href="?" class="logout-btn" title="Kembali ke Kasir">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
    </a>
  </div>

  <div class="checkout-page">
    <div class="summary-row" style="font-size:14px;"><span>Total Tagihan:</span><b style="font-size:16px; color:var(--accent);">Rp <?= rp($grandTotal) ?></b></div>
    
    <label style="font-size:13px; color:var(--text-sub); margin-top:8px; display:block;">Jumlah Uang Tunai (Rp):</label>
    <input type="number" id="cashInput" class="cash-input" placeholder="0" autofocus oninput="calculateChange()">
    
    <div class="quick-cash">
      <button type="button" onclick="setQuickCash('pas')">Uang Pas</button>
      <button type="button" onclick="setQuickCash(10000)">10.000</button>
      <button type="button" onclick="setQuickCash(20000)">20.000</button>
      <button type="button" onclick="setQuickCash(50000)">50.000</button>
      <button type="button" onclick="setQuickCash(100000)">100.000</button>
      <button type="button" onclick="setQuickCash(200000)">200.000</button>
    </div>

    <div id="changeBox" class="change-box">
      <div style="font-size:12px;">Kembalian</div>
      <b id="changeAmount" style="font-size:18px;">Rp 0</b>
    </div>

    <div style="margin-top:auto;">
      <button type="button" id="btn-finish-pay" class="btn-checkout" disabled onclick="completeTransaction()">Proses & Selesai Transaksi</button>
      <a href="?" style="text-align:center; display:block; color:var(--text-sub); text-decoration:none; padding:10px; margin-top:6px; font-size:13px;">Batal / Kembali</a>
    </div>
  </div>
</div>
<footer></footer>

<script>
let currentGrandTotal = <?= $grandTotal ?>;

function setQuickCash(val) {
    let cashInput = document.getElementById('cashInput');
    if(val === 'pas') {
        cashInput.value = currentGrandTotal;
    } else {
        cashInput.value = val;
    }
    calculateChange();
}

function calculateChange() {
    let cash = parseFloat(document.getElementById('cashInput').value) || 0;
    let change = cash - currentGrandTotal;
    let changeBox = document.getElementById('changeBox');
    let changeAmount = document.getElementById('changeAmount');
    let btnPay = document.getElementById('btn-finish-pay');

    if (change >= 0) {
        changeBox.className = 'change-box';
        changeAmount.innerText = 'Rp ' + formatRupiah(change);
        btnPay.disabled = false;
        btnPay.style.opacity = '1';
    } else {
        changeBox.className = 'change-box short';
        changeAmount.innerText = 'Kurang: Rp ' + formatRupiah(Math.abs(change));
        btnPay.disabled = true;
        btnPay.style.opacity = '0.5';
    }
}

function completeTransaction() {
    let cash = parseFloat(document.getElementById('cashInput').value) || 0;
    let change = cash - currentGrandTotal;

    fetch('?cart_action=save_transaction&ajax=1', { method: 'GET' })
        .then(res => res.json())
        .then(resData => {
            if(resData.status === 'success') {
                window.location.href = `?success=1&cash=${cash}&change=${change}`;
            }
        });
}

function formatRupiah(num) { return new Intl.NumberFormat('id-ID').format(num); }
</script>

<?php elseif ($isSuccessPage): ?>
<!-- HALAMAN STRUK TRANSAKSI SELESAI -->
<?php 
    $cashPaid = $_GET['cash'] ?? 0;
    $changeBack = $_GET['change'] ?? 0;
    $receiptItems = $_SESSION['receipt_data'] ?? [];
    $totalReceiptAmount = 0;
    foreach($receiptItems as $ritem) {
        $totalReceiptAmount += ($ritem['price'] * $ritem['qty']);
    }
?>
<div class="pos-container">
  <div class="pos-header">
    <h3>Transaksi Selesai</h3>
  </div>
  <div class="checkout-page" style="text-align:center; justify-content:center;">
    <h3 style="color:var(--success); margin-top:0; font-family:'Source Serif 4', Georgia, serif; font-size:18px;">Transaksi Berhasil!</h3>
    <div class="receipt-box" style="text-align:left; font-size:12px; max-height:240px;">
      <b>POS Kasir Manualan</b><br>
      Kasir: <?= e($_SESSION['cashier_account']) ?><br>
      Tgl: <?= date('d/m/Y H:i') ?><br>
      --------------------------------<br>
      <?php if (!empty($receiptItems)): ?>
        <?php foreach ($receiptItems as $item): ?>
          <?= e($item['deskripsi']) ?><br>
          <?= $item['qty'] ?> x <?= rp($item['price']) ?> = Rp <?= rp($item['qty'] * $item['price']) ?><br>
        <?php endforeach; ?>
      <?php else: ?>
        <i>Tidak ada item terekam.</i><br>
      <?php endif; ?>
      --------------------------------<br>
      TOTAL    : Rp <?= rp($totalReceiptAmount) ?><br>
      TUNAI    : Rp <?= rp($cashPaid) ?><br>
      KEMBALI  : Rp <?= rp($changeBack) ?><br>
      <div style="text-align:center; margin-top:6px;">Terima Kasih!</div>
    </div>
    <a href="?" class="btn-checkout" style="background:var(--accent); margin-top:16px;">Transaksi Baru</a>
  </div>
</div>
<footer></footer>

<?php else: ?>
<!-- POS UTAMA -->
<div class="pos-container">
  <div class="pos-header">
    <div>
      <h3>POS Kasir Manualan</h3>
      <span style="font-size: 11px; color: var(--text-sub);">Akun : <?= e($_SESSION['cashier_account']) ?></span>
    </div>
    <div style="display:flex; gap:6px; align-items:center;">
      <button type="button" class="icon-btn" onclick="openSavedModal()" title="Daftar Tersimpan di Akun Ini">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <span>Tersimpan</span>
      </button>
      <a href="?logout=1" class="logout-btn" title="Logout / Ganti Akun">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
      </a>
    </div>
  </div>

  <!-- Keranjang -->
  <div class="pos-cart">
    <div class="cart-header">
      <span>Keranjang Belanja (<span id="cart-item-count"><?= $totalCartItems ?></span>)</span>
      <div class="cart-actions-top">
        <button type="button" class="icon-btn danger" onclick="clearCart()" title="Kosongkan Keranjang">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        </button>
        <button type="button" class="icon-btn" onclick="openSearchPopup()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
          <span>Cari Produk</span>
        </button>
      </div>
    </div>

    <div class="cart-items" id="cart-items-container">
      <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart">Keranjang kosong. Klik <b>"Cari Produk"</b> untuk menambah barang.</div>
      <?php else: ?>
        <?php foreach ($_SESSION['cart'] as $item): 
          $subTotalItem = $item['price'] * $item['qty'];
        ?>
          <div class="cart-item" id="cart-row-<?= e($item['plu']) ?>">
            <button type="button" class="cart-remove-btn" onclick="updateCart('remove', '<?= e($item['plu']) ?>')" title="Hapus Item">&times;</button>
            <div class="cart-item-info">
              <div class="cart-item-plu"><?= e($item['plu']) ?></div>
              <div class="cart-item-title"><?= e($item['deskripsi']) ?></div>
              <div class="cart-item-sub">
                <b><?= $item['qty'] ?> x Rp <?= rp($item['price']) ?> : Rp <?= rp($subTotalItem) ?></b>
              </div>
            </div>
            <div class="cart-controls">
              <button type="button" onclick="updateCart('minus', '<?= e($item['plu']) ?>')">-</button>
              <span style="font-size: 13px; font-weight: bold; width: 18px; text-align: center; color: var(--text-main);"><?= $item['qty'] ?></span>
              <button type="button" onclick="updateCart('add', '<?= e($item['plu']) ?>')">+</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="cart-footer">
      <div class="summary-row"><span>Subtotal</span><span id="summary-subtotal">Rp <?= rp($grandTotal) ?></span></div>
      <div class="summary-row total"><span>Total Bayar</span><span id="summary-grandtotal" style="color:var(--accent);">Rp <?= rp($grandTotal) ?></span></div>
      <a href="?checkout=1" id="btn-checkout" class="btn-checkout" <?= empty($_SESSION['cart']) ? 'style="opacity:0.5; pointer-events:none;"' : '' ?>>Bayar Sekarang</a>
    </div>
  </div>
</div>
<footer></footer>

<!-- POP-UP PENCARIAN -->
<div class="popup-overlay" id="searchPopup">
  <div class="popup-content">
    <div class="popup-header-bar">
      <div class="search-input-wrap">
        <input type="search" id="searchInput" placeholder="Ketik PLU, Barcode, Nama Produk..." autocomplete="off" onkeydown="handleSearchKeydown(event)">
        <button type="button" class="scan-btn" onclick="toggleScanner()" title="Scan Barcode">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </button>
      </div>
      <button type="button" style="background:none; border:none; font-size:22px; cursor:pointer; color:var(--text-main);" onclick="closeSearchPopup()">&times;</button>
    </div>

    <div id="scanner-container"><div id="reader" style="width:100%;"></div></div>

    <div class="popup-body-list" id="popupProductList" onscroll="handlePopupScroll()">
      <div class="empty-cart">Ketik teks di atas lalu tekan Enter...</div>
    </div>
  </div>
</div>

<!-- MODAL DAFTAR ITEM TERSIMPAN DI DATABASE & TOMBOL HAPUS -->
<div id="savedModal" class="modal-overlay">
  <div class="modal-box">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
      <h3>Item Tersimpan</h3>
      <button type="button" style="background:none; border:none; font-size:20px; cursor:pointer; color:var(--text-main);" onclick="closeSavedModal()">&times;</button>
    </div>
    <div style="font-size:12px; color:var(--text-sub); margin-bottom:8px;">Akun : <b id="saved-account-name">-</b></div>
    <div class="receipt-box" id="saved-items-content" style="max-height:220px;">
      Memuat data...
    </div>
    <div style="display:flex; gap:8px; margin-top:12px;">
      <button type="button" class="btn-checkout" style="background:var(--danger); margin-top:0; font-size:13px; padding:10px;" onclick="clearSavedHistory()">Hapus Item Tersimpan</button>
      <button type="button" class="btn-checkout" style="background:var(--accent); margin-top:0; font-size:13px; padding:10px;" onclick="closeSavedModal()">Tutup</button>
    </div>
  </div>
</div>

<script>
let currentPage = 1;
let isLoading = false;
let hasMoreItems = true;
let currentQuery = '';
let html5QrCode = null;
let isScannerRunning = false;

function openSearchPopup() {
    document.getElementById('searchPopup').style.display = 'flex';
    let input = document.getElementById('searchInput');
    input.value = '';
    input.focus();
    if(document.getElementById('popupProductList').children.length <= 1) {
        performSearch();
    }
}

function closeSearchPopup() {
    document.getElementById('searchPopup').style.display = 'none';
    stopScanner();
}

function handleSearchKeydown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        performSearch();
    }
}

function performSearch() {
    let inputElem = document.getElementById('searchInput');
    currentQuery = inputElem.value.trim();
    inputElem.value = ''; 
    currentPage = 1;
    hasMoreItems = true;
    document.getElementById('popupProductList').innerHTML = '';
    loadMoreProducts();
}

function handlePopupScroll() {
    let container = document.getElementById('popupProductList');
    if (container.scrollTop + container.clientHeight >= container.scrollHeight - 30) {
        if (!isLoading && hasMoreItems) {
            currentPage++;
            loadMoreProducts();
        }
    }
}

function loadMoreProducts() {
    isLoading = true;
    let container = document.getElementById('popupProductList');
    
    let loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'loading-more';
    loadingIndicator.innerText = 'Memuat produk...';
    container.appendChild(loadingIndicator);

    fetch(`?api_search=1&q=${encodeURIComponent(currentQuery)}&page=${currentPage}`)
        .then(res => res.json())
        .then(data => {
            if (loadingIndicator.parentNode) {
                container.removeChild(loadingIndicator);
            }
            hasMoreItems = data.has_more;
            
            if (data.items.length === 0 && currentPage === 1) {
                container.innerHTML = '<div class="empty-cart">Produk tidak ditemukan.</div>';
                isLoading = false;
                return;
            }

            data.items.forEach(item => {
                let div = document.createElement('div');
                div.className = 'prod-card';
                div.innerHTML = `
                    <div class="prod-info">
                      <div class="prod-meta">${escapeHtml(item.plu)}</div>
                      <div class="prod-title">${escapeHtml(item.deskripsi)}</div>
                      <div class="prod-price">
                        Rp ${item.harga_jual}
                        ${item.harga_norm ? `<span style="text-decoration: line-through; font-size: 11px; color: var(--text-sub); margin-left: 4px;">Rp ${item.harga_norm}</span>` : ''}
                      </div>
                    </div>
                    <button type="button" class="btn-add-item" onclick="addToCart('${escapeHtml(item.plu)}')">+ Tambah</button>
                `;
                container.appendChild(div);
            });
            isLoading = false;
        });
}

function addToCart(plu) {
    updateCart('add', plu);
    closeSearchPopup();
}

function updateCart(action, plu) {
    fetch(`?cart_action=${action}&plu=${encodeURIComponent(plu)}&ajax=1`, { method: 'GET' })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                updateCartUI(data);
            }
        });
}

function clearCart() {
    if(confirm('Kosongkan keranjang belanja?')) {
        fetch('?cart_action=clear&ajax=1', { method: 'GET' })
            .then(res => res.json())
            .then(data => {
                updateCartUI(data);
            });
    }
}

function updateCartUI(data) {
    document.getElementById('cart-item-count').innerText = data.total_items;
    document.getElementById('summary-subtotal').innerText = 'Rp ' + data.grand_total;
    document.getElementById('summary-grandtotal').innerText = 'Rp ' + data.grand_total;

    let container = document.getElementById('cart-items-container');
    let checkoutBtn = document.getElementById('btn-checkout');

    if (Object.keys(data.cart).length === 0) {
        container.innerHTML = '<div class="empty-cart">Keranjang kosong. Klik <b>"Cari Item"</b> untuk menambah barang.</div>';
        checkoutBtn.style.opacity = '0.5';
        checkoutBtn.style.pointerEvents = 'none';
    } else {
        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.pointerEvents = 'auto';

        let html = '';
        for (let plu in data.cart) {
            let item = data.cart[plu];
            let subtotal = item.price * item.qty;
            html += `
              <div class="cart-item" id="cart-row-${item.plu}">
                <button type="button" class="cart-remove-btn" onclick="updateCart('remove', '${item.plu}')">&times;</button>
                <div class="cart-item-info">
                  <div class="cart-item-plu">${escapeHtml(item.plu)}</div>
                  <div class="cart-item-title">${escapeHtml(item.deskripsi)}</div>
                  <div class="cart-item-sub">
                    <b>${item.qty} x Rp ${formatRupiah(item.price)} : Rp ${formatRupiah(subtotal)}</b>
                  </div>
                </div>
                <div class="cart-controls">
                  <button type="button" onclick="updateCart('minus', '${item.plu}')">-</button>
                  <span style="font-size: 13px; font-weight: bold; width: 18px; text-align: center; color: var(--text-main);">${item.qty}</span>
                  <button type="button" onclick="updateCart('add', '${item.plu}')">+</button>
                </div>
              </div>
            `;
        }
        container.innerHTML = html;
    }
}

/* MODAL RIWAYAT / DAFTAR ITEM TERSIMPAN DI AKUN */
function openSavedModal() {
    document.getElementById('savedModal').style.display = 'flex';
    document.getElementById('saved-items-content').innerHTML = 'Memuat data dari database...';

    fetch('?api_get_saved=1')
        .then(res => res.json())
        .then(data => {
            document.getElementById('saved-account-name').innerText = data.account;
            let container = document.getElementById('saved-items-content');
            
            if (data.items.length === 0) {
                container.innerHTML = 'Belum ada item tersimpan untuk akun ini.';
                return;
            }

            let html = '';
            data.items.forEach(row => {
                html += `${row.plu}<br>`;
                html += `${escapeHtml(row.deskripsi)}<br>`;
                html += `${row.qty} item | Rp ${formatRupiah(row.total_harga)}<br>`;
                html += `--------------------------------<br>`;
            });
            html += `<b>Total Keseluruhan : ${data.grand_total_saved}</b>`;
            container.innerHTML = html;
        });
}

function closeSavedModal() {
    document.getElementById('savedModal').style.display = 'none';
}

function clearSavedHistory() {
    if(confirm('Hapus semua riwayat item tersimpan untuk akun ini?')) {
        fetch('?cart_action=clear_saved_history&ajax=1')
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    openSavedModal(); // Refresh modal
                }
            });
    }
}

/* SCANNER BARCODE */
function toggleScanner() {
    let container = document.getElementById('scanner-container');
    if (!isScannerRunning) {
        container.style.display = 'block';
        if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 150 } },
            qrMessage => {
                document.getElementById('searchInput').value = qrMessage;
                performSearch();
                stopScanner();
            },
            err => {}
        );
        isScannerRunning = true;
    } else {
        stopScanner();
    }
}

function stopScanner() {
    if (html5QrCode && isScannerRunning) {
        html5QrCode.stop().then(() => {
            isScannerRunning = false;
            document.getElementById('scanner-container').style.display = 'none';
        });
    }
}

function formatRupiah(num) { return new Intl.NumberFormat('id-ID').format(num); }
function escapeHtml(text) { return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;"); }
</script>
<?php endif; ?>

</body>
</html>