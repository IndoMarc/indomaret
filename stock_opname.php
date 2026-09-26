<?php
// --- HANDLER SERVER SIDE (PHP) ---

// 1. AJAX Handler: Upload Ke Database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_db') {
    header('Content-Type: application/json');

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

        $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
        $account = isset($_POST['account']) ? $_POST['account'] : 'Unknown';

        if (empty($items)) {
            echo json_encode(['success' => false, 'message' => 'Tidak ada data item untuk diupload.']);
            exit;
        }

        $sql = "INSERT INTO hasil_stock_opname 
                (akun, nama_rak, noshelf, kirikanan, plumd, deskripsi, harga, qty_lpp, stok_fisik, selisih, total_harga_selisih) 
                VALUES 
                (:akun, :nama_rak, :noshelf, :kirikanan, :plumd, :deskripsi, :harga, :qty_lpp, :stok_fisik, :selisih, :total_harga_selisih)";

        $stmt = $pdo->prepare($sql);
        $pdo->beginTransaction();

        foreach ($items as $item) {
            $stmt->execute([
                ':akun'                => $account,
                ':nama_rak'            => isset($item['nama_rak']) ? $item['nama_rak'] : '',
                ':noshelf'             => isset($item['noshelf']) ? $item['noshelf'] : '',
                ':kirikanan'           => isset($item['kirikanan']) ? $item['kirikanan'] : '',
                ':plumd'               => isset($item['plumd']) ? $item['plumd'] : '',
                ':deskripsi'           => isset($item['deskripsi']) ? $item['deskripsi'] : '',
                ':harga'               => isset($item['harga']) ? floatval($item['harga']) : 0,
                ':qty_lpp'             => isset($item['qty_lpp']) ? intval($item['qty_lpp']) : 0,
                ':stok_fisik'          => isset($item['stok_fisik']) ? intval($item['stok_fisik']) : 0,
                ':selisih'             => isset($item['selisih']) ? intval($item['selisih']) : 0,
                ':total_harga_selisih' => isset($item['total_harga_selisih']) ? floatval($item['total_harga_selisih']) : 0
            ]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan ke database MySQL!']);

    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Gagal DB: ' . $e->getMessage()]);
    }
    exit;
}

// 2. AJAX Handler: Ambil Seluruh Data Database untuk Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'get_admin_database_data') {
    header('Content-Type: application/json');

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

        $stmt = $pdo->query("SELECT * FROM hasil_stock_opname ORDER BY created_at DESC, id DESC");
        $rows = $stmt->fetchAll();

        echo json_encode(['success' => true, 'data' => $rows]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data DB: ' . $e->getMessage()]);
    }
    exit;
}

// 3. AJAX Handler: Hapus Item dari Database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_item_db') {
    header('Content-Type: application/json');

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

        $nama_rak  = isset($_POST['nama_rak']) ? $_POST['nama_rak'] : '';
        $noshelf   = isset($_POST['noshelf']) ? $_POST['noshelf'] : '';
        $kirikanan = isset($_POST['kirikanan']) ? $_POST['kirikanan'] : '';
        $plumd     = isset($_POST['plumd']) ? $_POST['plumd'] : '';

        if (empty($plumd)) {
            echo json_encode(['success' => false, 'message' => 'PLU tidak boleh kosong.']);
            exit;
        }

        $sql = "DELETE FROM hasil_stock_opname 
                WHERE plumd = :plumd 
                  AND nama_rak = :nama_rak 
                  AND noshelf = :noshelf 
                  AND kirikanan = :kirikanan";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':plumd'     => $plumd,
            ':nama_rak'  => $nama_rak,
            ':noshelf'   => $noshelf,
            ':kirikanan' => $kirikanan
        ]);

        $deletedCount = $stmt->rowCount();

        echo json_encode([
            'success' => true, 
            'message' => "Berhasil menghapus $deletedCount baris data PLU ($plumd) dari database."
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>STOCK OPNAME</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-size: 16px;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            font-family: sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        header, footer {
            height: 45px;
            background-color: #1e293b;
            color: #ffffff;
            display: flex;
            align-items: center;
            padding: 0 16px;
            position: fixed;
            left: 0;
            right: 0;
            z-index: 1001;
        }

        header {
            top: 0;
        }

        footer {
            bottom: 0;
            justify-content: center;
            font-size: 12px;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            outline: none;
            padding: 2px;
            display: flex;
            align-items: center;
        }

        .header-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-align: center;
            white-space: nowrap;
        }

        .wrapper {
            position: fixed;
            top: 45px;
            bottom: 45px;
            left: 0;
            right: 0;
            overflow: hidden;
        }

        .sidebar {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 240px;
            background-color: #334155;
            color: #fff;
            transition: transform 0.3s ease;
            transform: translateX(-100%);
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-menu-container {
            overflow-y: auto;
            flex: 1;
        }

        .sidebar ul {
            list-style: none;
            padding: 16px 0;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s, color 0.2s;
            cursor: pointer;
        }

        .sidebar ul li a i {
            width: 18px;
            height: 18px;
        }

        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: #0f172a;
            color: #fff;
        }

        .sidebar ul li.disabled a {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        .sidebar-footer-action {
            padding: 12px 16px;
            border-top: 1px solid #475569;
            background-color: #1e293b;
        }

        .btn-sidebar-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 10px;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-sidebar-logout:hover {
            background-color: #dc2626;
        }

        .content {
            height: 100%;
            padding: 4px;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            position: relative;
        }

        .card {
            background: #ffffff;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 12px;
            width: 100%;
            margin-bottom: 8px;
        }

        .card-laporan {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 8px;
            margin-bottom: 0;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .view-section {
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .view-section.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        #sectionLaporan.active, #sectionHasilAkhir.active, #sectionAdminDB.active, #sectionUploadSelisihPLU.active {
            display: block;
            height: 100%;
            opacity: 1;
            transform: translateY(0);
        }

        .form-box {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-box label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .form-box input[type="file"], .form-box select, .form-box input[type="text"], .search-input, .textarea-custom {
            font-size: 13px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: #f8fafc;
            width: 100%;
        }

        .textarea-custom {
            resize: vertical;
            min-height: 100px;
            font-family: monospace;
        }

        .account-card-header {
            text-align: center;
            margin-bottom: 16px;
        }

        .account-card-header h2 {
            font-size: 16px;
            color: #1e293b;
            font-weight: 700;
        }

        .account-card-header p {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .account-box {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-account {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 12px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            width: 100%;
            text-align: left;
        }

        .btn-account:hover {
            border-color: #0284c7;
            background-color: #f0f9ff;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-account .account-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background-color: #e0f2fe;
            color: #0284c7;
            flex-shrink: 0;
        }

        .btn-account[data-account="Admin"] .account-icon {
            background-color: #fef3c7;
            color: #d97706;
        }

        .btn-account .account-details {
            display: flex;
            flex-direction: column;
        }

        .btn-account .account-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        .active-account-info {
            font-size: 13px;
            color: #1e293b;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .search-box-container {
            margin-bottom: 8px;
            flex-shrink: 0;
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
        }

        .search-input {
            cursor: text;
            padding-left: 32px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            background: #f8fafc !important;
        }

        .search-icon-inside {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            width: 16px;
            height: 16px;
            pointer-events: none;
        }

        .btn-scan-camera {
            background-color: #0284c7;
            color: white;
            border: none;
            padding: 10px 12px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }

        .btn-scan-camera:hover {
            background-color: #0369a1;
        }

        .action-bar-hasil {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 8px;
            flex-shrink: 0;
        }

        .btn-copy-data, .btn-export-excel {
            background-color: #0284c7;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }

        .btn-copy-data:hover, .btn-export-excel:hover {
            background-color: #0369a1;
        }

        .btn-export-excel {
            background-color: #0d9488;
        }

        .btn-export-excel:hover {
            background-color: #0f766e;
        }

        .btn-upload-db {
            background-color: #16a34a;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }

        .btn-upload-db:hover {
            background-color: #15803d;
        }

        .btn-reset-data {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.2s;
        }

        .btn-reset-data:hover {
            background-color: #dc2626;
        }

        .btn-delete-row {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 10px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: background-color 0.2s;
        }

        .btn-delete-row:hover {
            background-color: #dc2626;
        }

        .download-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 4px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .download-text {
            font-size: 13px;
            color: #1e40af;
            line-height: 1.4;
        }

        .wifi-note {
            font-size: 11px;
            color: #d97706;
            font-weight: 600;
        }

        .btn-download {
            display: inline-block;
            text-align: center;
            background-color: #16a34a;
            color: white;
            padding: 10px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .btn-download:hover {
            background-color: #15803d;
        }

        .btn-submit {
            background-color: #0284c7;
            color: white;
            border: none;
            padding: 10px;
            font-size: 13px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-submit:hover {
            background-color: #0369a1;
        }

        .alert {
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 4px;
            margin-bottom: 12px;
            display: none;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .cache-info {
            font-size: 12px;
            color: #0369a1;
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 8px;
            display: none;
        }

        .table-responsive {
            width: 100%;
            flex: 1;
            overflow-y: auto;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        table {
            width: 100%;
            min-width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 10px;
            table-layout: fixed;
        }

        th, td {
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #f1f5f9;
            padding: 6px 1px;
            font-size: 10px;
            font-weight: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: #000;
            vertical-align: middle;
            line-height: 1.25;
        }

        th:last-child, td:last-child {
            border-right: none;
        }

        #tableBody td:nth-child(1), #tableBody th:nth-child(1) { width: 11%; text-align: left; padding-left: 2px; }
        #tableBody td:nth-child(2), #tableBody th:nth-child(2) { width: 15%; text-align: left; }
        #tableBody td:nth-child(3), #tableBody th:nth-child(3) { width: 56%; text-align: left; }
        #tableBody td:nth-child(4), #tableBody th:nth-child(4) { width: 6%; text-align: center; }
        #tableBody td:nth-child(5), #tableBody th:nth-child(5) { width: 6%; text-align: center; }
        #tableBody td:nth-child(6), #tableBody th:nth-child(6) { width: 6%; text-align: center; }

        #tableHasilAkhirBody td:nth-child(1), #tableHasilAkhirHeader th:nth-child(1) { width: 15%; text-align: left; }
        #tableHasilAkhirBody td:nth-child(2), #tableHasilAkhirHeader th:nth-child(2) { width: 15%; text-align: left; }
        #tableHasilAkhirBody td:nth-child(3), #tableHasilAkhirHeader th:nth-child(3) { width: 25%; text-align: left; }
        #tableHasilAkhirBody td:nth-child(4), #tableHasilAkhirHeader th:nth-child(4) { width: 15%; text-align: right; }
        #tableHasilAkhirBody td:nth-child(5), #tableHasilAkhirHeader th:nth-child(5) { width: 10%; text-align: center; }
        #tableHasilAkhirBody td:nth-child(6), #tableHasilAkhirHeader th:nth-child(6) { width: 20%; text-align: right; }

        #tableUploadSelisihBody td:nth-child(1), #tableUploadSelisihHeader th:nth-child(1) { width: 15%; text-align: left; }
        #tableUploadSelisihBody td:nth-child(2), #tableUploadSelisihHeader th:nth-child(2) { width: 15%; text-align: left; }
        #tableUploadSelisihBody td:nth-child(3), #tableUploadSelisihHeader th:nth-child(3) { width: 25%; text-align: left; }
        #tableUploadSelisihBody td:nth-child(4), #tableUploadSelisihHeader th:nth-child(4) { width: 15%; text-align: right; }
        #tableUploadSelisihBody td:nth-child(5), #tableUploadSelisihHeader th:nth-child(5) { width: 10%; text-align: center; }
        #tableUploadSelisihBody td:nth-child(6), #tableUploadSelisihHeader th:nth-child(6) { width: 20%; text-align: right; }

        .admin-table-container {
            margin-bottom: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }

        .admin-table-header-title {
            background-color: #334155;
            color: #ffffff;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .tableAdminCustom th:nth-child(1), .tableAdminCustom td:nth-child(1) { width: 18%; text-align: left; padding-left: 4px; }
        .tableAdminCustom th:nth-child(2), .tableAdminCustom td:nth-child(2) { width: 15%; text-align: left; }
        .tableAdminCustom th:nth-child(3), .tableAdminCustom td:nth-child(3) { width: 32%; text-align: left; }
        .tableAdminCustom th:nth-child(4), .tableAdminCustom td:nth-child(4) { width: 8%; text-align: center; }
        .tableAdminCustom th:nth-child(5), .tableAdminCustom td:nth-child(5) { width: 15%; text-align: right; padding-right: 4px; }
        .tableAdminCustom th:nth-child(6), .tableAdminCustom td:nth-child(6) { width: 12%; text-align: center; }

        .tableAdminCustom tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 2px solid #cbd5e1;
            padding: 8px 4px;
            font-size: 10px;
            color: #000;
        }

        .grand-total-box {
            background-color: #1e293b;
            color: #ffffff;
            padding: 12px;
            border-radius: 6px;
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 12px;
        }

        .grand-total-box .grand-val {
            font-size: 13px;
        }

        #tableHasilAkhirFoot td, #tableUploadSelisihFoot td {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px solid #cbd5e1;
            padding: 8px 4px;
            font-size: 10px;
            color: #000;
        }

        th {
            background-color: #f1f5f9;
            color: #000;
            font-weight: 700;
            position: sticky;
            top: 0;
            z-index: 10;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0px;
            line-height: 1.2;
            padding: 6px 0px;
            text-align: center;
        }

        th:nth-child(1), th:nth-child(2), th:nth-child(3) {
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        tr:hover {
            background-color: #f0f9ff;
        }

        tr.last-updated-row {
            background-color: #fefce8 !important;
        }

        tr.history-row {
            border-bottom: 2px solid #e2e8f0;
        }

        tr.history-row.last-updated-row {
            background-color: #fefce8 !important;
        }

        .td-history {
            padding: 2px 4px;
            background-color: rgba(248, 250, 252, 0.8);
            text-align: left;
            font-size: 10px;
            color: #000;
        }

        .history-text {
            font-size: 10px;
            color: #000;
            font-weight: normal;
            display: block;
            word-break: break-all;
            min-height: 14px;
        }

        .badge-stok, .badge-stok-fisik {
            font-weight: normal;
            color: #000;
            text-align: center;
            font-size: 10px;
        }

        .badge-selisih {
            font-weight: bold;
            color: #000;
            text-align: center;
            font-size: 10px;
        }

        .badge-stok-fisik-clickable {
            cursor: pointer;
            background-color: transparent;
            padding: 2px !important;
        }

        .badge-stok-fisik-clickable span {
            display: inline-block;
            width: 100%;
            padding: 3px 0;
            border: 1px solid #0284c7;
            border-radius: 4px;
            background-color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            color: #0284c7;
            transition: all 0.2s ease;
        }

        .badge-stok-fisik-clickable:hover span {
            background-color: #e0f2fe;
            border-color: #0369a1;
        }

        .badge-selisih.badge-selisih-negatif, .badge-selisih-negatif {
            color: #dc2626 !important;
        }

        .badge-selisih.badge-selisih-positif, .badge-selisih-positif {
            color: #16a34a !important;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 320px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
        }

        .modal-img-container {
            width: 100%;
            height: 130px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .modal-img-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
        }

        .modal-item-title {
            font-size: 14px;
            font-weight: bold;
            color: #0284c7;
        }

        .modal-item-desc {
            font-size: 12px;
            color: #475569;
            margin-top: 2px;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modal-body label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .modal-input {
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
            width: 100%;
            text-align: center;
            color: #0f172a;
        }

        .modal-action-btns {
            display: flex;
            gap: 8px;
        }

        .btn-action-modal {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: background-color 0.2s;
        }

        .btn-minus {
            background-color: #ef4444;
        }

        .btn-minus:hover {
            background-color: #dc2626;
        }

        .btn-plus {
            background-color: #16a34a;
        }

        .btn-plus:hover {
            background-color: #15803d;
        }

        .btn-close-modal {
            background-color: #94a3b8;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 4px;
            transition: background-color 0.2s;
        }

        .btn-close-modal:hover {
            background-color: #64748b;
        }

        #scannerContainer {
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
        }

        footer p {
            font-size: 12px;
        }
    </style>
</head>
<body>

    <header>
        <button class="toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
            &#9776;
        </button>
        <h1 class="header-title">STOCK OPNAME</h1>
    </header>

    <div class="wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-menu-container">
                <ul>
                    <li><a id="menuAkun" class="active"><i data-lucide="users"></i> <span id="txtMenuAkun">Pilih Akun</span></a></li>
                    
                    <!-- Menu Khusus Admin -->
                    <li id="navAdminOnly" style="display: none;"><a id="menuAdminDB"><i data-lucide="database"></i> Full List Hasil SO</a></li>

                    <!-- Menu Khusus Akun Non-Admin -->
                    <li class="nav-restricted" style="display: none;"><a id="menuUpload"><i data-lucide="upload-cloud"></i> Upload Stok</a></li>
                    <li class="nav-restricted" style="display: none;"><a id="menuModis"><i data-lucide="filter"></i> Pilih Modis</a></li>
                    <li class="nav-restricted nav-filter-required disabled" style="display: none;"><a id="menuLaporan"><i data-lucide="list"></i> List Item SO</a></li>
                    <li class="nav-restricted nav-filter-required disabled" style="display: none;"><a id="menuHasilAkhir"><i data-lucide="check-square"></i> Hasil Akhir SO</a></li>
                    <li class="nav-restricted" style="display: none;"><a id="menuUploadSelisihPLU"><i data-lucide="file-input"></i> Upload Item</a></li>
                </ul>
            </div>

            <div class="sidebar-footer-action" id="sidebarFooterAction" style="display: none;">
                <button type="button" class="btn-sidebar-logout" id="btnGantiAkun">
                    <i data-lucide="log-out" style="width: 14px; height: 14px;"></i> Ganti Akun
                </button>
            </div>
        </aside>

        <main class="content" id="mainContent">
            
            <div id="sectionAkun" class="view-section active">
                <div class="card">
                    <div id="activeAccountDisplay" class="active-account-info" style="display: none;">
                        <span style="display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="user-check" style="width: 18px; height: 18px; color: #0284c7;"></i>
                            Akun Aktif: <span id="lblAccountName" style="color: #0284c7;">-</span>
                        </span>
                    </div>

                    <div id="accountSelectionBox">
                        <div class="account-card-header">
                            <h2>Pilih Akun Stock Opname</h2>
                            <p>Silakan pilih akun untuk melanjutkan SO</p>
                        </div>
                        <div class="account-box">
                            <button type="button" class="btn-account" data-account="Admin">
                                <div class="account-icon">
                                    <i data-lucide="shield-check"></i>
                                </div>
                                <div class="account-details">
                                    <span class="account-title">DATABASE</span>
                                </div>
                            </button>
                            <button type="button" class="btn-account" data-account="CIF">
                                <div class="account-icon">
                                    <i data-lucide="user"></i>
                                </div>
                                <div class="account-details">
                                    <span class="account-title">CIF - IKA</span>
                                </div>
                            </button>
                            <button type="button" class="btn-account" data-account="SSL">
                                <div class="account-icon">
                                    <i data-lucide="user"></i>
                                </div>
                                <div class="account-details">
                                    <span class="account-title">SSL - HARY</span>
                                </div>
                            </button>
                            <button type="button" class="btn-account" data-account="SJL">
                                <div class="account-icon">
                                    <i data-lucide="user"></i>
                                </div>
                                <div class="account-details">
                                    <span class="account-title">SJL - CITRA</span>
                                </div>
                            </button>
                            <button type="button" class="btn-account" data-account="SCB">
                                <div class="account-icon">
                                    <i data-lucide="user"></i>
                                </div>
                                <div class="account-details">
                                    <span class="account-title">SCB - NANDA</span>
                                </div>
                            </button>
                            <button type="button" class="btn-account" data-account="SCG">
                                <div class="account-icon">
                                    <i data-lucide="user"></i>
                                </div>
                                <div class="account-details">
                                    <span class="account-title">SCB - VERY</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Khusus Admin (Full List Database) -->
            <div id="sectionAdminDB" class="view-section">
                <div class="card card-laporan">
                    <div class="action-bar-hasil">
                        <button type="button" id="btnRefreshAdminDB" class="btn-copy-data" style="background-color: #16a34a;">
                            <i data-lucide="refresh-cw" style="width: 14px; height: 14px;"></i> Refresh Data
                        </button>
                        <button type="button" id="btnExportExcel" class="btn-export-excel">
                            <i data-lucide="file-spreadsheet" style="width: 14px; height: 14px;"></i> Ekspor Ke Excel
                        </button>
                    </div>
                    <div class="table-responsive" id="adminTablesContainer">
                        <div style="text-align: left; color: #000; padding: 12px;">Memuat data dari database...</div>
                    </div>
                </div>
            </div>

            <div id="sectionUpload" class="view-section">
                <div class="card">
                    <div id="alertError" class="alert alert-error"></div>

                    <div class="form-box">
                        <div class="download-box">
                            <span class="download-text">Sebelum Upload Data, silakan download data stok terlebih dahulu melalui tombol berikut:</span>
                            <a href="http://192.168.137.1:3000/data_so.html" class="btn-download">Download Data Stok</a>
                            <span class="wifi-note">* Pastikan tersambung ke WiFi komputer kasir sebelum klik tombol download.</span>
                        </div>

                        <div>
                            <label for="json_file">Upload File Data Stok:</label>
                            <input type="file" id="json_file" accept=".json">
                        </div>
                    </div>
                </div>
            </div>

            <div id="sectionModis" class="view-section">
                <div id="alertSuccessUpload" class="alert alert-success">File JSON berhasil diproses. Silakan pilih filter.</div>
                <div id="cacheInfo" class="cache-info">Data tersimpan di cache dimuat. Silakan pilih filter.</div>
                <div class="card">
                    <div class="form-box">
                        <div>
                            <label for="selectModis">Pilih Modis:</label>
                            <select id="selectModis">
                                <option value="">-- Semua Modis --</option>
                            </select>
                        </div>
                        <div>
                            <label for="selectDariShelfing">Dari Shelfing:</label>
                            <select id="selectDariShelfing">
                                <option value="">-- Semua Shelfing --</option>
                            </select>
                        </div>
                        <div>
                            <label for="selectSampaiShelfing">Sampai Shelfing:</label>
                            <select id="selectSampaiShelfing">
                                <option value="">-- Semua Shelfing --</option>
                            </select>
                        </div>
                        <button type="button" id="btnTampilkanFilter" class="btn-submit">Simpan & Tampilkan</button>
                    </div>
                </div>
            </div>

            <div id="sectionLaporan" class="view-section">
                <div class="card card-laporan">
                    <div class="search-box-container">
                        <div class="search-input-wrapper">
                            <i data-lucide="search" class="search-icon-inside"></i>
                            <input type="text" id="searchInput" class="search-input" inputmode="numeric" pattern="[0-9]*" placeholder="Cari Produk...">
                        </div>
                        <button type="button" id="btnStartScan" class="btn-scan-camera" title="Scan Barcode Kamera">
                            <i data-lucide="camera" style="width: 18px; height: 18px;"></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Modis</th>
                                    <th>PLU</th>
                                    <th>Deskripsi</th>
                                    <th>LPPTK</th>
                                    <th>Qty Fisik</th>
                                    <th>Selisih</th>
                                    <th style="display:none;">Harga</th>
                                    <th style="display:none;">Barcode</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="6" style="text-align: left; color: #000; padding: 12px;">Belum ada data. Silakan unggah file JSON melalui menu Upload Data.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="sectionHasilAkhir" class="view-section">
                <div class="card card-laporan">
                    <div class="action-bar-hasil">
                        <button type="button" id="btnUploadDB" class="btn-upload-db">
                            <i data-lucide="database" style="width: 14px; height: 14px;"></i> Upload Data
                        </button>
                        <button type="button" id="btnSalinHasilAkhir" class="btn-copy-data">
                            <i data-lucide="copy" style="width: 14px; height: 14px;"></i> Salin Hasil
                        </button>
                        <button type="button" id="btnResetHasilAkhir" class="btn-reset-data">
                            <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i> Reset Data
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr id="tableHasilAkhirHeader">
                                    <th>Modis</th>
                                    <th>PLU</th>
                                    <th>Deskripsi</th>
                                    <th>Harga</th>
                                    <th>Selisih</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody id="tableHasilAkhirBody">
                                <tr>
                                    <td colspan="6" style="text-align: left; color: #000; padding: 12px;">Belum ada data hasil akhir SO.</td>
                                </tr>
                            </tbody>
                            <tfoot id="tableHasilAkhirFoot" style="display: none;">
                                <tr>
                                    <td colspan="5" style="text-align: right; font-weight: bold; color: #000;">Selisih Total Harga</td>
                                    <td id="cellTotalHargaFooter" style="text-align: right; font-weight: bold;">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Section Upload Selisih PLU -->
            <div id="sectionUploadSelisihPLU" class="view-section">
                <div class="card card-laporan">
                    <div class="form-box" style="margin-bottom: 12px;">
                        <label for="txtSearchSelisihPLU">Input PLU & Selisih:</label>
                        <textarea id="txtSearchSelisihPLU" class="textarea-custom" placeholder="20024079 -1&#10;10036631 3"></textarea>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" id="btnCariSelisihPLU" class="btn-submit" style="flex: 1;">
                                <i data-lucide="search" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> Tampilkan
                            </button>
                            <button type="button" id="btnUploadDBSelisihPLU" class="btn-upload-db" style="display: none;">
                                <i data-lucide="database" style="width: 14px; height: 14px;"></i> Upload Data
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr id="tableUploadSelisihHeader">
                                    <th>Modis</th>
                                    <th>PLU</th>
                                    <th>Deskripsi</th>
                                    <th>Harga</th>
                                    <th>Selisih</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody id="tableUploadSelisihBody">
                                <tr>
                                    <td colspan="6" style="text-align: left; color: #000; padding: 12px;">Masukkan PLU dan Selisih lalu klik tombol Cari.</td>
                                </tr>
                            </tbody>
                            <tfoot id="tableUploadSelisihFoot" style="display: none;">
                                <tr>
                                    <td colspan="5" style="text-align: right; font-weight: bold; color: #000;">Selisih Total Harga</td>
                                    <td id="cellTotalHargaFootUploadSelisih" style="text-align: right; font-weight: bold;">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Modal Input Pop-up -->
    <div id="inputModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-img-container">
                <img id="modalProductImg" src="" alt="Gambar Produk" referrerpolicy="no-referrer" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%2394a3b8\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><polyline points=\'21 15 16 10 5 21\'/></svg>'">
            </div>
            <div class="modal-header">
                <div id="modalPlumd" class="modal-item-title">PLUMD</div>
                <div id="modalDesc" class="modal-item-desc">Deskripsi Item</div>
            </div>
            <div class="modal-body">
                <label for="modalInputValue">Jumlah Input:</label>
                <input type="text" id="modalInputValue" class="modal-input" inputmode="numeric" pattern="[0-9]*" placeholder="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <div class="modal-action-btns">
                    <button type="button" id="btnModalMinus" class="btn-action-modal btn-minus">
                        <i data-lucide="minus" style="width: 16px; height: 16px;"></i> Kurang
                    </button>
                    <button type="button" id="btnModalPlus" class="btn-action-modal btn-plus">
                        <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Tambah
                    </button>
                </div>
                <button type="button" id="btnModalClose" class="btn-close-modal">Close</button>
            </div>
        </div>
    </div>

    <!-- Camera Scan Modal -->
    <div id="scanModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-item-title">Scan Barcode</div>
                <div class="modal-item-desc">Arahkan kamera ke barcode produk</div>
            </div>
            <div class="modal-body">
                <div id="scannerContainer"></div>
                <button type="button" id="btnCloseScanModal" class="btn-close-modal">Tutup Kamera</button>
            </div>
        </div>
    </div>

    <footer>
        <p>~ m.h.r ~</p>
    </footer>

    <script>
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        const menuAkun = document.getElementById('menuAkun');
        const txtMenuAkun = document.getElementById('txtMenuAkun');
        const menuAdminDB = document.getElementById('menuAdminDB');
        const navAdminOnly = document.getElementById('navAdminOnly');

        const menuUpload = document.getElementById('menuUpload');
        const menuModis = document.getElementById('menuModis');
        const menuLaporan = document.getElementById('menuLaporan');
        const menuHasilAkhir = document.getElementById('menuHasilAkhir');
        const menuUploadSelisihPLU = document.getElementById('menuUploadSelisihPLU');

        const sectionAkun = document.getElementById('sectionAkun');
        const sectionAdminDB = document.getElementById('sectionAdminDB');
        const sectionUpload = document.getElementById('sectionUpload');
        const sectionModis = document.getElementById('sectionModis');
        const sectionLaporan = document.getElementById('sectionLaporan');
        const sectionHasilAkhir = document.getElementById('sectionHasilAkhir');
        const sectionUploadSelisihPLU = document.getElementById('sectionUploadSelisihPLU');

        const navRestrictedList = document.querySelectorAll('.nav-restricted');
        const navFilterRequiredList = document.querySelectorAll('.nav-filter-required');
        const accountBtns = document.querySelectorAll('.btn-account');
        const activeAccountDisplay = document.getElementById('activeAccountDisplay');
        const lblAccountName = document.getElementById('lblAccountName');
        const accountSelectionBox = document.getElementById('accountSelectionBox');
        const btnGantiAkun = document.getElementById('btnGantiAkun');
        const sidebarFooterAction = document.getElementById('sidebarFooterAction');

        const adminTablesContainer = document.getElementById('adminTablesContainer');
        const btnRefreshAdminDB = document.getElementById('btnRefreshAdminDB');
        const btnExportExcel = document.getElementById('btnExportExcel');

        const fileInput = document.getElementById('json_file');
        const tableBody = document.getElementById('tableBody');
        const tableHasilAkhirBody = document.getElementById('tableHasilAkhirBody');
        const tableHasilAkhirFoot = document.getElementById('tableHasilAkhirFoot');
        const cellTotalHargaFooter = document.getElementById('cellTotalHargaFooter');
        
        const txtSearchSelisihPLU = document.getElementById('txtSearchSelisihPLU');
        const btnCariSelisihPLU = document.getElementById('btnCariSelisihPLU');
        const btnUploadDBSelisihPLU = document.getElementById('btnUploadDBSelisihPLU');
        const tableUploadSelisihBody = document.getElementById('tableUploadSelisihBody');
        const tableUploadSelisihFoot = document.getElementById('tableUploadSelisihFoot');
        const cellTotalHargaFootUploadSelisih = document.getElementById('cellTotalHargaFootUploadSelisih');

        const alertError = document.getElementById('alertError');
        const alertSuccessUpload = document.getElementById('alertSuccessUpload');
        const cacheInfo = document.getElementById('cacheInfo');

        const selectModis = document.getElementById('selectModis');
        const selectDariShelfing = document.getElementById('selectDariShelfing');
        const selectSampaiShelfing = document.getElementById('selectSampaiShelfing');
        const btnTampilkanFilter = document.getElementById('btnTampilkanFilter');
        const searchInput = document.getElementById('searchInput');
        const btnStartScan = document.getElementById('btnStartScan');
        const btnSalinHasilAkhir = document.getElementById('btnSalinHasilAkhir');
        const btnUploadDB = document.getElementById('btnUploadDB');
        const btnResetHasilAkhir = document.getElementById('btnResetHasilAkhir');

        const inputModal = document.getElementById('inputModal');
        const modalProductImg = document.getElementById('modalProductImg');
        const modalPlumd = document.getElementById('modalPlumd');
        const modalDesc = document.getElementById('modalDesc');
        const modalInputValue = document.getElementById('modalInputValue');
        const btnModalMinus = document.getElementById('btnModalMinus');
        const btnModalPlus = document.getElementById('btnModalPlus');
        const btnModalClose = document.getElementById('btnModalClose');

        const scanModal = document.getElementById('scanModal');
        const btnCloseScanModal = document.getElementById('btnCloseScanModal');

        let globalProcessedItems = [];
        let currentFilteredItems = [];
        let plumdToBarcodesMap = new Map();
        let selectedAccount = null;
        let activeRowIndex = null;
        let activeModalSourceItems = null;
        let lastUpdatedPlumdKey = null;
        let html5QrCode = null;
        let isDropdownPopulated = false;
        let adminExportDataGroups = null;
        let currentSelisihSearchResult = [];

        function updateMenuLockStatus() {
            const hasFilter = loadFilterFromCache();
            navFilterRequiredList.forEach(item => {
                if (hasFilter) {
                    item.classList.remove('disabled');
                } else {
                    item.classList.add('disabled');
                }
            });
            return hasFilter;
        }

        function initDB() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open('StockOpnameDB', 2);
                request.onupgradeneeded = function(e) {
                    const db = e.target.result;
                    if (!db.objectStoreNames.contains('data_store')) {
                        db.createObjectStore('data_store');
                    }
                };
                request.onsuccess = function(e) {
                    resolve(e.target.result);
                };
                request.onerror = function(e) {
                    reject('Gagal membuka IndexedDB');
                };
            });
        }

        async function saveToCache(data) {
            try {
                const db = await initDB();
                const tx = db.transaction('data_store', 'readwrite');
                const store = tx.objectStore('data_store');
                store.put(data, 'cached_json');
            } catch (err) {
                console.error('Gagal menyimpan cache:', err);
            }
        }

        function getInputCacheKey() {
            return selectedAccount ? `cached_input_state_${selectedAccount}` : null;
        }

        async function saveInputStateToCache() {
            const cacheKey = getInputCacheKey();
            if (!cacheKey) return;

            try {
                const db = await initDB();
                const tx = db.transaction('data_store', 'readwrite');
                const store = tx.objectStore('data_store');
                
                const inputState = {
                    itemsInputData: globalProcessedItems.map(item => ({
                        key: getItemUniqueKey(item),
                        stokFisik: item.STOK_FISIK,
                        history: item.INPUT_HISTORY || []
                    })),
                    lastUpdatedKey: lastUpdatedPlumdKey
                };
                
                store.put(inputState, cacheKey);
            } catch (err) {
                console.error('Gagal menyimpan input state ke cache:', err);
            }
        }

        async function loadInputStateFromCache() {
            const cacheKey = getInputCacheKey();
            if (!cacheKey) return false;

            try {
                const db = await initDB();
                const tx = db.transaction('data_store', 'readonly');
                const store = tx.objectStore('data_store');
                const request = store.get(cacheKey);
                
                return new Promise((resolve) => {
                    request.onsuccess = function() {
                        if (request.result) {
                            const state = request.result;
                            lastUpdatedPlumdKey = state.lastUpdatedKey || null;

                            if (Array.isArray(state.itemsInputData)) {
                                const inputMap = new Map();
                                state.itemsInputData.forEach(d => inputMap.set(d.key, d));

                                globalProcessedItems.forEach(item => {
                                    const k = getItemUniqueKey(item);
                                    if (inputMap.has(k)) {
                                        const saved = inputMap.get(k);
                                        item.STOK_FISIK = saved.stokFisik;
                                        item.INPUT_HISTORY = saved.history || [];
                                    } else {
                                        item.STOK_FISIK = undefined;
                                        item.INPUT_HISTORY = [];
                                    }
                                });
                            }
                        } else {
                            lastUpdatedPlumdKey = null;
                            globalProcessedItems.forEach(item => {
                                item.STOK_FISIK = undefined;
                                item.INPUT_HISTORY = [];
                            });
                        }
                        resolve(true);
                    };
                    request.onerror = function() {
                        resolve(false);
                    };
                });
            } catch (err) {
                console.error('Gagal memuat input state dari cache:', err);
                return false;
            }
        }

        async function loadFromCache() {
            try {
                const db = await initDB();
                const tx = db.transaction('data_store', 'readonly');
                const store = tx.objectStore('data_store');
                const request = store.get('cached_json');
                
                request.onsuccess = async function() {
                    if (request.result) {
                        processData(request.result);
                        if (selectedAccount && selectedAccount !== 'Admin') {
                            await loadInputStateFromCache();
                            alertSuccessUpload.style.display = 'none';
                            cacheInfo.style.display = 'block';
                            const hasFilter = updateMenuLockStatus();
                            if (hasFilter) {
                                applyFilterAndRender();
                                switchMenu(menuLaporan, sectionLaporan);
                            } else {
                                switchMenu(menuModis, sectionModis);
                            }
                        }
                    }
                };
            } catch (err) {
                console.error('Gagal memuat cache:', err);
            }
        }

        function getFilterCacheKey() {
            return selectedAccount ? `so_filter_${selectedAccount}` : null;
        }

        function saveFilterToCache() {
            const key = getFilterCacheKey();
            if (!key) return;

            const filterState = {
                modis: selectModis.value,
                dariShelf: selectDariShelfing.value,
                sampaiShelf: selectSampaiShelfing.value
            };

            localStorage.setItem(key, JSON.stringify(filterState));
            updateMenuLockStatus();
        }

        function loadFilterFromCache() {
            const key = getFilterCacheKey();
            if (!key) return false;

            const savedFilter = localStorage.getItem(key);
            if (savedFilter) {
                try {
                    const filterState = JSON.parse(savedFilter);
                    selectModis.value = filterState.modis || "";
                    selectDariShelfing.value = filterState.dariShelf || "";
                    selectSampaiShelfing.value = filterState.sampaiShelf || "";
                    return !!filterState.modis || !!filterState.dariShelf || !!filterState.sampaiShelf;
                } catch (e) {
                    console.error('Gagal membaca cache filter:', e);
                }
            }
            return false;
        }

        function applyFilterAndRender() {
            const selectedModis = selectModis.value;
            const dariShelf = selectDariShelfing.value !== "" ? parseInt(selectDariShelfing.value) : null;
            const sampaiShelf = selectSampaiShelfing.value !== "" ? parseInt(selectSampaiShelfing.value) : null;

            currentFilteredItems = globalProcessedItems.filter(item => {
                if (selectedModis && item.NAMA_RAK_FULL !== selectedModis) {
                    return false;
                }

                const currentShelf = parseInt(item.NOSHELF || 0);

                if (dariShelf !== null && currentShelf < dariShelf) {
                    return false;
                }

                if (sampaiShelf !== null && currentShelf > sampaiShelf) {
                    return false;
                }

                return true;
            });

            searchInput.value = '';
            renderTable(currentFilteredItems);
        }

        function checkAccountCache() {
            const savedAccount = localStorage.getItem('so_user_account');
            if (savedAccount) {
                setAccount(savedAccount);
            } else {
                hideRestrictedMenus();
                switchMenu(menuAkun, sectionAkun);
            }
        }

        async function setAccount(accountName) {
            selectedAccount = accountName;
            localStorage.setItem('so_user_account', accountName);
            
            lblAccountName.innerText = accountName;
            txtMenuAkun.innerText = `Akun: ${accountName}`;
            activeAccountDisplay.style.display = 'flex';
            accountSelectionBox.style.display = 'none';
            sidebarFooterAction.style.display = 'block';

            if (selectedAccount === 'Admin') {
                showAdminMenuOnly();
                loadAdminDatabaseData();
                switchMenu(menuAdminDB, sectionAdminDB);
            } else {
                showRestrictedMenus();

                if (globalProcessedItems.length > 0) {
                    await loadInputStateFromCache();
                    const hasFilter = updateMenuLockStatus();
                    if (hasFilter) {
                        applyFilterAndRender();
                        switchMenu(menuLaporan, sectionLaporan);
                    } else {
                        switchMenu(menuModis, sectionModis);
                    }
                }
            }

            if (window.lucide) {
                lucide.createIcons();
            }
        }

        function showAdminMenuOnly() {
            navAdminOnly.style.display = 'block';
            navRestrictedList.forEach(item => {
                item.style.display = 'none';
            });
        }

        function showRestrictedMenus() {
            navAdminOnly.style.display = 'none';
            navRestrictedList.forEach(item => {
                item.style.display = 'block';
            });
            updateMenuLockStatus();
        }

        function hideRestrictedMenus() {
            navAdminOnly.style.display = 'none';
            navRestrictedList.forEach(item => {
                item.style.display = 'none';
            });
        }

        accountBtns.forEach(btn => {
            btn.addEventListener('click', async function() {
                const acc = this.getAttribute('data-account');
                await setAccount(acc);
                if (selectedAccount === 'Admin') return;

                if (globalProcessedItems.length > 0) {
                    const hasFilter = updateMenuLockStatus();
                    if (hasFilter) {
                        switchMenu(menuLaporan, sectionLaporan);
                    } else {
                        switchMenu(menuModis, sectionModis);
                    }
                } else {
                    switchMenu(menuUpload, sectionUpload);
                }
            });
        });

        btnGantiAkun.addEventListener('click', function() {
            localStorage.removeItem('so_user_account');
            selectedAccount = null;
            txtMenuAkun.innerText = 'Pilih Akun';
            activeAccountDisplay.style.display = 'none';
            accountSelectionBox.style.display = 'block';
            sidebarFooterAction.style.display = 'none';
            hideRestrictedMenus();
            switchMenu(menuAkun, sectionAkun);
        });

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        mainContent.addEventListener('click', function() {
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });

        function switchMenu(activeMenu, activeSection) {
            if (activeMenu.parentElement.classList.contains('disabled')) return;

            menuAkun.classList.remove('active');
            if (menuAdminDB) menuAdminDB.classList.remove('active');
            menuUpload.classList.remove('active');
            menuModis.classList.remove('active');
            menuLaporan.classList.remove('active');
            menuHasilAkhir.classList.remove('active');
            if (menuUploadSelisihPLU) menuUploadSelisihPLU.classList.remove('active');

            sectionAkun.classList.remove('active');
            sectionAdminDB.classList.remove('active');
            sectionUpload.classList.remove('active');
            sectionModis.classList.remove('active');
            sectionLaporan.classList.remove('active');
            sectionHasilAkhir.classList.remove('active');
            sectionUploadSelisihPLU.classList.remove('active');

            activeMenu.classList.add('active');
            
            setTimeout(() => {
                activeSection.classList.add('active');
            }, 50);

            sidebar.classList.remove('open');
        }

        menuAkun.addEventListener('click', function() {
            switchMenu(menuAkun, sectionAkun);
        });

        if (menuAdminDB) {
            menuAdminDB.addEventListener('click', function() {
                loadAdminDatabaseData();
                switchMenu(menuAdminDB, sectionAdminDB);
            });
        }

        menuUpload.addEventListener('click', function() {
            if (!selectedAccount) return;
            switchMenu(menuUpload, sectionUpload);
        });

        menuModis.addEventListener('click', function() {
            if (!selectedAccount) return;
            switchMenu(menuModis, sectionModis);
        });

        menuLaporan.addEventListener('click', function() {
            if (!selectedAccount) return;
            switchMenu(menuLaporan, sectionLaporan);
        });

        menuHasilAkhir.addEventListener('click', function() {
            if (!selectedAccount) return;
            renderTableHasilAkhir();
            switchMenu(menuHasilAkhir, sectionHasilAkhir);
        });

        if (menuUploadSelisihPLU) {
            menuUploadSelisihPLU.addEventListener('click', function() {
                if (!selectedAccount) return;
                switchMenu(menuUploadSelisihPLU, sectionUploadSelisihPLU);
            });
        }

        async function loadAdminDatabaseData() {
            adminTablesContainer.innerHTML = `<div style="text-align: left; color: #000; padding: 12px;">Memuat data dari database...</div>`;

            try {
                const formData = new FormData();
                formData.append('action', 'get_admin_database_data');

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success && Array.isArray(result.data)) {
                    renderAdminTable(result.data);
                } else {
                    adminTablesContainer.innerHTML = `<div style="text-align: left; color: #dc2626; padding: 12px;">Gagal memuat data: ${result.message || 'Terjadi kesalahan'}</div>`;
                }
            } catch (err) {
                console.error(err);
                adminTablesContainer.innerHTML = `<div style="text-align: left; color: #dc2626; padding: 12px;">Terjadi kesalahan jaringan saat mengambil data dari database.</div>`;
            }
        }

        function renderAdminTable(rows) {
            if (rows.length === 0) {
                adminTablesContainer.innerHTML = `<div style="text-align: left; color: #000; padding: 12px;">Belum ada data di database.</div>`;
                adminExportDataGroups = null;
                return;
            }

            const uniqueMap = new Map();
            rows.forEach(item => {
                const rakShort = String(item.nama_rak || '').substring(0, 6);
                const modisFull = `${rakShort}-${item.noshelf || ''}-${item.kirikanan || ''}`;
                const plu = String(item.plumd || '').trim();

                const key = plu;
                if (!uniqueMap.has(key)) {
                    item.computed_modis = modisFull;
                    uniqueMap.set(key, item);
                }
            });

            const processedRows = Array.from(uniqueMap.values());
            processedRows.sort((a, b) => a.computed_modis.localeCompare(b.computed_modis));

            const groups = new Map();
            processedRows.forEach(item => {
                const groupKey = item.computed_modis.substring(0, 6);
                if (!groups.has(groupKey)) {
                    groups.set(groupKey, []);
                }
                groups.get(groupKey).push(item);
            });

            adminExportDataGroups = groups;

            let containerHtml = '';
            let grandTotalHargaSelisih = 0;

            groups.forEach((items, groupKey) => {
                let tableRowsHtml = '';
                let tableTotalHargaSelisih = 0;

                items.forEach(item => {
                    const selisih = parseInt(item.selisih || 0);
                    const totalSelisih = parseFloat(item.total_harga_selisih || 0);
                    tableTotalHargaSelisih += totalSelisih;

                    let selisihClass = '';
                    let selisihStr = `${selisih}`;
                    let totalSelisihStr = `${totalSelisih.toLocaleString('id-ID')}`;

                    if (selisih > 0) {
                        selisihClass = 'badge-selisih-positif';
                        selisihStr = `+${selisih}`;
                        totalSelisihStr = `+${totalSelisihStr}`;
                    } else if (selisih < 0) {
                        selisihClass = 'badge-selisih-negatif';
                    }

                    const safeRak = escapeHtml(item.nama_rak || '');
                    const safeShelf = escapeHtml(item.noshelf || '');
                    const safeKK = escapeHtml(item.kirikanan || '');
                    const safePlu = escapeHtml(item.plumd || '');

                    tableRowsHtml += `
                        <tr>
                            <td>${escapeHtml(item.computed_modis)}</td>
                            <td>${safePlu}</td>
                            <td>${escapeHtml(item.deskripsi || '-')}</td>
                            <td class="badge-selisih ${selisihClass}">${selisihStr}</td>
                            <td class="badge-selisih ${selisihClass}">${totalSelisihStr}</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-delete-row" onclick="deleteItemFromDB('${safeRak}', '${safeShelf}', '${safeKK}', '${safePlu}')">
                                    <i data-lucide="trash-2" style="width: 12px; height: 12px;"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });

                grandTotalHargaSelisih += tableTotalHargaSelisih;

                let tableTotalClass = '';
                let tableTotalStr = tableTotalHargaSelisih.toLocaleString('id-ID');
                if (tableTotalHargaSelisih > 0) {
                    tableTotalClass = 'badge-selisih-positif';
                    tableTotalStr = `+${tableTotalStr}`;
                } else if (tableTotalHargaSelisih < 0) {
                    tableTotalClass = 'badge-selisih-negatif';
                }

                containerHtml += `
                    <div class="admin-table-container">
                        <div class="admin-table-header-title">Modis: ${escapeHtml(groupKey)}</div>
                        <table class="tableAdminCustom">
                            <thead>
                                <tr>
                                    <th>Modis</th>
                                    <th>PLU</th>
                                    <th>Deskripsi</th>
                                    <th>Selisih</th>
                                    <th style="text-align: right;">Total Harga</th>
                                    <th style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRowsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right; font-weight: bold;">Selisih Total Harga</td>
                                    <td class="${tableTotalClass}">${tableTotalStr}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;
            });

            let grandTotalClass = '';
            let grandTotalStr = grandTotalHargaSelisih.toLocaleString('id-ID');
            if (grandTotalHargaSelisih > 0) {
                grandTotalClass = 'badge-selisih-positif';
                grandTotalStr = `+${grandTotalStr}`;
            } else if (grandTotalHargaSelisih < 0) {
                grandTotalClass = 'badge-selisih-negatif';
            }

            containerHtml += `
                <div class="grand-total-box">
                    <span>Grand Selisih Total Harga</span>
                    <span class="grand-val ${grandTotalClass}">${grandTotalStr}</span>
                </div>
            `;

            adminTablesContainer.innerHTML = containerHtml;
            if (window.lucide) {
                lucide.createIcons();
            }
        }

        async function deleteItemFromDB(namaRak, noshelf, kirikanan, plumd) {
            if (!confirm(`Apakah Anda yakin ingin menghapus item PLU (${plumd}) ini?\n\nCatatan: Semua riwayat data lama terkait PLU ini di rak/modis ini akan dihapus dari database.`)) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'delete_item_db');
                formData.append('nama_rak', namaRak);
                formData.append('noshelf', noshelf);
                formData.append('kirikanan', kirikanan);
                formData.append('plumd', plumd);

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    loadAdminDatabaseData();
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan saat menghapus data.');
            }
        }

        function exportAdminTableToExcel() {
            if (!adminExportDataGroups || adminExportDataGroups.size === 0) {
                alert('Tidak ada data untuk diekspor ke Excel.');
                return;
            }

            if (typeof XLSX === 'undefined') {
                alert('Pustaka SheetJS belum siap.');
                return;
            }

            const excelData = [];
            let grandTotalHarga = 0;

            adminExportDataGroups.forEach((items, groupKey) => {
                excelData.push({ "Modis": `--- MODIS: ${groupKey} ---`, "PLU": "", "Deskripsi": "", "Selisih": "", "Total Harga": "" });

                let subtotalGroup = 0;

                items.forEach(item => {
                    const selisih = parseInt(item.selisih || 0);
                    const totalHarga = parseFloat(item.total_harga_selisih || 0);
                    subtotalGroup += totalHarga;

                    excelData.push({
                        "Modis": item.computed_modis,
                        "PLU": item.plumd || '',
                        "Deskripsi": item.deskripsi || '',
                        "Selisih": selisih,
                        "Total Harga": totalHarga
                    });
                });

                grandTotalHarga += subtotalGroup;

                excelData.push({
                    "Modis": `Subtotal Selisih Total Harga (${groupKey})`,
                    "PLU": "",
                    "Deskripsi": "",
                    "Selisih": "",
                    "Total Harga": subtotalGroup
                });

                excelData.push({ "Modis": "", "PLU": "", "Deskripsi": "", "Selisih": "", "Total Harga": "" });
            });

            excelData.push({
                "Modis": "GRAND SELISIH TOTAL HARGA",
                "PLU": "",
                "Deskripsi": "",
                "Selisih": "",
                "Total Harga": grandTotalHarga
            });

            const worksheet = XLSX.utils.json_to_sheet(excelData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Full List Hasil SO");

            const todayStr = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(workbook, `Full_List_Hasil_SO_${todayStr}.xlsx`);
        }

        if (btnExportExcel) {
            btnExportExcel.addEventListener('click', exportAdminTableToExcel);
        }

        if (btnRefreshAdminDB) {
            btnRefreshAdminDB.addEventListener('click', loadAdminDatabaseData);
        }

        window.addEventListener('DOMContentLoaded', function() {
            checkAccountCache();
            loadFromCache();
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            alertError.style.display = 'none';

            if (!file) return;

            const reader = new FileReader();

            reader.onload = async function(e) {
                try {
                    const data = JSON.parse(e.target.result);

                    if (data && Array.isArray(data.data)) {
                        await saveToCache(data);
                        processData(data);
                        cacheInfo.style.display = 'none';
                        alertSuccessUpload.style.display = 'block';
                        switchMenu(menuModis, sectionModis);
                    } else {
                        alertError.innerText = 'Format data JSON tidak sesuai atau struktur "data" tidak ditemukan.';
                        alertError.style.display = 'block';
                    }
                } catch (err) {
                    alertError.innerText = 'Gagal membaca file JSON: Format file rusak atau bukan JSON.';
                    alertError.style.display = 'block';
                }
            };

            reader.readAsText(file);
        });

        function getItemUniqueKey(item) {
            return `${item.NAMA_RAK_FULL || ''}|${item.NOSHELF || ''}|${item.KIRIKANAN || ''}|${item.PLUMD || ''}`;
        }

        function processData(data) {
            if (!data || !Array.isArray(data.data)) return;

            const rawItems = data.data;
            const uniqueMap = new Map();
            plumdToBarcodesMap.clear();

            for (let i = 0; i < rawItems.length; i++) {
                const item = rawItems[i];
                const rakFull = String(item.NAMA_RAK || '');
                const rakShort = rakFull.substring(0, 6);
                const shelf = String(item.NOSHELF || '');
                const kirikanan = String(item.KIRIKANAN || '');
                const plumd = String(item.PLUMD || '');
                const barcd = String(item.BARCD || '');

                if (plumd && barcd) {
                    if (!plumdToBarcodesMap.has(plumd)) {
                        plumdToBarcodesMap.set(plumd, new Set());
                    }
                    plumdToBarcodesMap.get(plumd).add(barcd);
                }

                const key = `${rakFull}|${shelf}|${kirikanan}|${plumd}`;

                if (!uniqueMap.has(key)) {
                    item.NAMA_RAK_FULL = rakFull;
                    item.NAMA_RAK_SHORT = rakShort;
                    item.STOK_FISIK = undefined;
                    item.INPUT_HISTORY = [];
                    uniqueMap.set(key, item);
                }
            }

            globalProcessedItems = Array.from(uniqueMap.values());

            globalProcessedItems.sort((a, b) => {
                const cmpRak = (a.NAMA_RAK_FULL || '').localeCompare(b.NAMA_RAK_FULL || '');
                if (cmpRak !== 0) return cmpRak;

                const cmpShelf = parseInt(a.NOSHELF || 0) - parseInt(b.NOSHELF || 0);
                if (cmpShelf !== 0) return cmpShelf;

                return parseInt(a.KIRIKANAN || 0) - parseInt(b.KIRIKANAN || 0);
            });

            isDropdownPopulated = false;
            populateDropdowns(globalProcessedItems);
            updateMenuLockStatus();
        }

        function populateDropdowns(items) {
            if (isDropdownPopulated) return;

            const modisSet = new Set();
            const shelfSet = new Set();

            for (let i = 0; i < items.length; i++) {
                if (items[i].NAMA_RAK_FULL) {
                    modisSet.add(items[i].NAMA_RAK_FULL);
                }
                if (items[i].NOSHELF !== undefined && items[i].NOSHELF !== '') {
                    shelfSet.add(parseInt(items[i].NOSHELF || 0));
                }
            }

            const modisList = Array.from(modisSet).sort();
            const shelfList = Array.from(shelfSet).sort((a, b) => a - b);

            let modisOptions = '<option value="">-- Semua Modis --</option>';
            for (let i = 0; i < modisList.length; i++) {
                modisOptions += `<option value="${escapeHtml(modisList[i])}">${escapeHtml(modisList[i])}</option>`;
            }
            selectModis.innerHTML = modisOptions;

            let shelfOptions = '<option value="">-- Semua Shelfing --</option>';
            for (let i = 0; i < shelfList.length; i++) {
                shelfOptions += `<option value="${shelfList[i]}">${shelfList[i]}</option>`;
            }
            selectDariShelfing.innerHTML = shelfOptions;
            selectSampaiShelfing.innerHTML = shelfOptions;

            isDropdownPopulated = true;
        }

        btnTampilkanFilter.addEventListener('click', function() {
            saveFilterToCache();
            applyFilterAndRender();
            switchMenu(menuLaporan, sectionLaporan);
        });

        function performSearch(query) {
            query = query.trim();

            if (!query) {
                renderTable(currentFilteredItems);
                return;
            }

            const searchedItems = currentFilteredItems.filter(item => {
                const plumd = String(item.PLUMD || '');
                if (plumd.includes(query)) return true;

                const barcodes = plumdToBarcodesMap.get(plumd);
                if (barcodes) {
                    for (let barcd of barcodes) {
                        if (barcd.includes(query)) return true;
                    }
                }

                return false;
            });

            renderTable(searchedItems);

            if (searchedItems.length === 1) {
                openInputModal(0, searchedItems);
            }
        }

        searchInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            performSearch(this.value);
        });

        btnStartScan.addEventListener('click', function() {
            if (typeof Html5Qrcode === 'undefined') {
                alert('Pustaka scanner belum siap. Pastikan jaringan internet aktif.');
                return;
            }
            scanModal.classList.add('active');
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("scannerContainer");
            }
            
            const config = { fps: 10, qrbox: { width: 220, height: 180 } };
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    searchInput.value = decodedText.replace(/[^0-9]/g, '');
                    stopScanner();
                    performSearch(searchInput.value);
                },
                (errorMessage) => {}
            ).catch(err => {
                console.error("Gagal memulai kamera:", err);
                alert("Kamera tidak dapat diakses. Pastikan izin kamera telah diberikan.");
            });
        });

        function stopScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    scanModal.classList.remove('active');
                }).catch(err => console.error(err));
            } else {
                scanModal.classList.remove('active');
            }
        }

        btnCloseScanModal.addEventListener('click', stopScanner);

        function renderTable(items) {
            if (items.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" style="text-align: left; color: #000; padding: 12px;">Data tidak ditemukan.</td></tr>`;
                return;
            }

            let rowsHtml = '';
            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                const stokLpp = (parseInt(item.QTY) || 0) + (parseInt(item.NPB) || 0);
                const modis = `${item.NAMA_RAK_SHORT}-${item.NOSHELF || ''}-${item.KIRIKANAN || ''}`;
                
                const itemKey = getItemUniqueKey(item);
                const isHighlight = (itemKey === lastUpdatedPlumdKey);
                const highlightClass = isHighlight ? 'last-updated-row' : '';

                const valFisik = item.STOK_FISIK !== undefined ? item.STOK_FISIK : '';
                
                let selisihStr = '';
                let selisihClass = '';
                if (item.STOK_FISIK !== undefined) {
                    const diff = item.STOK_FISIK - stokLpp;
                    if (diff > 0) {
                        selisihStr = `+${diff}`;
                        selisihClass = 'badge-selisih-positif';
                    } else if (diff < 0) {
                        selisihStr = `${diff}`;
                        selisihClass = 'badge-selisih-negatif';
                    } else {
                        selisihStr = '0';
                    }
                }

                const historyStr = (item.INPUT_HISTORY && item.INPUT_HISTORY.length > 0) ? item.INPUT_HISTORY.join('') : '';

                rowsHtml += `
                    <tr id="rowItem_${i}" class="${highlightClass}">
                        <td>${escapeHtml(modis)}</td>
                        <td>${escapeHtml(item.PLUMD || '')}</td>
                        <td>${escapeHtml(item.DESC2 || '')}</td>
                        <td class="badge-stok">${stokLpp}</td>
                        <td class="badge-stok-fisik badge-stok-fisik-clickable" onclick="openInputModal(${i})"><span id="stokFisikVal_${i}">${valFisik}</span></td>
                        <td class="badge-selisih ${selisihClass}">${selisihStr}</td>
                        <td style="display:none;">${item.PRICE || ''}</td>
                        <td style="display:none;">${escapeHtml(item.BARCD || '')}</td>
                    </tr>
                    <tr class="history-row ${highlightClass}">
                        <td colspan="6" class="td-history">
                            <span class="history-text">History Input: ${escapeHtml(historyStr)}</span>
                        </td>
                    </tr>
                `;
            }

            tableBody.innerHTML = rowsHtml;
        }

        function renderTableHasilAkhir() {
            if (currentFilteredItems.length === 0) {
                tableHasilAkhirBody.innerHTML = `<tr><td colspan="6" style="text-align: left; color: #000; padding: 12px;">Data tidak ditemukan.</td></tr>`;
                tableHasilAkhirFoot.style.display = 'none';
                return;
            }

            let rowsHtml = '';
            let grandTotalHarga = 0;

            for (let i = 0; i < currentFilteredItems.length; i++) {
                const item = currentFilteredItems[i];
                const stokLpp = (parseInt(item.QTY) || 0) + (parseInt(item.NPB) || 0);
                const modis = `${item.NAMA_RAK_SHORT}-${item.NOSHELF || ''}-${item.KIRIKANAN || ''}`;
                
                let selisih = 0;

                if (item.STOK_FISIK === undefined) {
                    selisih = -stokLpp;
                } else {
                    selisih = item.STOK_FISIK - stokLpp;
                }

                if (selisih !== 0) {
                    const harga = parseFloat(item.PRICE || 0);
                    const totalHarga = harga * selisih;
                    grandTotalHarga += totalHarga;

                    let selisihStr = '';
                    let selisihClass = '';
                    let totalHargaStr = '';

                    if (selisih > 0) {
                        selisihStr = `+${selisih}`;
                        selisihClass = 'badge-selisih-positif';
                        totalHargaStr = `+${totalHarga.toLocaleString('id-ID')}`;
                    } else {
                        selisihStr = `${selisih}`;
                        selisihClass = 'badge-selisih-negatif';
                        totalHargaStr = `${totalHarga.toLocaleString('id-ID')}`;
                    }

                    rowsHtml += `
                        <tr>
                            <td>${escapeHtml(modis)}</td>
                            <td>${escapeHtml(item.PLUMD || '')}</td>
                            <td>${escapeHtml(item.DESC2 || '')}</td>
                            <td style="text-align: right;">${harga.toLocaleString('id-ID')}</td>
                            <td class="badge-selisih ${selisihClass}">${selisihStr}</td>
                            <td style="text-align: right;" class="badge-selisih ${selisihClass}">${totalHargaStr}</td>
                        </tr>
                    `;
                }
            }

            if (rowsHtml === '') {
                tableHasilAkhirBody.innerHTML = `<tr><td colspan="6" style="text-align: left; color: #000; padding: 12px;">Tidak ada data selisih (semua item sesuai/0).</td></tr>`;
                tableHasilAkhirFoot.style.display = 'none';
            } else {
                tableHasilAkhirBody.innerHTML = rowsHtml;
                tableHasilAkhirFoot.style.display = 'table-footer-group';

                cellTotalHargaFooter.className = '';
                if (grandTotalHarga > 0) {
                    cellTotalHargaFooter.innerText = `+${grandTotalHarga.toLocaleString('id-ID')}`;
                    cellTotalHargaFooter.classList.add('badge-selisih-positif');
                } else if (grandTotalHarga < 0) {
                    cellTotalHargaFooter.innerText = `${grandTotalHarga.toLocaleString('id-ID')}`;
                    cellTotalHargaFooter.classList.add('badge-selisih-negatif');
                } else {
                    cellTotalHargaFooter.innerText = '0';
                }
            }
        }

        // Logic Pencarian Selisih PLU (Menu Baru)
        function processSearchSelisihPLU() {
            const rawText = txtSearchSelisihPLU.value.trim();
            btnUploadDBSelisihPLU.style.display = 'none';
            currentSelisihSearchResult = [];

            if (!rawText) {
                tableUploadSelisihBody.innerHTML = `<tr><td colspan="6" style="text-align: left; color: #000; padding: 12px;">Silakan isi PLU dan Selisih di kotak pencarian.</td></tr>`;
                tableUploadSelisihFoot.style.display = 'none';
                return;
            }

            if (globalProcessedItems.length === 0) {
                alert('Data stok belum dimuat. Silakan upload file data stok terlebih dahulu.');
                return;
            }

            const lines = rawText.split('\n');
            const pluInputMap = new Map();

            lines.forEach(line => {
                const trimmed = line.trim();
                if (!trimmed) return;
                
                const parts = trimmed.split(/\s+/);
                if (parts.length >= 2) {
                    const plu = parts[0].trim();
                    const selisihVal = parseInt(parts[1]) || 0;
                    if (plu) {
                        if (pluInputMap.has(plu)) {
                            pluInputMap.set(plu, pluInputMap.get(plu) + selisihVal);
                        } else {
                            pluInputMap.set(plu, selisihVal);
                        }
                    }
                }
            });

            if (pluInputMap.size === 0) {
                tableUploadSelisihBody.innerHTML = `<tr><td colspan="6" style="text-align: left; color: #000; padding: 12px;">Format input tidak valid. Contoh: 20024079 -1</td></tr>`;
                tableUploadSelisihFoot.style.display = 'none';
                return;
            }

            const resultArray = [];
            let grandTotalHarga = 0;

            pluInputMap.forEach((userSelisih, plu) => {
                const matchedItems = globalProcessedItems.filter(item => String(item.PLUMD || '').trim() === plu);

                if (matchedItems.length > 0) {
                    const firstItem = matchedItems[0];
                    const modis = `${firstItem.NAMA_RAK_SHORT}-${firstItem.NOSHELF || ''}-${firstItem.KIRIKANAN || ''}`;
                    const harga = parseFloat(firstItem.PRICE || 0);
                    const totalHarga = harga * userSelisih;

                    resultArray.push({
                        nama_rak: firstItem.NAMA_RAK_FULL || '',
                        noshelf: firstItem.NOSHELF || '',
                        kirikanan: firstItem.KIRIKANAN || '',
                        plumd: plu,
                        deskripsi: firstItem.DESC2 || '',
                        harga: harga,
                        qty_lpp: (parseInt(firstItem.QTY) || 0) + (parseInt(firstItem.NPB) || 0),
                        stok_fisik: ((parseInt(firstItem.QTY) || 0) + (parseInt(firstItem.NPB) || 0)) + userSelisih,
                        selisih: userSelisih,
                        total_harga_selisih: totalHarga,
                        modisDisplay: modis
                    });

                    grandTotalHarga += totalHarga;
                } else {
                    resultArray.push({
                        nama_rak: '',
                        noshelf: '',
                        kirikanan: '',
                        plumd: plu,
                        deskripsi: 'PLU Tidak Ditemukan di Data Stok',
                        harga: 0,
                        qty_lpp: 0,
                        stok_fisik: userSelisih,
                        selisih: userSelisih,
                        total_harga_selisih: 0,
                        modisDisplay: '-'
                    });
                }
            });

            currentSelisihSearchResult = resultArray;

            let rowsHtml = '';
            resultArray.forEach(item => {
                let selisihStr = item.selisih > 0 ? `+${item.selisih}` : `${item.selisih}`;
                let selisihClass = item.selisih > 0 ? 'badge-selisih-positif' : (item.selisih < 0 ? 'badge-selisih-negatif' : '');
                let totalHargaStr = item.total_harga_selisih > 0 ? `+${item.total_harga_selisih.toLocaleString('id-ID')}` : `${item.total_harga_selisih.toLocaleString('id-ID')}`;

                rowsHtml += `
                    <tr>
                        <td>${escapeHtml(item.modisDisplay)}</td>
                        <td>${escapeHtml(item.plumd)}</td>
                        <td>${escapeHtml(item.deskripsi)}</td>
                        <td style="text-align: right;">${item.harga.toLocaleString('id-ID')}</td>
                        <td class="badge-selisih ${selisihClass}">${selisihStr}</td>
                        <td style="text-align: right;" class="badge-selisih ${selisihClass}">${totalHargaStr}</td>
                    </tr>
                `;
            });

            tableUploadSelisihBody.innerHTML = rowsHtml;
            tableUploadSelisihFoot.style.display = 'table-footer-group';

            cellTotalHargaFootUploadSelisih.className = '';
            if (grandTotalHarga > 0) {
                cellTotalHargaFootUploadSelisih.innerText = `+${grandTotalHarga.toLocaleString('id-ID')}`;
                cellTotalHargaFootUploadSelisih.classList.add('badge-selisih-positif');
            } else if (grandTotalHarga < 0) {
                cellTotalHargaFootUploadSelisih.innerText = `${grandTotalHarga.toLocaleString('id-ID')}`;
                cellTotalHargaFootUploadSelisih.classList.add('badge-selisih-negatif');
            } else {
                cellTotalHargaFootUploadSelisih.innerText = '0';
            }

            if (resultArray.length > 0) {
                btnUploadDBSelisihPLU.style.display = 'inline-flex';
            }
        }

        if (btnCariSelisihPLU) {
            btnCariSelisihPLU.addEventListener('click', processSearchSelisihPLU);
        }

        async function uploadSelisihPLUToDatabase() {
            if (currentSelisihSearchResult.length === 0) {
                alert('Tidak ada data untuk diupload.');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin mengupload data selisih PLU ini ke database MySQL?')) {
                return;
            }

            btnUploadDBSelisihPLU.disabled = true;
            btnUploadDBSelisihPLU.innerText = 'Mengupload...';

            try {
                const formData = new FormData();
                formData.append('action', 'upload_db');
                formData.append('account', selectedAccount || 'Unknown');
                formData.append('items', JSON.stringify(currentSelisihSearchResult));

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('Berhasil: ' + result.message);
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan/server saat mengupload data.');
            } finally {
                btnUploadDBSelisihPLU.disabled = false;
                btnUploadDBSelisihPLU.innerHTML = '<i data-lucide="database" style="width: 14px; height: 14px;"></i> Upload Data';
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        }

        if (btnUploadDBSelisihPLU) {
            btnUploadDBSelisihPLU.addEventListener('click', uploadSelisihPLUToDatabase);
        }

        async function uploadKeDatabase() {
            if (currentFilteredItems.length === 0) {
                alert('Tidak ada data untuk diupload.');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin mengupload seluruh data tabel ini ke database MySQL?')) {
                return;
            }

            const payloadItems = currentFilteredItems.map(item => {
                const stokLpp = (parseInt(item.QTY) || 0) + (parseInt(item.NPB) || 0);
                const stokFisik = item.STOK_FISIK !== undefined ? item.STOK_FISIK : 0;
                const selisih = stokFisik - stokLpp;
                const harga = parseFloat(item.PRICE || 0);
                const totalHargaSelisih = harga * selisih;

                return {
                    nama_rak: item.NAMA_RAK_FULL || '',
                    noshelf: item.NOSHELF || '',
                    kirikanan: item.KIRIKANAN || '',
                    plumd: item.PLUMD || '',
                    deskripsi: item.DESC2 || '',
                    harga: harga,
                    qty_lpp: stokLpp,
                    stok_fisik: stokFisik,
                    selisih: selisih,
                    total_harga_selisih: totalHargaSelisih
                };
            });

            btnUploadDB.disabled = true;
            btnUploadDB.innerText = 'Mengupload...';

            try {
                const formData = new FormData();
                formData.append('action', 'upload_db');
                formData.append('account', selectedAccount || 'Unknown');
                formData.append('items', JSON.stringify(payloadItems));

                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('Berhasil: ' + result.message);
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan/server saat mengupload data.');
            } finally {
                btnUploadDB.disabled = false;
                btnUploadDB.innerHTML = '<i data-lucide="database" style="width: 14px; height: 14px;"></i> Upload Data';
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        }

        btnUploadDB.addEventListener('click', uploadKeDatabase);

        function justifyLine(leftText, rightText, targetWidth) {
            const minSpaces = 1;
            const currentLen = leftText.length + rightText.length;
            const spacesNeeded = Math.max(minSpaces, targetWidth - currentLen);
            return leftText + ' '.repeat(spacesNeeded) + rightText;
        }

        function copyHasilAkhir() {
            if (currentFilteredItems.length === 0) {
                alert('Tidak ada data untuk disalin.');
                return;
            }

            const itemsSelisih = [];
            for (let i = 0; i < currentFilteredItems.length; i++) {
                const item = currentFilteredItems[i];
                const stokLpp = (parseInt(item.QTY) || 0) + (parseInt(item.NPB) || 0);
                
                let selisih = 0;
                if (item.STOK_FISIK === undefined) {
                    selisih = -stokLpp;
                } else {
                    selisih = item.STOK_FISIK - stokLpp;
                }

                if (selisih !== 0) {
                    const selisihStr = selisih > 0 ? `(+${selisih})` : `(${selisih})`;
                    itemsSelisih.push({
                        plumd: String(item.PLUMD || '').trim(),
                        desc: String(item.DESC2 || '').trim(),
                        selisihStr: selisihStr
                    });
                }
            }

            if (itemsSelisih.length === 0) {
                alert('Tidak ada data selisih untuk disalin.');
                return;
            }

            let namaRakFull = selectModis.value || (currentFilteredItems[0] ? currentFilteredItems[0].NAMA_RAK_FULL : '');
            let namaRak6Char = String(namaRakFull || '').substring(0, 6);

            const preparedLines = itemsSelisih.map(item => {
                const leftText = `${item.plumd} ${item.desc}`;
                const rightText = item.selisihStr;
                return { leftText, rightText };
            });

            let maxLen = 0;
            preparedLines.forEach(line => {
                const len = line.leftText.length + 1 + line.rightText.length;
                if (len > maxLen) maxLen = len;
            });

            const targetWidth = Math.max(maxLen, 35);

            let resultText = "```\n";
            resultText += `HASIL SO ( ${namaRak6Char} )\n`;
            resultText += "————————————————————————————\n";

            preparedLines.forEach(line => {
                resultText += justifyLine(line.leftText, line.rightText, targetWidth) + "\n";
            });

            resultText += "```";

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(resultText).then(() => {
                    alert('Hasil SO berhasil disalin ke clipboard!');
                }).catch(err => {
                    fallbackCopyTextToClipboard(resultText);
                });
            } else {
                fallbackCopyTextToClipboard(resultText);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    alert('Hasil SO berhasil disalin ke clipboard!');
                } else {
                    alert('Gagal menyalin hasil SO.');
                }
            } catch (err) {
                alert('Gagal menyalin hasil SO: ' + err);
            }

            document.body.removeChild(textArea);
        }

        btnSalinHasilAkhir.addEventListener('click', copyHasilAkhir);

        async function resetAccountData() {
            if (!selectedAccount) return;
            
            if (!confirm(`Apakah Anda yakin ingin mereset semua filter, inputan stok fisik, dan history untuk ${selectedAccount}?`)) {
                return;
            }

            const filterKey = getFilterCacheKey();
            if (filterKey) {
                localStorage.removeItem(filterKey);
            }

            selectModis.value = "";
            selectDariShelfing.value = "";
            selectSampaiShelfing.value = "";

            globalProcessedItems.forEach(item => {
                item.STOK_FISIK = undefined;
                item.INPUT_HISTORY = [];
            });
            lastUpdatedPlumdKey = null;

            await saveInputStateToCache();

            updateMenuLockStatus();
            
            switchMenu(menuModis, sectionModis);

            alert('Data dan filter akun berhasil direset.');
        }

        btnResetHasilAkhir.addEventListener('click', resetAccountData);

        function openInputModal(index, sourceList) {
            activeRowIndex = index;
            activeModalSourceItems = sourceList || currentFilteredItems;
            
            const item = activeModalSourceItems[index];
            if (!item) return;

            const plumdVal = String(item.PLUMD || '').trim();
            modalProductImg.src = plumdVal ? `https://cdn-klik.klikindomaret.com/klik-catalog/product/${plumdVal}_1.jpg` : '';

            modalPlumd.innerText = item.PLUMD || '-';
            modalDesc.innerText = item.DESC2 || '-';
            modalInputValue.value = '';

            inputModal.classList.add('active');
            setTimeout(() => {
                modalInputValue.focus();
            }, 100);
        }

        function closeModal() {
            inputModal.classList.remove('active');
            activeRowIndex = null;
            activeModalSourceItems = null;
        }

        btnModalClose.addEventListener('click', closeModal);

        async function updateItemStock(op) {
            if (activeRowIndex === null) return;
            
            const sourceList = activeModalSourceItems || currentFilteredItems;
            const item = sourceList[activeRowIndex];
            if (!item) return;

            const valInput = parseInt(modalInputValue.value) || 0;
            if (valInput <= 0) {
                closeModal();
                return;
            }

            if (!item.INPUT_HISTORY) {
                item.INPUT_HISTORY = [];
            }

            let currentFisik = item.STOK_FISIK !== undefined ? item.STOK_FISIK : 0;

            if (op === '+') {
                currentFisik += valInput;
                if (item.INPUT_HISTORY.length === 0) {
                    item.INPUT_HISTORY.push(`${valInput}`);
                } else {
                    item.INPUT_HISTORY.push(`+${valInput}`);
                }
            } else if (op === '-') {
                currentFisik = Math.max(0, currentFisik - valInput);
                item.INPUT_HISTORY.push(`-${valInput}`);
            }

            item.STOK_FISIK = currentFisik;
            lastUpdatedPlumdKey = getItemUniqueKey(item);

            await saveInputStateToCache();

            const activeQuery = searchInput.value.trim();
            if (activeQuery !== '') {
                const searchedItems = currentFilteredItems.filter(it => {
                    const plumd = String(it.PLUMD || '');
                    if (plumd.includes(activeQuery)) return true;

                    const barcodes = plumdToBarcodesMap.get(plumd);
                    if (barcodes) {
                        for (let barcd of barcodes) {
                            if (barcd.includes(activeQuery)) return true;
                        }
                    }
                    return false;
                });
                renderTable(searchedItems);
            } else {
                renderTable(currentFilteredItems);
            }

            const targetRow = document.getElementById(`rowItem_${activeRowIndex}`);
            if (targetRow) {
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            closeModal();
        }

        btnModalPlus.addEventListener('click', function() {
            updateItemStock('+');
        });

        btnModalMinus.addEventListener('click', function() {
            updateItemStock('-');
        });

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>