<?php
$host   = 'db.fr-roub1.bengt.wasmernet.com';
$port   = '20184';
$dbname = 'stock_opname';
$dbuser = 'user_a2e7c23a';
$dbpass = 'pw_XVc32h58LGUKszLr1XCGg8R8FVDzTAcy';

$message = '';
$days = ['Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Senin', 'Selasa', 'Rabu'];

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        $pdo->beginTransaction();

        $pdo->exec("DELETE FROM sales_reports");
        $pdo->exec("DELETE FROM items");

        $stmtItem  = $pdo->prepare("INSERT INTO items (slot_item, target_qty) VALUES (:slot_item, :target_qty)");
        $stmtSales = $pdo->prepare("INSERT INTO sales_reports (item_id, day_name, shift_1, shift_2) VALUES (:item_id, :day_name, :shift_1, :shift_2)");

        $insertedCount = 0;

        if (isset($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $slotItem = trim($item['slot_item'] ?? '');
                if ($slotItem === '') {
                    continue;
                }

                $targetQty = (int)($item['target_qty'] ?? 0);

                $stmtItem->execute([
                    ':slot_item'  => $slotItem,
                    ':target_qty' => $targetQty
                ]);
                $itemId = $pdo->lastInsertId();

                foreach ($days as $day) {
                    $shift1 = (int)($item['sales'][$day]['shift_1'] ?? 0);
                    $shift2 = (int)($item['sales'][$day]['shift_2'] ?? 0);

                    $stmtSales->execute([
                        ':item_id'  => $itemId,
                        ':day_name' => $day,
                        ':shift_1'  => $shift1,
                        ':shift_2'  => $shift2,
                    ]);
                }
                $insertedCount++;
            }
        }

        $pdo->commit();
        if ($insertedCount > 0) {
            $message = "<div class='alert alert-success'>Berhasil menyimpan $insertedCount item ke database!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Tidak ada data yang diisi untuk disimpan.</div>";
        }
    }

    $existingItems = $pdo->query("SELECT * FROM items ORDER BY id ASC")->fetchAll();
    $formData = [];

    foreach ($existingItems as $idx => $item) {
        $itemId = $item['id'];
        $salesRows = $pdo->query("SELECT * FROM sales_reports WHERE item_id = $itemId")->fetchAll();
        
        $salesByDay = [];
        foreach ($salesRows as $sr) {
            $salesByDay[$sr['day_name']] = [
                'shift_1' => $sr['shift_1'],
                'shift_2' => $sr['shift_2']
            ];
        }

        $formData[$idx] = [
            'slot_item'  => $item['slot_item'],
            'target_qty' => $item['target_qty'],
            'sales'      => $salesByDay
        ];
    }

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

$initialRowCount = max(10, count($formData));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Laporan Sales Paling Murah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap" rel="stylesheet">
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

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-main);
            padding: 16px; 
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .container { 
            background-color: var(--card-bg); 
            padding: 20px; 
            border-radius: 16px; 
            box-shadow: var(--shadow); 
            border: 1px solid var(--border);
            max-width: 1400px; 
            margin: 0 auto; 
        }

        h2 { 
            font-family: 'Source Serif 4', Georgia, serif;
            text-align: center; 
            margin-bottom: 20px; 
            font-size: 24px; 
            font-weight: 500;
            color: var(--text-main); 
            letter-spacing: -0.4px;
        }

        .alert { 
            padding: 12px 16px; 
            border-radius: 10px; 
            margin-bottom: 16px; 
            text-align: center; 
            font-weight: 500; 
            font-size: 13px; 
            border: 1px solid var(--border);
        }
        .alert-success { background-color: var(--accent-soft); color: var(--accent); border-color: var(--border-strong); }
        .alert-danger { background-color: #fde8e8; color: #c81e1e; border-color: #f8b4b4; }
        
        .table-responsive { 
            width: 100%; 
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch; 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            position: relative; 
            background: var(--card-bg);
        }
        
        table { border-collapse: separate; border-spacing: 0; width: 100%; min-width: 1100px; font-size: 13px; }
        
        th, td { 
            border-right: 1px solid var(--border); 
            border-bottom: 1px solid var(--border); 
            padding: 8px 6px; 
            text-align: center; 
            vertical-align: middle; 
            background-color: var(--card-bg); 
            color: var(--text-main);
        }
        
        th { 
            background-color: var(--item-hover); 
            color: var(--text-sub); 
            font-weight: 600; 
            font-size: 12px; 
        }
        
        .sticky-col-1 { position: sticky; left: 0; z-index: 2; width: 115px; min-width: 115px; }
        .sticky-col-2 { position: sticky; left: 115px; z-index: 2; width: 70px; min-width: 70px; border-right: 2px solid var(--border-strong); }
        
        th.sticky-col-1, th.sticky-col-2 { background-color: var(--item-hover); z-index: 3; }
        td.sticky-col-1, td.sticky-col-2 { background-color: var(--card-bg); }

        .bg-day { 
            background-color: var(--accent-soft); 
            color: var(--accent); 
            cursor: pointer; 
            transition: opacity 0.2s; 
            font-weight: 600; 
        }
        .bg-day:hover { opacity: 0.85; }
        .bg-total { background-color: var(--item-hover); }
        .bg-summary { background-color: var(--item-hover); font-weight: 600; }
        
        input[type="text"], input[type="number"] {
            font-family: 'Inter', sans-serif;
            background-color: var(--card-bg);
            color: var(--text-main);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 8px;
            font-size: 12px;
            outline: none;
            transition: all 0.15s ease-in-out;
        }
        
        input[type="text"]:focus, input[type="number"]:focus { 
            border-color: var(--accent); 
            box-shadow: 0 0 0 2px var(--accent-soft); 
        }
        
        input[type="text"] { width: 100px; text-align: left; }
        input[type="number"] { width: 52px; text-align: center; -moz-appearance: textfield; }
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        
        input[readonly] { 
            background-color: var(--item-hover); 
            border-color: var(--border); 
            font-weight: 600; 
            color: var(--text-sub); 
        }
        
        .btn-group-bottom {
            display: flex;
            flex-direction: row;
            gap: 12px;
            margin-top: 16px;
        }
        
        .btn { 
            font-family: 'Inter', sans-serif;
            padding: 12px 20px; 
            border: 1px solid transparent; 
            border-radius: 10px; 
            cursor: pointer; 
            font-size: 14px; 
            font-weight: 500; 
            flex: 1; 
            text-align: center; 
            transition: background 0.2s, transform 0.1s;
        }
        
        .btn:active { transform: scale(0.99); }
        
        .btn-submit { background-color: var(--accent); color: #fff; }
        .btn-submit:hover { background-color: var(--accent-hover); }
        
        .btn-add { background-color: transparent; color: var(--text-main); border-color: var(--border-strong); }
        .btn-add:hover { background-color: var(--item-hover); }

        /* Custom Modal Styling */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(41, 38, 27, 0.45);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
            animation: fade 0.15s ease;
        }

        .modal-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 24px;
            border-radius: 20px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            animation: pop 0.18s ease;
        }

        .modal-box h3 {
            font-family: 'Source Serif 4', Georgia, serif;
            margin: 0 0 12px 0;
            color: var(--text-main);
            font-size: 20px;
            font-weight: 500;
        }

        .modal-textarea {
            width: 100%;
            height: 180px;
            font-family: 'Inter', monospace;
            background: var(--item-hover);
            color: var(--text-main);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            font-size: 12px;
            resize: none;
            outline: none;
        }

        @keyframes fade {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pop {
            from { opacity: 0; transform: translateY(6px) scale(0.98); }
            to { opacity: 1; transform: none; }
        }

        @media (max-width: 576px) {
            body { padding: 8px; }
            .container { padding: 12px; border-radius: 12px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Laporan Sales Paling Murah</h2>
    <?= $message ?>

    <form action="" method="POST" id="mainForm">
        <input type="hidden" name="action" id="form_action" value="save">

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky-col-1">Slot Item</th>
                        <th rowspan="2" class="sticky-col-2">Target Qty</th>
                        <?php foreach ($days as $day): ?>
                            <th colspan="3" class="bg-day" onclick="copyDayReport('<?= $day ?>')" title="Klik untuk menyalin laporan hari <?= $day ?>"><?= $day ?> 📋</th>
                        <?php endforeach; ?>
                        <th rowspan="2" class="bg-summary">Total Sales</th>
                        <th rowspan="2" class="bg-summary">ACV Sales</th>
                    </tr>
                    <tr>
                        <?php for ($i = 0; $i < 7; $i++): ?>
                            <th>Shift 1</th>
                            <th>Shift 2</th>
                            <th class="bg-total">Total</th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php for ($row = 0; $row < $initialRowCount; $row++): 
                        $itemVal   = $formData[$row]['slot_item'] ?? '';
                        $targetVal = isset($formData[$row]['target_qty']) && $formData[$row]['target_qty'] > 0 ? $formData[$row]['target_qty'] : '';
                    ?>
                    <tr id="row_<?= $row ?>">
                        <td class="sticky-col-1">
                            <input type="text" name="items[<?= $row ?>][slot_item]" id="slot_item_<?= $row ?>" value="<?= htmlspecialchars($itemVal) ?>" placeholder="Slot <?= $row + 1 ?>">
                        </td>
                        <td class="sticky-col-2">
                            <input type="number" name="items[<?= $row ?>][target_qty]" class="target-qty" id="target_qty_<?= $row ?>" value="<?= $targetVal ?>" placeholder="0" min="0" oninput="calculateRow(<?= $row ?>)">
                        </td>
                        
                        <?php foreach ($days as $day): 
                            $s1 = isset($formData[$row]['sales'][$day]['shift_1']) && $formData[$row]['sales'][$day]['shift_1'] > 0 ? $formData[$row]['sales'][$day]['shift_1'] : '';
                            $s2 = isset($formData[$row]['sales'][$day]['shift_2']) && $formData[$row]['sales'][$day]['shift_2'] > 0 ? $formData[$row]['sales'][$day]['shift_2'] : '';
                        ?>
                            <td><input type="number" name="items[<?= $row ?>][sales][<?= $day ?>][shift_1]" class="shift-input row-<?= $row ?> day-<?= $day ?>" id="shift1_<?= $row ?>_<?= $day ?>" value="<?= $s1 ?>" placeholder="0" min="0" oninput="calculateRow(<?= $row ?>)"></td>
                            <td><input type="number" name="items[<?= $row ?>][sales][<?= $day ?>][shift_2]" class="shift-input row-<?= $row ?> day-<?= $day ?>" id="shift2_<?= $row ?>_<?= $day ?>" value="<?= $s2 ?>" placeholder="0" min="0" oninput="calculateRow(<?= $row ?>)"></td>
                            <td class="bg-total"><input type="number" id="total_<?= $row ?>_<?= $day ?>" placeholder="0" readonly></td>
                        <?php endforeach; ?>

                        <td class="bg-summary"><input type="number" id="grand_total_<?= $row ?>" placeholder="0" readonly></td>
                        <td class="bg-summary"><input type="text" id="acv_sales_<?= $row ?>" placeholder="0%" style="width: 55px;" readonly></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="btn-group-bottom">
            <button type="button" onclick="addNewRow()" class="btn btn-add">+ Tambah Baris</button>
            <button type="submit" onclick="setAction('save')" class="btn btn-submit">Simpan Data</button>
        </div>
    </form>
</div>

<!-- Custom Modal Laporan Salin Otomatis -->
<div class="modal-overlay" id="modalCopy">
    <div class="modal-box">
        <h3 id="modalTitle">Hasil Laporan</h3>
        <textarea id="modalTextarea" class="modal-textarea" readonly></textarea>
    </div>
</div>

<script>
let totalRows = <?= $initialRowCount ?>;
const days = ['Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Senin', 'Selasa', 'Rabu'];

function calculateRow(rowIndex) {
    let grandTotal = 0;

    days.forEach(day => {
        let inputs = document.querySelectorAll('.row-' + rowIndex + '.day-' + day);
        let dayTotal = 0;
        inputs.forEach(input => {
            dayTotal += parseInt(input.value) || 0;
        });
        let totalInput = document.getElementById('total_' + rowIndex + '_' + day);
        if (totalInput) totalInput.value = dayTotal > 0 ? dayTotal : '';
        grandTotal += dayTotal;
    });

    let grandTotalInput = document.getElementById('grand_total_' + rowIndex);
    if (grandTotalInput) grandTotalInput.value = grandTotal > 0 ? grandTotal : '';

    let targetQtyInput = document.getElementById('target_qty_' + rowIndex);
    let acvInput = document.getElementById('acv_sales_' + rowIndex);

    if (targetQtyInput && acvInput) {
        let targetQty = parseInt(targetQtyInput.value) || 0;
        if (targetQty > 0) {
            let acvSales = ((grandTotal / targetQty) * 100).toFixed(1);
            acvInput.value = acvSales + '%';
        } else {
            acvInput.value = '';
        }
    }
}

function addNewRow() {
    let tbody = document.getElementById('tableBody');
    let row = totalRows;
    let tr = document.createElement('tr');
    tr.id = 'row_' + row;

    let html = `
        <td class="sticky-col-1">
            <input type="text" name="items[${row}][slot_item]" id="slot_item_${row}" placeholder="Slot ${row + 1}">
        </td>
        <td class="sticky-col-2">
            <input type="number" name="items[${row}][target_qty]" class="target-qty" id="target_qty_${row}" placeholder="0" min="0" oninput="calculateRow(${row})">
        </td>
    `;

    days.forEach(day => {
        html += `
            <td><input type="number" name="items[${row}][sales][${day}][shift_1]" class="shift-input row-${row} day-${day}" id="shift1_${row}_${day}" placeholder="0" min="0" oninput="calculateRow(${row})"></td>
            <td><input type="number" name="items[${row}][sales][${day}][shift_2]" class="shift-input row-${row} day-${day}" id="shift2_${row}_${day}" placeholder="0" min="0" oninput="calculateRow(${row})"></td>
            <td class="bg-total"><input type="number" id="total_${row}_${day}" placeholder="0" readonly></td>
        `;
    });

    html += `
        <td class="bg-summary"><input type="number" id="grand_total_${row}" placeholder="0" readonly></td>
        <td class="bg-summary"><input type="text" id="acv_sales_${row}" placeholder="0%" style="width: 55px;" readonly></td>
    `;

    tr.innerHTML = html;
    tbody.appendChild(tr);
    totalRows++;
}

function copyDayReport(day) {
    let text = "*F8YF - Grand Mutiara*\n";
    let validItemIndex = 1;
    let slotCount = 0;

    for (let row = 0; row < totalRows; row++) {
        let slotItemEl = document.getElementById('slot_item_' + row);
        if (!slotItemEl) continue;

        let slotItem = slotItemEl.value.trim();
        if (slotItem === '') continue;

        let targetQty = parseInt(document.getElementById('target_qty_' + row).value) || 0;
        let s1 = parseInt(document.getElementById('shift1_' + row + '_' + day).value) || 0;
        let s2 = parseInt(document.getElementById('shift2_' + row + '_' + day).value) || 0;
        let dayTotal = parseInt(document.getElementById('total_' + row + '_' + day).value) || 0;
        let grandTotal = parseInt(document.getElementById('grand_total_' + row).value) || 0;
        let acvVal = 0;

        if (targetQty > 0) {
            acvVal = Math.round((grandTotal / targetQty) * 100);
        }

        let isTargetReached = acvVal >= 100;
        if (isTargetReached) {
            slotCount++;
        }

        text += `${validItemIndex}. ${slotItem}_${targetQty}_${s1}_${s2}_${dayTotal}_${grandTotal}_${acvVal}%${isTargetReached ? '✅' : ''}\n`;
        validItemIndex++;
    }

    text += `Acv Slot : ${slotCount}`;

    // Salin otomatis ke clipboard
    navigator.clipboard.writeText(text).then(() => {
        // Tampilkan popup sejenak lalu otomatis tutup setelah 2 detik
        document.getElementById('modalTitle').innerText = 'Laporan Hari ' + day + ' ( Berhasil Disalin! )';
        document.getElementById('modalTextarea').value = text;
        let modal = document.getElementById('modalCopy');
        modal.classList.add('active');

        setTimeout(() => {
            modal.classList.remove('active');
        }, 2000);
    }).catch(err => {
        alert("Gagal menyalin otomatis: " + err);
    });
}

// Tutup modal jika klik di luar area kotak modal
document.getElementById('modalCopy').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.remove('active');
    }
});

function setAction(actionName) {
    document.getElementById('form_action').value = actionName;
}

window.onload = function() {
    for (let i = 0; i < totalRows; i++) {
        calculateRow(i);
    }
};
</script>

</body>
</html>