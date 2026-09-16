<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="referrer" content="no-referrer">
    <title>Scan ITT PJR</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

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
            padding: 20px 0;
            min-height: 100vh;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        .page-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .container {
            padding: 0 15px;
            max-width: 1440px;
            margin: 0 auto;
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

        .search-textarea:focus, .filter-select:focus {
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

    <h1 class="page-title">Scan ITT PJR</h1>

    <?php 
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
    ?>

    <!-- Modal Zoom Gambar -->
    <div class="img-modal" id="imgModal" onclick="closeImageModal()">
        <button class="img-modal-close" onclick="closeImageModal()"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
        <img class="img-modal-content" id="imgModalSrc" src="" alt="Zoom Gambar Produk" referrerpolicy="no-referrer">
    </div>

    <div class="container">
        
        <form method="GET" action="" class="search-container" id="form_scan_itt">
            <textarea 
                name="keyword" 
                id="input_scan_itt"
                class="search-textarea" 
                placeholder="Ketik PLU / Barcode ( pisahkan dengan spasi atau enter )..."
                autocomplete="off"
            ><?= htmlspecialchars($keyword) ?></textarea>

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
                <a href="?" class="btn-reset">Reset</a>
            </div>
        </form>

        <div class="result-area">
            <?php
            $is_scan_itt_submitted = !empty($keyword) || !empty($modis_filter) || !empty($dari_shelfing) || !empty($sampai_shelfing);
            if ($is_scan_itt_submitted) {
                render_results($produk_list, $keyword, $nama_file, $current_p, $modis_filter, $dari_shelfing, $sampai_shelfing);
            } else {
                echo "<p class='info'>Silakan isi kata kunci atau pilih filter lalu tekan Tampilkan Data.</p>";
            }
            ?>
        </div>

    </div>

    <?php
    function render_results($produk_list, $keyword, $nama_file, $current_p = 1, $modis_filter = '', $dari_shelfing = '', $sampai_shelfing = '') {
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

            $per_page = 1;
            $total_items = count($hasil_pencarian);
            $total_pages = ceil($total_items / $per_page);
            $current_p = min(max(1, $current_p), $total_pages);
            
            $offset = ($current_p - 1) * $per_page;
            $paged_items = array_slice($hasil_pencarian, $offset, $per_page);

            echo '<div class="pop-overlay show" id="popOverlayScan">';
            echo '<div class="pop-card-content">';
            echo '<button class="pop-close-btn" onclick="closePopOverlay()"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>';
            echo '<div class="pop-card-body">';

            foreach ($paged_items as $index => $produk) {
                $plu = $produk['plu'] ?? '';
                $img_url = "https://cdn-klik.klikindomaret.com/klik-catalog/product/{$plu}_1.jpg";
                $card_id = "card_" . $index;
                ?>
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
                <?php
            }

            echo '</div>';

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

            echo '</div>';
            echo '</div>';
        } else {
            echo "<p class='info'>Data produk tidak ditemukan berdasarkan kriteria pencarian/filter.</p>";
        }
    }
    ?>

    <script>
        function renderBarcodes() {
            const svgElements = document.querySelectorAll('.barcode-svg');
            svgElements.forEach(svg => {
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

        const imgModal = document.getElementById('imgModal');
        const imgModalSrc = document.getElementById('imgModalSrc');

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

        window.addEventListener('DOMContentLoaded', () => {
            renderBarcodes();

            const activeInput = document.getElementById('input_scan_itt');
            if (activeInput) {
                activeInput.focus();
                if (activeInput.value) {
                    activeInput.select();
                }
            }
        });
    </script>

</body>
</html>