<?php
// Konfigurasi Database
$host   = 'db.fr-roub1.bengt.wasmernet.com';
$port   = '20184';
$dbname = 'stock_opname';
$dbuser = 'user_a2e7c23a';
$dbpass = 'pw_XVc32h58LGUKszLr1XCGg8R8FVDzTAcy';

// Koneksi menggunakan PDO
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif; padding:20px; color:red;'><h3>Koneksi Gagal:</h3> " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Ambil parameter aksi
$action = $_GET['action'] ?? 'tables';
$table  = $_GET['table'] ?? '';
$message = '';
$error   = '';

// Proses Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Eksekusi Custom SQL Query
    if (isset($_POST['sql_query'])) {
        $query = trim($_POST['sql_query']);
        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            if (stripos($query, 'SELECT') === 0 || stripos($query, 'SHOW') === 0 || stripos($query, 'DESCRIBE') === 0) {
                $query_result = $stmt->fetchAll();
                $query_columns = $query_result ? array_keys($query_result[0]) : [];
            } else {
                $message = "Query berhasil dieksekusi. Affected rows: " . $stmt->rowCount();
            }
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
    
    // 2. Insert Data
    if (isset($_POST['insert_data']) && $table) {
        $columns = $_POST['col'] ?? [];
        $values  = $_POST['val'] ?? [];
        
        $valid_cols = [];
        $valid_vals = [];
        $params = [];
        
        foreach ($columns as $i => $col) {
            if ($values[$i] !== '') {
                $valid_cols[] = "`$col`";
                $valid_vals[] = "?";
                $params[] = $values[$i];
            }
        }
        
        if (!empty($valid_cols)) {
            try {
                $sql = "INSERT INTO `$table` (" . implode(', ', $valid_cols) . ") VALUES (" . implode(', ', $valid_vals) . ")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = "Data berhasil ditambahkan!";
                header("Location: ?action=browse&table=$table&msg=" . urlencode($message));
                exit;
            } catch (PDOException $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = "Tidak ada data yang diinput.";
        }
    }

    // 3. Update Data
    if (isset($_POST['update_data']) && $table) {
        $columns = $_POST['col'] ?? [];
        $values  = $_POST['val'] ?? [];
        $pk_col  = $_POST['pk_col'];
        $pk_val  = $_POST['pk_val'];

        $set_parts = [];
        $params = [];
        foreach ($columns as $i => $col) {
            $set_parts[] = "`$col` = ?";
            $params[] = $values[$i];
        }
        $params[] = $pk_val;

        try {
            $sql = "UPDATE `$table` SET " . implode(', ', $set_parts) . " WHERE `$pk_col` = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = "Data berhasil diperbarui!";
            header("Location: ?action=browse&table=$table&msg=" . urlencode($message));
            exit;
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}

// 4. Delete Single Row Data
if ($action === 'delete' && $table && isset($_GET['pk_col']) && isset($_GET['pk_val'])) {
    $pk_col = $_GET['pk_col'];
    $pk_val = $_GET['pk_val'];
    try {
        $stmt = $pdo->prepare("DELETE FROM `$table` WHERE `$pk_col` = ?");
        $stmt->execute([$pk_val]);
        $message = "Data berhasil dihapus!";
        header("Location: ?action=browse&table=$table&msg=" . urlencode($message));
        exit;
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

// 5. Truncate Table (Hapus seluruh isi tabel)
if ($action === 'truncate' && $table) {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE `$table`");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
        $message = "Seluruh isi tabel `$table` berhasil dikosongkan!";
        header("Location: ?action=browse&table=$table&msg=" . urlencode($message));
        exit;
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

// Ambil daftar tabel
$tables = [];
$stmt = $pdo->query("SHOW TABLES");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Manager - <?= htmlspecialchars($dbname) ?></title>
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
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 280px;
            background: var(--card-bg);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-host {
            font-size: 11px;
            color: var(--text-sub);
            font-weight: 500;
        }

        .sidebar-user {
            font-size: 12px;
            color: var(--text-sub);
        }

        .sidebar-user strong {
            color: var(--text-main);
        }

        .sidebar-header h2 {
            font-family: 'Source Serif 4', Georgia, serif;
            font-size: 18px;
            font-weight: 500;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0;
        }

        .sidebar-menu {
            overflow-y: auto;
            flex: 1;
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .menu-category {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-sub);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        /* Table List Menu Styling */
        .table-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .table-list li a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            color: var(--text-main);
            text-decoration: none;
            font-size: 13px;
            border-radius: 8px;
            transition: background 0.15s ease, color 0.15s ease;
            word-break: break-all;
        }

        .table-list li a:hover {
            background: var(--item-hover);
            color: var(--accent);
        }

        .table-list li a.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 500;
        }

        /* Main Content Styling */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .topbar {
            background: var(--card-bg);
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-shrink: 0;
        }

        .content-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        h1, h2, h3 {
            font-family: 'Source Serif 4', Georgia, serif;
            color: var(--text-main);
            font-weight: 500;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            letter-spacing: -0.3px;
        }

        .alert {
            padding: 14px 18px;
            margin-bottom: 20px;
            border-radius: 12px;
            font-size: 14px;
            border: 1px solid var(--border);
        }
        .alert-success { background: rgba(46, 204, 113, 0.1); color: #2ecc71; border-color: rgba(46, 204, 113, 0.2); }
        .alert-danger { background: rgba(231, 76, 60, 0.1); color: #e74c3c; border-color: rgba(231, 76, 60, 0.2); }

        /* Table Styling */
        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow-x: auto;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        table.data-table th, table.data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }

        table.data-table th {
            background: var(--item-hover);
            color: var(--text-main);
            font-weight: 600;
        }

        table.data-table tr:last-child td {
            border-bottom: none;
        }

        table.data-table tr:hover td {
            background: var(--item-hover);
        }

        /* Buttons & Forms */
        .btn {
            font-family: inherit;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .btn svg {
            width: 14px;
            height: 14px;
        }

        .btn-primary {
            background: var(--accent);
            color: #ffffff;
        }
        .btn-primary:hover { background: var(--accent-hover); }

        .btn-secondary {
            background: transparent;
            color: var(--text-main);
            border-color: var(--border-strong);
        }
        .btn-secondary:hover { background: var(--item-hover); }

        .btn-warning {
            background: rgba(241, 196, 15, 0.15);
            color: #f39c12;
            border-color: rgba(241, 196, 15, 0.3);
            padding: 4px 10px;
            font-size: 12px;
        }

        .btn-danger {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border-color: rgba(231, 76, 60, 0.3);
            padding: 4px 10px;
            font-size: 12px;
        }

        textarea, .form-control {
            width: 100%;
            padding: 12px 14px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease;
            box-sizing: border-box;
        }

        textarea:focus, .form-control:focus {
            border-color: var(--accent);
        }

        textarea {
            height: 140px;
            font-family: monospace;
            resize: vertical;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 13px;
            color: var(--text-sub);
        }

        /* Tabs */
        .nav-tabs {
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .nav-tabs a {
            padding: 10px 16px;
            text-decoration: none;
            color: var(--text-sub);
            font-weight: 500;
            font-size: 14px;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s ease, border-color 0.2s ease;
        }

        .nav-tabs a svg {
            width: 15px;
            height: 15px;
        }

        .nav-tabs a:hover {
            color: var(--text-main);
        }

        .nav-tabs a.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            font-size: 13px;
            color: var(--text-sub);
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { height: auto; overflow: visible; }
            .nav-tabs { flex-wrap: wrap; }
        }
    </style>
    <script>
        // Validasi Kode Akses Saat Halaman Dimuat
        window.addEventListener('DOMContentLoaded', () => {
            const kodeAksesBenar = '$$&&@@';
            let aksesDiberikan = false;
            
            // Anda bisa menggunakan sessionStorage agar tidak terus-menerus muncul dalam satu sesi browser
            if (sessionStorage.getItem('authorized') === 'true') {
                return;
            }

            while (!aksesDiberikan) {
                let inputKode = prompt("Masukkan kode akses:");
                
                // Jika pengguna menekan Cancel
                if (inputKode === null) {
                    alert("Akses dibatalkan. Halaman tidak dapat dimuat.");
                    document.body.innerHTML = "<div style='padding: 40px; font-family: sans-serif; text-align: center;'><h2>Akses Ditolak</h2><p>Silakan muat ulang halaman untuk mencoba kembali.</p></div>";
                    throw new Error("Akses dibatalkan");
                }
                
                if (inputKode === kodeAksesBenar) {
                    sessionStorage.setItem('authorized', 'true');
                    aksesDiberikan = true;
                } else {
                    alert("Kode akses salah! Silakan coba lagi.");
                }
            }
        });
    </script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-host">Host: <?= htmlspecialchars($host) ?>:<?= htmlspecialchars($port) ?></div>
            <div class="sidebar-user">User: <strong><?= htmlspecialchars($dbuser) ?></strong></div>
            <h2><?= htmlspecialchars($dbname) ?></h2>
        </div>
        <div class="sidebar-menu">
            <div>
                <span class="menu-category">Daftar Tabel</span>
                <ul class="table-list">
                    <?php foreach ($tables as $t): ?>
                        <li>
                            <a href="?action=browse&table=<?= $t ?>" class="<?= ($table === $t) ? 'active' : '' ?>">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <?= htmlspecialchars($t) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <a href="?" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                SQL Editor
            </a>
        </div>

        <div class="content-body">
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><strong>Error:</strong> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- HALAMAN 1: SQL EDITOR & BERANDA -->
            <?php if ($action === 'tables' && !$table): ?>
                <h2>SQL Editor</h2>
                <form method="POST">
                    <div class="form-group">
                        <textarea name="sql_query" placeholder="Tulis query SQL di sini (Contoh: SELECT * FROM nama_tabel)..."><?= htmlspecialchars($_POST['sql_query'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Jalankan Query
                    </button>
                </form>

                <?php if (isset($query_result)): ?>
                    <h3 style="margin-top: 30px; margin-bottom: 15px;">Hasil Query:</h3>
                    <?php if (empty($query_result)): ?>
                        <p style="color: var(--text-sub);">Query tidak menghasilkan data.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <?php foreach ($query_columns as $col): ?>
                                            <th><?= htmlspecialchars($col) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($query_result as $row): ?>
                                        <tr>
                                            <?php foreach ($query_columns as $col): ?>
                                                <td><?= htmlspecialchars($row[$col] ?? 'NULL') ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- HALAMAN 2: BROWSE TABEL -->
            <?php elseif ($action === 'browse' && $table): ?>
                <div class="nav-tabs">
                    <a href="?action=browse&table=<?= $table ?>" class="active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Jelajahi
                    </a>
                    <a href="?action=insert&table=<?= $table ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah
                    </a>
                    <a href="?action=structure&table=<?= $table ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9c0 .69.41 1.31 1.03 1.51h.09a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09c-.62 0-1.23.42-1.51 1.03z"></path></svg>
                        Struktur
                    </a>
                    <a href="?action=truncate&table=<?= $table ?>" onclick="return confirm('PERINGATAN: Seluruh isi data pada tabel ini akan dihapus permanen! Lanjutkan?')" style="color: #e74c3c;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        Kosongkan Tabel
                    </a>
                </div>

                <h2>Tabel: <code><?= htmlspecialchars($table) ?></code></h2>

                <?php
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = 10;
                $offset = ($page - 1) * $limit;

                $total_stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                $total_rows = $total_stmt->fetchColumn();
                $total_pages = ceil($total_rows / $limit);

                $pk_col = '';
                $pk_stmt = $pdo->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
                $pk_row = $pk_stmt->fetch();
                if ($pk_row) {
                    $pk_col = $pk_row['Column_name'];
                }

                $data_stmt = $pdo->query("SELECT * FROM `$table` LIMIT $limit OFFSET $offset");
                $rows = $data_stmt->fetchAll();
                ?>

                <?php if (empty($rows)): ?>
                    <p style="color: var(--text-sub);">Tidak ada data di dalam tabel ini.</p>
                <?php else: ?>
                    <?php $columns = array_keys($rows[0]); ?>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <?php if ($pk_col): ?><th>Aksi</th><?php endif; ?>
                                    <?php foreach ($columns as $col): ?>
                                        <th><?= htmlspecialchars($col) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <?php if ($pk_col): ?>
                                            <td>
                                                <a href="?action=edit&table=<?= $table ?>&pk_col=<?= $pk_col ?>&pk_val=<?= urlencode($row[$pk_col]) ?>" class="btn btn-warning">Edit</a>
                                                <a href="?action=delete&table=<?= $table ?>&pk_col=<?= $pk_col ?>&pk_val=<?= urlencode($row[$pk_col]) ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus baris ini?')">Hapus</a>
                                            </td>
                                        <?php endif; ?>
                                        <?php foreach ($columns as $col): ?>
                                            <td><?= htmlspecialchars($row[$col] ?? 'NULL') ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ringkasan Navigasi Prev / Next -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?action=browse&table=<?= $table ?>&page=<?= $page - 1 ?>" class="btn btn-secondary">← Prev</a>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">← Prev</button>
                            <?php endif; ?>

                            <span>Halaman <strong><?= $page ?></strong> dari <strong><?= $total_pages ?></strong></span>

                            <?php if ($page < $total_pages): ?>
                                <a href="?action=browse&table=<?= $table ?>&page=<?= $page + 1 ?>" class="btn btn-secondary">Next →</a>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">Next →</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- HALAMAN 3: INSERT DATA -->
            <?php elseif ($action === 'insert' && $table): ?>
                <div class="nav-tabs">
                    <a href="?action=browse&table=<?= $table ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Jelajahi
                    </a>
                    <a href="?action=insert&table=<?= $table ?>" class="active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah
                    </a>
                    <a href="?action=structure&table=<?= $table ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9c0 .69.41 1.31 1.03 1.51h.09a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09c-.62 0-1.23.42-1.51 1.03z"></path></svg>
                        Struktur
                    </a>
                </div>

                <h2>Tambah Data: <code><?= htmlspecialchars($table) ?></code></h2>
                
                <?php
                $columns_meta = $pdo->query("DESCRIBE `$table`")->fetchAll();
                ?>
                <form method="POST">
                    <?php foreach ($columns_meta as $meta): ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($meta['Field']) ?> <span style="color: var(--text-sub);">(<?= $meta['Type'] ?><?= $meta['Null'] === 'NO' ? ' *required' : '' ?>)</span></label>
                            <input type="hidden" name="col[]" value="<?= htmlspecialchars($meta['Field']) ?>">
                            <input type="text" name="val[]" class="form-control" placeholder="<?= htmlspecialchars($meta['Default'] ?? '') ?>">
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" name="insert_data" class="btn btn-primary">Simpan Data</button>
                    <a href="?action=browse&table=<?= $table ?>" class="btn btn-secondary">Batal</a>
                </form>

            <!-- HALAMAN 4: EDIT DATA -->
            <?php elseif ($action === 'edit' && $table && isset($_GET['pk_col']) && isset($_GET['pk_val'])): ?>
                <?php
                $pk_col = $_GET['pk_col'];
                $pk_val = $_GET['pk_val'];
                
                $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE `$pk_col` = ?");
                $stmt->execute([$pk_val]);
                $current_data = $stmt->fetch();

                if (!$current_data) {
                    die("Data tidak ditemukan.");
                }
                
                $columns_meta = $pdo->query("DESCRIBE `$table`")->fetchAll();
                ?>
                <h2>Edit Data: <code><?= htmlspecialchars($table) ?></code></h2>
                
                <form method="POST">
                    <input type="hidden" name="pk_col" value="<?= htmlspecialchars($pk_col) ?>">
                    <input type="hidden" name="pk_val" value="<?= htmlspecialchars($pk_val) ?>">
                    
                    <?php foreach ($columns_meta as $meta): ?>
                        <div class="form-group">
                            <label><?= htmlspecialchars($meta['Field']) ?> <span style="color: var(--text-sub);">(<?= $meta['Type'] ?>)</span></label>
                            <input type="hidden" name="col[]" value="<?= htmlspecialchars($meta['Field']) ?>">
                            <input type="text" name="val[]" class="form-control" value="<?= htmlspecialchars($current_data[$meta['Field']] ?? '') ?>">
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" name="update_data" class="btn btn-primary">Perbarui Data</button>
                    <a href="?action=browse&table=<?= $table ?>" class="btn btn-secondary">Batal</a>
                </form>

            <!-- HALAMAN 5: STRUKTUR TABEL -->
            <?php elseif ($action === 'structure' && $table): ?>
                <div class="nav-tabs">
                    <a href="?action=browse&table=<?= $table ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Jelajahi
                    </a>
                    <a href="?action=insert&table=<?= $table ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah
                    </a>
                    <a href="?action=structure&table=<?= $table ?>" class="active">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l-.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06-.06a1.65 1.65 0 0 0-.33 1.82V9c0 .69.41 1.31 1.03 1.51h.09a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09c-.62 0-1.23.42-1.51 1.03z"></path></svg>
                        Struktur
                    </a>
                    <a href="?action=truncate&table=<?= $table ?>" onclick="return confirm('PERINGATAN: Seluruh isi data pada tabel ini akan dihapus permanen! Lanjutkan?')" style="color: #e74c3c;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        Kosongkan Tabel
                    </a>
                </div>

                <h2>Struktur Tabel: <code><?= htmlspecialchars($table) ?></code></h2>
                <?php
                $columns_meta = $pdo->query("DESCRIBE `$table`")->fetchAll();
                ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Type</th>
                                <th>Null</th>
                                <th>Key</th>
                                <th>Default</th>
                                <th>Extra</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($columns_meta as $meta): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($meta['Field']) ?></strong></td>
                                    <td><?= htmlspecialchars($meta['Type']) ?></td>
                                    <td><?= htmlspecialchars($meta['Null']) ?></td>
                                    <td><?= htmlspecialchars($meta['Key']) ?></td>
                                    <td><?= htmlspecialchars($meta['Default'] ?? 'NULL') ?></td>
                                    <td><?= htmlspecialchars($meta['Extra']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>
    </div>

</body>
</html>