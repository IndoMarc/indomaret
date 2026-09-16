<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="referrer" content="no-referrer">
    <title>Cek Harga Produk</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/html5-qrcode"></script>

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
            padding-top: 20px;
            padding-bottom: 20px;
            min-height: 100vh;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        .page-title {
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .container {
            padding: 15px;
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

        .search-input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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
    $keyword = trim($_GET['keyword'] ?? '');
    $current_p = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
    $nama_file = 'data_produk.json';
    $produk_list = [];

    if (file_exists($nama_file)) {
        $json_data = file_get_contents($nama_file);
        $produk_list = json_decode($json_data, true) ?? [];
    }
    ?>

    <h1 class="page-title">Cek Harga Produk</h1>

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
        <form method="GET" action="" class="search-container search-form" id="form_cek_harga">
            <div class="search-input-group">
                <input 
                    type="text" 
                    name="keyword" 
                    id="input_cek_harga"
                    class="search-input" 
                    placeholder="Ketik PLU, Barcode, Nama Produk..." 
                    autocomplete="off"
                    value="<?= htmlspecialchars($keyword) ?>"
                >
                <button type="button" class="btn-scan-camera" onclick="openScannerModal('form_cek_harga', 'input_cek_harga')" title="Scan via Kamera">
                    <svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                </button>
            </div>
        </form>

        <div class="result-area">
            <?php
            if (!empty($keyword)) {
                render_results($produk_list, $keyword, $nama_file, $current_p);
            } else {
                echo "<p class='info'>Silakan scan barcode atau masukkan kata kunci lalu tekan Enter untuk menampilkan data.</p>";
            }
            ?>
        </div>
    </div>

    <?php
    function render_results($produk_list, $keyword, $nama_file, $current_p = 1) {
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

            if ($cocok_keyword) {
                $filtered_results[] = $item;
            }
        }

        if (!empty($filtered_results)) {
            $hasil_pencarian = array_values($filtered_results);

            $per_page = 10;
            $total_items = count($hasil_pencarian);
            $total_pages = ceil($total_items / $per_page);
            $current_p = min(max(1, $current_p), $total_pages);
            
            $offset = ($current_p - 1) * $per_page;
            $paged_items = array_slice($hasil_pencarian, $offset, $per_page);

            echo '<div class="card-grid">';

            foreach ($paged_items as $index => $produk) {
                $plu = $produk['plu'] ?? '';
                $img_url = "https://cdn-klik.klikindomaret.com/klik-catalog/product/{$plu}_1.jpg";
                $has_promo = isset($produk['harga_promo']) && $produk['harga_promo'] !== null;
                $card_id = "card_" . $index;
                ?>
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
        } else {
            echo "<p class='info'>Data produk tidak ditemukan berdasarkan kriteria pencarian/filter.</p>";
        }
    }
    ?>

    <script>
        const imgModal = document.getElementById('imgModal');
        const imgModalSrc = document.getElementById('imgModalSrc');
        const scannerModal = document.getElementById('scannerModal');

        let html5QrcodeScanner = null;
        let activeTargetFormId = null;
        let activeTargetInputId = null;

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

        window.addEventListener('DOMContentLoaded', () => {
            const activeInput = document.getElementById('input_cek_harga');
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