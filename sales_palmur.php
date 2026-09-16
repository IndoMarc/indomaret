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

    if (isset($_POST['action']) && $_POST['action'] === 'clear') {
        $pdo->beginTransaction();
        $pdo->exec("DELETE FROM sales_reports");
        $pdo->exec("DELETE FROM items");
        $pdo->commit();
        $message = '<div class="alert alert-success">Seluruh isi data tabel database berhasil dihapus!</div>';
    }

    if (isset($_POST['action']) && $_POST['action'] === 'save') {
        $pdo->beginTransaction();

        $pdo->exec("DELETE FROM sales_reports");
        $pdo->exec("DELETE FROM items");

        $stmtItem = $pdo->prepare("INSERT INTO items (slot_item, target_qty) VALUES (:slot_item, :target_qty)");
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            background-color: #f1f5f9; 
            color: #0f172a;
            margin: 0; 
            padding: 12px; 
        }
        .container { 
            background-color: #ffffff; 
            padding: 12px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            max-width: 100%; 
            margin: 0 auto; 
        }
        h2 { 
            text-align: center; 
            margin-top: 4px; 
            margin-bottom: 16px; 
            font-size: 1.35rem; 
            font-weight: 700;
            color: #1e293b; 
        }
        .alert { 
            padding: 10px 14px; 
            border-radius: 8px; 
            margin-bottom: 12px; 
            text-align: center; 
            font-weight: 600; 
            font-size: 0.875rem; 
        }
        .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .table-responsive { 
            width: 100%; 
            overflow-x: auto; 
            -webkit-overflow-scrolling: touch; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            position: relative; 
            box-shadow: inset 0 0 0 1px #e2e8f0;
        }
        table { border-collapse: separate; border-spacing: 0; width: 100%; min-width: 1100px; font-size: 13px; }
        th, td { border-right: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; padding: 6px 4px; text-align: center; vertical-align: middle; background-color: #fff; }
        th { background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 12px; }
        
        .sticky-col-1 { position: sticky; left: 0; z-index: 2; width: 115px; min-width: 115px; }
        .sticky-col-2 { position: sticky; left: 115px; z-index: 2; width: 70px; min-width: 70px; border-right: 2px solid #cbd5e1; }
        
        th.sticky-col-1, th.sticky-col-2 { background-color: #f8fafc; z-index: 3; }
        td.sticky-col-1, td.sticky-col-2 { background-color: #ffffff; }

        .bg-day { background-color: #e0f2fe; color: #0369a1; cursor: pointer; transition: background-color 0.2s; font-weight: 600; }
        .bg-day:hover { background-color: #bae6fd; }
        .bg-total { background-color: #f0fdf4; }
        .bg-summary { background-color: #fefce8; }
        
        input[type="text"], input[type="number"] {
            font-family: 'Inter', sans-serif;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 4px;
            font-size: 12px;
            outline: none;
            transition: all 0.15s ease-in-out;
        }
        input[type="text"]:focus, input[type="number"]:focus { 
            border-color: #3b82f6; 
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); 
        }
        input[type="text"] { width: 100px; text-align: left; }
        input[type="number"] { width: 48px; text-align: center; -moz-appearance: textfield; }
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[readonly] { background-color: #f1f5f9; border-color: #cbd5e1; font-weight: 600; color: #334155; }
        
        .btn-group-top { 
            display: flex; 
            flex-direction: row; 
            gap: 12px; 
            margin-bottom: 14px; 
        }
        .btn-group-bottom {
            display: flex;
            margin-top: 10px;
        }
        .btn { 
            font-family: 'Inter', sans-serif;
            padding: 10px 18px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 14px; 
            font-weight: 600; 
            flex: 1; 
            text-align: center; 
            transition: background-color 0.2s, transform 0.1s;
        }
        .btn:active { transform: scale(0.99); }
        .btn-submit { background-color: #16a34a; color: #fff; }
        .btn-submit:hover { background-color: #15803d; }
        .btn-clear { background-color: #dc2626; color: #fff; }
        .btn-clear:hover { background-color: #b91c1c; }
        .btn-add { background-color: #0284c7; color: #fff; }
        .btn-add:hover { background-color: #0369a1; }

        @media (min-width: 576px) {
            body { padding: 16px; }
            .container { padding: 16px; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Laporan Sales Paling Murah</h2>
    <?= $message ?>

    <form action="" method="POST" id="mainForm">
        <input type="hidden" name="action" id="form_action" value="save">

        <div class="btn-group-top">
            <button type="submit" onclick="setAction('save')" class="btn btn-submit">Simpan Data</button>
            <button type="button" onclick="confirmClear()" class="btn btn-clear">Hapus Semua Data</button>
        </div>

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
                        <td class="bg-summary"><input type="text" id="acv_sales_<?= $row ?>" placeholder="0%" style="width: 50px;" readonly></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class="btn-group-bottom">
            <button type="button" onclick="addNewRow()" class="btn btn-add">+ Tambah Baris</button>
        </div>
    </form>
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
        <td class="bg-summary"><input type="text" id="acv_sales_${row}" placeholder="0%" style="width: 50px;" readonly></td>
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

    navigator.clipboard.writeText(text).then(() => {
        alert("Laporan hari " + day + " berhasil disalin ke clipboard!\n\n" + text);
    }).catch(err => {
        alert("Gagal menyalin otomatis: " + err);
    });
}

function setAction(actionName) {
    document.getElementById('form_action').value = actionName;
}

function confirmClear() {
    if (confirm("Apakah Anda yakin ingin menghapus seluruh isi data pada database?")) {
        setAction('clear');
        document.getElementById('mainForm').submit();
    }
}

window.onload = function() {
    for (let i = 0; i < totalRows; i++) {
        calculateRow(i);
    }
};
</script>

</body>
</html>