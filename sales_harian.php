<?php 
$host = 'db.fr-roub1.bengt.wasmernet.com';
$port = '20184';
$dbname = 'stock_opname';
$dbuser = 'user_a2e7c23a';
$dbpass = 'pw_XVc32h58LGUKszLr1XCGg8R8FVDzTAcy';

$pdo = null;
$db_error = null;
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $db_error = "Koneksi database gagal: " . $e->getMessage();
}

$bulan_names = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$current_month = (int) date('n');
$current_year = (int) date('Y');

$selected_bulan = isset($_REQUEST['bulan']) ? intval($_REQUEST['bulan']) : $current_month;
$selected_tahun = isset($_REQUEST['tahun']) ? intval($_REQUEST['tahun']) : $current_year;

$prev_bulan = $selected_bulan - 1;
$prev_tahun = $selected_tahun;
if ($prev_bulan < 1) {
    $prev_bulan = 12;
    $prev_tahun--;
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_sales') {
    $target_spd = floatval(str_replace('.', '', $_POST['target_spd']));
    $target_akm_sales = floatval(str_replace('.', '', $_POST['target_akm_sales']));

    $stmt = $pdo->prepare("INSERT INTO sales_target (bulan, tahun, target_spd, target_akm_sales) 
                           VALUES (?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE target_spd=?, target_akm_sales=?");
    $stmt->execute([$selected_bulan, $selected_tahun, $target_spd, $target_akm_sales, $target_spd, $target_akm_sales]);

    $stmt_daily = $pdo->prepare("INSERT INTO sales_daily (bulan, tahun, tgl, sales_harian, struk) 
                                 VALUES (?, ?, ?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE sales_harian=?, struk=?");

    if (isset($_POST['daily']) && is_array($_POST['daily'])) {
        foreach ($_POST['daily'] as $tgl => $row) {
            $sales_raw = trim($row['sales']);
            $struk_raw = trim($row['struk']);

            if ($sales_raw !== '' || $struk_raw !== '') {
                $sales = floatval(str_replace('.', '', $sales_raw));
                $struk = floatval(str_replace('.', '', $struk_raw));
                $stmt_daily->execute([$selected_bulan, $selected_tahun, $tgl, $sales, $struk, $sales, $struk]);
            } else {
                $stmt_del = $pdo->prepare("DELETE FROM sales_daily WHERE bulan=? AND tahun=? AND tgl=?");
                $stmt_del->execute([$selected_bulan, $selected_tahun, $tgl]);
            }
        }
    }
}

$target_spd = 0;
$target_akm_sales = 0;
$current_daily = [];
$prev_daily_raw = [];

if ($pdo) {
    $stmt_target = $pdo->prepare("SELECT target_spd, target_akm_sales FROM sales_target WHERE bulan=? AND tahun=?");
    $stmt_target->execute([$selected_bulan, $selected_tahun]);
    $target = $stmt_target->fetch(PDO::FETCH_ASSOC);

    $target_spd = $target ? $target['target_spd'] : 0;
    $target_akm_sales = $target ? $target['target_akm_sales'] : 0;

    $stmt_daily_curr = $pdo->prepare("SELECT tgl, sales_harian, struk FROM sales_daily WHERE bulan=? AND tahun=?");
    $stmt_daily_curr->execute([$selected_bulan, $selected_tahun]);
    $current_daily = $stmt_daily_curr->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

    $stmt_prev = $pdo->prepare("SELECT tgl, sales_harian, struk FROM sales_daily WHERE bulan=? AND tahun=? ORDER BY tgl ASC");
    $stmt_prev->execute([$prev_bulan, $prev_tahun]);
    $prev_daily_raw = $stmt_prev->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
}

$data_prev = [];
$prev_akm_sales = 0;
$prev_akm_struk = 0;
for ($t = 1; $t <= 31; $t++) {
    if (isset($prev_daily_raw[$t])) {
        $prev_akm_sales += $prev_daily_raw[$t]['sales_harian'];
        $prev_akm_struk += $prev_daily_raw[$t]['struk'];
        $spd_p = $prev_akm_sales / $t;
        $std_p = $prev_akm_struk / $t;
        $apc2_p = ($std_p > 0) ? ($spd_p / $std_p) : 0;
        $data_prev[$t] = ['spd' => $spd_p, 'std' => $std_p, 'apc2' => $apc2_p];
    } else {
        $data_prev[$t] = ['spd' => 0, 'std' => 0, 'apc2' => 0];
    }
}

$num_days = (int) date('t', mktime(0, 0, 0, $selected_bulan, 1, $selected_tahun));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Laporan Sales</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
            min-height: 100vh;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            padding: 15px;
            max-width: 1440px;
            margin: 0 auto;
        }

        .search-input {
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input:focus, .filter-select:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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

        .btn-submit {
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

        .sales-title-header {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .target-section-sales {
            display: flex;
            flex-direction: column;
            gap: 16px;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .target-inputs-sales {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        @media (min-width: 640px) {
            .target-inputs-sales {
                flex-direction: row;
                width: 100%;
                align-items: center;
                gap: 16px;
            }
        }

        .form-group-sales {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
        }

        @media (min-width: 640px) {
            .form-group-sales {
                width: auto;
                justify-content: start;
            }
        }

        .input-currency-sales {
            text-align: right !important;
            font-weight: 600;
            width: 140px;
        }

        .btn-sales-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .table-container-sales {
            background-color: #ffffff;
            border-radius: 12px;
            overflow: auto;
            max-height: 70vh;
            -webkit-overflow-scrolling: touch;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            width: 100%;
        }

        table.main-table-sales { 
            border-collapse: separate; 
            border-spacing: 0;
            width: 100%; 
            white-space: nowrap;
            font-size: 13px;
        }

        table.main-table-sales th, table.main-table-sales td { 
            border-right: 1px solid #e2e8f0; 
            border-bottom: 1px solid #e2e8f0; 
            padding: 8px 10px; 
            text-align: center; 
            vertical-align: middle;
        }

        table.main-table-sales tbody td.clickable-cell {
            cursor: pointer;
        }

        table.main-table-sales th { 
            background-color: #f8fafc; 
            color: #0f172a;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.3px;
            cursor: default;
            position: sticky;
            z-index: 10;
        }

        table.main-table-sales thead tr:nth-child(1) th {
            top: 0;
        }

        table.main-table-sales thead tr:nth-child(2) th {
            top: 34px;
        }

        table.main-table-sales tbody tr:hover {
            background-color: #f8fafc;
        }

        table.main-table-sales td.tgl-col { 
            font-weight: 600;
            color: #0f172a;
            background-color: #fafafa;
            cursor: pointer;
        }

        .input-table-sales {
            width: 100px;
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            text-align: right;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
        }

        .input-table-struk-sales {
            width: 65px;
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            text-align: right;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
        }

        .col-struk-sales {
            width: 75px;
        }

        .badge-positive-sales { color: #16a34a; font-weight: 600; }
        .badge-negative-sales { color: #dc2626; font-weight: 600; }

        .modal-overlay-sales {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-overlay-sales.active {
            display: flex;
        }

        .modal-card-sales {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 320px;
            padding: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transform: scale(0.95);
            transition: transform 0.2s ease;
            position: relative;
        }

        .modal-overlay-sales.active .modal-card-sales {
            transform: scale(1);
        }

        .btn-modal-close-x-sales {
            position: absolute;
            top: 14px;
            right: 14px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.15s ease;
            line-height: 1;
        }

        .btn-modal-close-x-sales:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .modal-body-sales {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 6px;
        }

        .modal-info-group-sales {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .modal-date-sales {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .modal-col-name-sales {
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modal-value-display-sales {
            width: 100%;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            padding: 14px;
            background-color: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            word-break: break-all;
        }

        .modal-nav-sales {
            display: flex;
            gap: 8px;
        }

        .nav-btn-sales {
            flex: 1;
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .nav-btn-sales:hover {
            background-color: #e2e8f0;
        }

        .toast-copy {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            z-index: 10000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .toast-copy.show {
            opacity: 1;
        }
    </style>
</head>
<body>

    <div class="container">
        <?php if ($db_error): ?>
            <p class="error"><?= htmlspecialchars($db_error) ?></p>
        <?php else: ?>
            <div class="sales-title-header">SALES BULAN <?= strtoupper($bulan_names[$selected_bulan]) . " " . $selected_tahun ?></div>

            <form method="POST" action="">
                <input type="hidden" name="action" value="save_sales">
                
                <div class="target-section-sales">
                    <div class="form-group-sales">
                        <label>Pilih Periode:</label>
                        <div style="display: flex; gap: 8px;">
                            <select name="bulan" form="form_filter_sales" class="filter-select" style="width: auto;">
                                <?php foreach ($bulan_names as $m_num => $m_name): ?>
                                    <option value="<?= $m_num ?>" <?= $m_num === $selected_bulan ? 'selected' : '' ?>><?= $m_name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="tahun" form="form_filter_sales" class="filter-select" style="width: auto;">
                                <?php for ($y = 2025; $y <= 2030; $y++): ?>
                                    <option value="<?= $y ?>" <?= $y === $selected_tahun ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="target-inputs-sales">
                        <div class="form-group-sales">
                            <label>Target SPD:</label>
                            <input type="text" id="target_spd_input" class="input-currency-sales number-only-sales search-input" name="target_spd" value="<?= number_format($target_spd, 0, ',', '.') ?>">
                        </div>
                        <div class="form-group-sales">
                            <label>Target Akm Sales:</label>
                            <input type="text" id="target_akm_sales_input" class="input-currency-sales number-only-sales search-input" name="target_akm_sales" value="<?= number_format($target_akm_sales, 0, ',', '.') ?>">
                        </div>
                    </div>

                    <div class="btn-sales-actions">
                        <button type="submit" class="btn-submit" style="flex: none; width: auto; padding: 9px 18px;">Simpan Ke Database</button>
                        <button type="submit" form="form_filter_sales" class="btn-reset" style="flex: none; width: auto; padding: 9px 18px; margin: 0;">Tampilkan</button>
                    </div>
                </div>

                <div class="table-container-sales">
                    <table class="main-table-sales" id="dataTableSales" data-bulan="<?= htmlspecialchars($bulan_names[$selected_bulan]) ?>" data-tahun="<?= $selected_tahun ?>">
                        <thead>
                            <tr>
                                <th rowspan="2">TGL</th>
                                <th rowspan="2">SALES HARIAN</th>
                                <th rowspan="2" class="col-struk-sales">STRUK</th>
                                <th rowspan="2">APC 1</th>
                                <th rowspan="2">AKM SALES</th>
                                <th rowspan="2">AKM STRUK</th>
                                <th rowspan="2">SPD</th>
                                <th rowspan="2">STD</th>
                                <th rowspan="2">APC 2</th>
                                <th colspan="2">% ACHIEVEMENT</th>
                                <th colspan="3">GROWTH (%) vs <?= strtoupper($bulan_names[$prev_bulan]) ?></th>
                            </tr>
                            <tr>
                                <th>% ACH AKM SALES</th>
                                <th>% ACH SPD</th>
                                <th>GROWTH SPD</th>
                                <th>GROWTH STD</th>
                                <th>GROWTH APC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $akm_sales = 0;
                            $akm_struk = 0;

                            for ($tgl = 1; $tgl <= $num_days; $tgl++): 
                                $has_data = isset($current_daily[$tgl]);
                                $sales_harian = $has_data ? floatval($current_daily[$tgl]['sales_harian']) : 0;
                                $struk = $has_data ? floatval($current_daily[$tgl]['struk']) : 0;

                                if ($has_data) {
                                    $akm_sales += $sales_harian;
                                    $akm_struk += $struk;

                                    $apc1 = ($struk > 0) ? ($sales_harian / $struk) : 0;
                                    $spd  = $akm_sales / $tgl;
                                    $std  = $akm_struk / $tgl;
                                    $apc2 = ($std > 0) ? ($spd / $std) : 0;

                                    $pct_akm_sales = ($target_akm_sales > 0) ? (100 / $target_akm_sales * $akm_sales) : 0;
                                    $pct_spd       = ($target_spd > 0) ? (100 / $target_spd * $spd) : 0;

                                    $prev_spd  = $data_prev[$tgl]['spd'];
                                    $prev_std  = $data_prev[$tgl]['std'];
                                    $prev_apc2 = $data_prev[$tgl]['apc2'];

                                    $growth_spd = ($prev_spd > 0) ? (($spd - $prev_spd) / $prev_spd * 100) : 0;
                                    $growth_std = ($prev_std > 0) ? (($std - $prev_std) / $prev_std * 100) : 0;
                                    $growth_apc = ($prev_apc2 > 0) ? (($apc2 - $prev_apc2) / $prev_apc2 * 100) : 0;
                                }
                            ?>
                            <tr data-tgl="<?= $tgl ?>">
                                <td class="tgl-col" onclick="copySalesRowData(<?= $tgl ?>)"><?= $tgl ?></td>
                                <td>
                                    <input type="text" class="input-table-sales number-only-sales" name="daily[<?= $tgl ?>][sales]" value="<?= $has_data ? number_format($sales_harian, 0, ',', '.') : '' ?>">
                                </td>
                                <td class="col-struk-sales">
                                    <input type="text" class="input-table-struk-sales number-only-sales" name="daily[<?= $tgl ?>][struk]" value="<?= $has_data ? number_format($struk, 0, ',', '.') : '' ?>">
                                </td>
                                <td class="clickable-cell"><?= $has_data && $apc1 > 0 ? number_format($apc1, 0, ',', '.') : '-' ?></td>
                                <td class="clickable-cell"><?= $has_data && $akm_sales > 0 ? number_format($akm_sales, 0, ',', '.') : '-' ?></td>
                                <td class="clickable-cell"><?= $has_data && $akm_struk > 0 ? number_format($akm_struk, 0, ',', '.') : '-' ?></td>
                                <td class="clickable-cell"><?= $has_data && $spd > 0 ? number_format($spd, 0, ',', '.') : '-' ?></td>
                                <td class="clickable-cell"><?= $has_data && $std > 0 ? number_format($std, 0, ',', '.') : '-' ?></td>
                                <td class="clickable-cell"><?= $has_data && $apc2 > 0 ? number_format($apc2, 0, ',', '.') : '-' ?></td>
                                <td class="clickable-cell"><?= $has_data && $akm_sales > 0 ? round($pct_akm_sales) . '%' : '-' ?></td>
                                <td class="clickable-cell"><?= $has_data && $spd > 0 ? number_format($pct_spd, 1, ',', '.') . '%' : '-' ?></td>
                                
                                <td class="clickable-cell <?= $has_data ? ($growth_spd >= 0 ? 'badge-positive-sales' : 'badge-negative-sales') : '' ?>">
                                    <?= $has_data ? number_format($growth_spd, 1, ',', '.') . '%' : '-' ?>
                                </td>
                                <td class="clickable-cell <?= $has_data ? ($growth_std >= 0 ? 'badge-positive-sales' : 'badge-negative-sales') : '' ?>">
                                    <?= $has_data ? number_format($growth_std, 1, ',', '.') . '%' : '-' ?>
                                </td>
                                <td class="clickable-cell <?= $has_data ? ($growth_apc >= 0 ? 'badge-positive-sales' : 'badge-negative-sales') : '' ?>">
                                    <?= $has_data ? number_format($growth_apc, 1, ',', '.') . '%' : '-' ?>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </form>
            
            <form id="form_filter_sales" method="GET" action="" style="display:none;"></form>
        <?php endif; ?>
    </div>

    <div class="modal-overlay-sales" id="viewModalSales">
        <div class="modal-card-sales">
            <button type="button" class="btn-modal-close-x-sales" id="btnModalCloseXSales">&times;</button>
            <div class="modal-body-sales">
                <div class="modal-info-group-sales">
                    <span class="modal-date-sales" id="modalDateSales">TANGGAL 1</span>
                    <span class="modal-col-name-sales" id="modalColNameSales">COLUMNS</span>
                </div>
                <div class="modal-value-display-sales" id="modalValueDisplaySales">-</div>
                <div class="modal-nav-sales">
                    <button type="button" class="nav-btn-sales" id="btnPrevSales">&lt;</button>
                    <button type="button" class="nav-btn-sales" id="btnNextSales">&gt;</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-copy" id="toastCopy">Teks laporan berhasil disalin!</div>

    <script>
        document.querySelectorAll('.number-only-sales').forEach(function(input) {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value !== '') {
                    this.value = new Intl.NumberFormat('id-ID').format(value);
                } else {
                    this.value = '';
                }
            });
        });

        const columnHeadersSales = [
            "TGL",
            "SALES HARIAN",
            "STRUK",
            "APC 1",
            "AKM SALES",
            "AKM STRUK",
            "SPD",
            "STD",
            "APC 2",
            "% ACH AKM SALES",
            "% ACH SPD",
            "GROWTH SPD",
            "GROWTH STD",
            "GROWTH APC"
        ];

        let currentCellSales = null;
        const modalSales = document.getElementById('viewModalSales');
        const modalColNameSales = document.getElementById('modalColNameSales');
        const modalDateSales = document.getElementById('modalDateSales');
        const modalValueDisplaySales = document.getElementById('modalValueDisplaySales');

        function getCellValueSales(td) {
            const input = td.querySelector('input');
            if (input) {
                return input.value.trim() !== '' ? input.value : '-';
            }
            return td.textContent.trim();
        }

        function openModalForCellSales(td) {
            currentCellSales = td;
            const colIndex = td.cellIndex;
            const tr = td.parentElement;
            const tgl = tr.querySelector('.tgl-col').textContent.trim();
            
            if (modalDateSales) modalDateSales.textContent = 'TANGGAL ' + tgl;
            if (modalColNameSales) modalColNameSales.textContent = columnHeadersSales[colIndex] || 'INFORMASI';
            if (modalValueDisplaySales) modalValueDisplaySales.textContent = getCellValueSales(td);
            
            if (modalSales) modalSales.classList.add('active');
        }

        document.querySelectorAll('#dataTableSales tbody td.clickable-cell').forEach(function(td) {
            td.addEventListener('click', function(e) {
                openModalForCellSales(this);
            });
        });

        const btnCloseSales = document.getElementById('btnModalCloseXSales');
        if (btnCloseSales) {
            btnCloseSales.addEventListener('click', function() {
                if (modalSales) modalSales.classList.remove('active');
            });
        }

        if (modalSales) {
            modalSales.addEventListener('click', function(e) {
                if (e.target === modalSales) {
                    modalSales.classList.remove('active');
                }
            });
        }

        function navigateColumnSales(direction) {
            if (!currentCellSales) return;
            
            const tr = currentCellSales.parentElement;
            const totalCols = columnHeadersSales.length;
            let newColIndex = currentCellSales.cellIndex + direction;
            
            while (newColIndex >= 0 && newColIndex < totalCols) {
                const candidateTd = tr.cells[newColIndex];
                if (candidateTd.classList.contains('clickable-cell')) {
                    openModalForCellSales(candidateTd);
                    return;
                }
                newColIndex += direction;
            }
        }

        const btnPrevSales = document.getElementById('btnPrevSales');
        if (btnPrevSales) {
            btnPrevSales.addEventListener('click', function() {
                navigateColumnSales(-1);
            });
        }

        const btnNextSales = document.getElementById('btnNextSales');
        if (btnNextSales) {
            btnNextSales.addEventListener('click', function() {
                navigateColumnSales(1);
            });
        }

        function showToastCopy() {
            const toast = document.getElementById('toastCopy');
            if (toast) {
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 2000);
            }
        }

        function formatGrowthVal(valStr) {
            valStr = valStr.trim();
            if (valStr === '' || valStr === '-') return '-';
            if (!valStr.startsWith('+') && !valStr.startsWith('-')) {
                const num = parseFloat(valStr.replace(',', '.'));
                if (!isNaN(num) && num > 0) {
                    return '+' + valStr;
                }
            }
            return valStr;
        }

        function copySalesRowData(targetTgl) {
            const table = document.getElementById('dataTableSales');
            if (!table) return;

            const bulan = table.getAttribute('data-bulan') || '';
            const tahun = table.getAttribute('data-tahun') || '';

            const targetSpd = document.getElementById('target_spd_input') ? document.getElementById('target_spd_input').value.trim() : '0';
            const targetSales = document.getElementById('target_akm_sales_input') ? document.getElementById('target_akm_sales_input').value.trim() : '0';

            let listRows = [];
            let targetRowData = null;

            const trs = table.querySelectorAll('tbody tr');
            trs.forEach(tr => {
                const tgl = parseInt(tr.getAttribute('data-tgl'));
                if (tgl <= targetTgl) {
                    const salesInput = tr.querySelector('input[name*="[sales]"]');
                    const strukInput = tr.querySelector('input[name*="[struk]"]');

                    const salesVal = salesInput ? salesInput.value.trim() : '';
                    const strukVal = strukInput ? strukInput.value.trim() : '';

                    if (salesVal !== '' || strukVal !== '') {
                        const cells = tr.cells;
                        const apc1Val = cells[3] ? cells[3].textContent.trim() : '-';
                        listRows.push(`${tgl}. ${salesVal}_${strukVal}_${apc1Val}`);
                    }

                    if (tgl === targetTgl) {
                        const cells = tr.cells;
                        targetRowData = {
                            spd: cells[6] ? cells[6].textContent.trim() : '-',
                            std: cells[7] ? cells[7].textContent.trim() : '-',
                            apc2: cells[8] ? cells[8].textContent.trim() : '-',
                            achAkmSales: cells[9] ? cells[9].textContent.trim() : '-',
                            achSpd: cells[10] ? cells[10].textContent.trim() : '-',
                            growthSpd: cells[11] ? cells[11].textContent.trim() : '-',
                            growthStd: cells[12] ? cells[12].textContent.trim() : '-',
                            growthApc: cells[13] ? cells[13].textContent.trim() : '-'
                        };
                    }
                }
            });

            if (!targetRowData) return;

            const textOutput = `Laporan sales 
BULAN : ${bulan} ${tahun}
TOKO. :  F8YF/GRAND MUTIARA

Tgl_sales_struk_apc
  
${listRows.join('\n')}

Spd : ${targetRowData.spd}
Std  : ${targetRowData.std}
Apc : ${targetRowData.apc2}

Target Spd : ${targetSpd}
Target ttl sales : ${targetSales}

Growth : Spd : ${formatGrowthVal(targetRowData.growthSpd)}
                 Std : ${formatGrowthVal(targetRowData.growthStd)}
                Apc : ${formatGrowthVal(targetRowData.growthApc)}

Achv Target Spd : ${targetRowData.achSpd}
Achv Target sales : ${targetRowData.achAkmSales}`;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textOutput).then(() => {
                    showToastCopy();
                }).catch(err => {
                    fallbackCopyText(textOutput);
                });
            } else {
                fallbackCopyText(textOutput);
            }
        }

        function fallbackCopyText(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToastCopy();
            } catch (err) {
                console.error('Fallback copy failed', err);
            }
            document.body.removeChild(textArea);
        }
    </script>

</body>
</html>