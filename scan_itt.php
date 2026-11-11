<?php
/**
 * Pencarian Data Produk - Scan ITT PJR
 */

const JSON_FILE = __DIR__ . '/data_produk.json';
const PER_PAGE  = 1;

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/**
 * Helper untuk mengubah data string berpemisah koma atau array menjadi array bersih
 */
function parseFieldToArray($val): array {
    if (is_array($val)) {
        $res = [];
        foreach ($val as $item) {
            foreach (explode(',', (string)$item) as $sub) {
                $trimmed = trim($sub);
                if ($trimmed !== '') $res[] = $trimmed;
            }
        }
        return $res;
    }
    
    $res = [];
    foreach (explode(',', (string)$val) as $sub) {
        $trimmed = trim($sub);
        if ($trimmed !== '') $res[] = $trimmed;
    }
    return $res;
}

$q            = trim($_GET['q'] ?? '');
$filter_rak   = trim($_GET['rak'] ?? '');
$filter_from  = trim($_GET['from'] ?? '');
$filter_to    = trim($_GET['to'] ?? '');
$page         = max(1, (int)($_GET['page'] ?? 1));

$error = null;
$hasil = [];
$total = 0;
$all_rak = [];
$all_shelf = [];

// Baca data JSON untuk dropdown dan pencarian
if (is_file(JSON_FILE)) {
    $raw_data = json_decode(file_get_contents(JSON_FILE), true);
    if (is_array($raw_data)) {
        foreach ($raw_data as $row) {
            foreach (parseFieldToArray($row['rak'] ?? '') as $rv) {
                $all_rak[$rv] = true;
            }
            foreach (parseFieldToArray($row['noshelf'] ?? '') as $sv) {
                $all_shelf[$sv] = true;
            }
        }
        ksort($all_rak);
        uksort($all_shelf, 'strnatcmp');
    }
}

// Cek apakah ada aksi pencarian/filter yang dikirim
$is_searched = ($q !== '' || $filter_rak !== '' || $filter_from !== '' || $filter_to !== '');

if ($is_searched) {
    if (!is_file(JSON_FILE)) {
        $error = 'File JSON tidak ditemukan: ' . basename(JSON_FILE);
    } else {
        $data = $raw_data ?? [];
        if (!is_array($data)) {
            $error = 'File JSON tidak valid.';
        } else {
            // Pisahkan kata kunci berdasarkan spasi atau enter (newline)
            $keywords = preg_split('/[\s\n\r]+/', mb_strtolower($q));
            $keywords = array_filter($keywords, function($k) { return $k !== ''; });

            foreach ($data as $row) {
                // 1. Filter Text Pencarian
                $match_text = true;
                if (!empty($keywords)) {
                    $haystack = mb_strtolower(($row['plu'] ?? '') . ' ' . ($row['barcode'] ?? '') . ' ' . ($row['deskripsi'] ?? ''));
                    $matched_any = false;
                    foreach ($keywords as $kw) {
                        if (mb_strpos($haystack, $kw) !== false) {
                            $matched_any = true;
                            break;
                        }
                    }
                    $match_text = $matched_any;
                }

                // 2. Filter Dropdown Rak & Shelfing
                $r_arr = parseFieldToArray($row['rak'] ?? '');
                $s_arr = parseFieldToArray($row['noshelf'] ?? '');

                $match_rak = true;
                if ($filter_rak !== '') {
                    $match_rak = in_array($filter_rak, $r_arr);
                }

                $match_shelf = true;
                if ($filter_from !== '' || $filter_to !== '') {
                    $match_shelf = false;
                    foreach ($s_arr as $s) {
                        if ($s !== '') {
                            $ok_from = ($filter_from === '' || strnatcmp($s, $filter_from) >= 0);
                            $ok_to   = ($filter_to === '' || strnatcmp($s, $filter_to) <= 0);
                            if ($ok_from && $ok_to) {
                                $match_shelf = true;
                                break;
                            }
                        }
                    }
                }

                if ($match_text && $match_rak && $match_shelf) {
                    $hasil[] = $row;
                }
            }

            // Urutkan ascending berdasarkan rak -> noshelf -> kirikanan pertama
            usort($hasil, function($a, $b) {
                $rA = parseFieldToArray($a['rak'] ?? '');
                $rB = parseFieldToArray($b['rak'] ?? '');
                $rakA = $rA[0] ?? '';
                $rakB = $rB[0] ?? '';
                if ($rakA !== $rakB) return strnatcmp($rakA, $rakB);

                $sA = parseFieldToArray($a['noshelf'] ?? '');
                $sB = parseFieldToArray($b['noshelf'] ?? '');
                $shelfA = $sA[0] ?? '';
                $shelfB = $sB[0] ?? '';
                if ($shelfA !== $shelfB) return strnatcmp($shelfA, $shelfB);

                $kA = parseFieldToArray($a['kirikanan'] ?? '');
                $kB = parseFieldToArray($b['kirikanan'] ?? '');
                $kkA = $kA[0] ?? '';
                $kkB = $kB[0] ?? '';
                return strnatcmp($kkA, $kkB);
            });

            $total = count($hasil);
        }
    }
}

$totalPages = max(1, (int)ceil($total / PER_PAGE));
$page       = min($page, $totalPages);
$tampil     = array_slice($hasil, ($page - 1) * PER_PAGE, PER_PAGE);

function url(array $over = []): string {
    global $q, $filter_rak, $filter_from, $filter_to, $page;
    return '?' . http_build_query(array_merge([
        'q' => $q, 
        'rak' => $filter_rak, 
        'from' => $filter_from, 
        'to' => $filter_to, 
        'page' => $page
    ], $over));
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Referrer Policy agar gambar dari CDN eksternal dapat dimuat dengan baik -->
<meta name="referrer" content="no-referrer">
<title>Scan ITT PJR</title>
<!-- Google Fonts (Inter & Source Serif 4) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap" rel="stylesheet">
<!-- Pustaka JsBarcode untuk merender barcode format Code 128 -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
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
    color: var(--text-main);
    min-height: 100vh;
    padding: 20px 16px;
    -webkit-font-smoothing: antialiased;
}

.wrap {
    max-width: 600px;
    margin: 0 auto;
    padding: 0 0 64px;
}

header {
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
    width: 100%;
}

h1 {
    font-family: 'Source Serif 4', Georgia, serif;
    font-size: 24px;
    font-weight: 500;
    letter-spacing: -0.4px;
    line-height: 1.25;
    color: var(--text-main);
}

form.search-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 16px;
    box-shadow: var(--shadow);
}

textarea.search-input {
    width: 100%;
    border: 1px solid var(--border-strong);
    border-radius: 10px;
    padding: 11px 14px;
    background: var(--item-bg);
    color: var(--text-main);
    font: inherit;
    outline: 0;
    resize: vertical;
    min-height: 70px;
}
textarea.search-input:focus {
    border-color: var(--accent);
}

.dropdown-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.dropdown-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

select.filter-select {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid var(--border-strong);
    border-radius: 10px;
    background: var(--item-bg);
    color: var(--text-main);
    font: inherit;
    outline: 0;
}
select.filter-select:focus {
    border-color: var(--accent);
}

/* Tombol Reset di Kiri, Cari di Kanan */
.btn-row {
    display: flex;
    gap: 10px;
    margin-top: 4px;
}
a.btn-reset {
    flex: 1;
    background: transparent;
    color: var(--text-main);
    border: 1px solid var(--border-strong);
    border-radius: 10px;
    padding: 11px 20px;
    font: inherit;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s ease, border-color 0.2s ease;
}
a.btn-reset:hover {
    background: var(--item-hover);
}
button.btn-submit {
    flex: 2;
    background: var(--accent);
    color: #ffffff;
    border: 1px solid transparent;
    border-radius: 10px;
    padding: 11px 20px;
    font: inherit;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.2s ease;
}
button.btn-submit:hover {
    background: var(--accent-hover);
}

.hint {
    text-align: center;
    color: var(--text-sub);
    font-size: 14px;
    margin-top: 16px;
}

/* Styling Modal Pop-up Melayang (Overlay) */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(41, 38, 27, 0.45);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    padding: 16px;
    overflow-y: auto;
}

/* Container Pop-up: Navigasi di Sebelah Kiri Card */
.modal-content-wrapper {
    display: flex;
    align-items: center;
    gap: 16px;
    max-width: 520px;
    width: 100%;
    position: relative;
}

/* Panel Navigasi Vertikal di Kiri (Next di Atas, Info di Tengah, Prev di Bawah) */
nav.side-pager {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 10px;
    min-width: 50px;
    flex-shrink: 0;
}
nav.side-pager a, nav.side-pager span.page-info, nav.side-pager span.off {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid var(--border);
    background: var(--card-bg);
    color: var(--text-main);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 600;
    box-shadow: var(--shadow);
}
nav.side-pager a:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--item-hover);
}
nav.side-pager .off {
    opacity: 0.3;
    cursor: not-allowed;
}
nav.side-pager span.page-info {
    font-size: 10px;
    font-weight: 600;
    color: var(--text-sub);
    line-height: 1.1;
    text-align: center;
    border-radius: 12px;
    background: var(--item-bg);
    padding: 4px;
    width: 44px;
    height: auto;
    border: 1px solid var(--border);
}

/* Cardview di dalam Pop-up */
.card {
    flex: 1;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 28px 24px 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    position: relative;
}

/* Tombol Close Pop-up (X) */
.btn-close-modal {
    position: absolute;
    top: 14px;
    right: 16px;
    background: var(--item-bg);
    border: 1px solid var(--border);
    color: var(--text-main);
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}
.btn-close-modal:hover {
    background: var(--accent-soft);
    color: var(--accent);
    border-color: var(--accent);
}

.card-content {
    display: flex;
    flex-direction: column;
    gap: 16px;
    align-items: center;
    margin-top: 10px;
}

/* Gambar Produk dari KlikIndomaret */
.product-img {
    max-width: 140px;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    background: #ffffff;
    padding: 4px;
    border: 1px solid var(--border);
}

.val-plu {
    font-size: 10px;
    color: var(--text-sub);
}

.val-deskripsi {
    font-family: 'Source Serif 4', Georgia, serif;
    font-size: 15px;
    font-weight: 500;
    line-height: 1.3;
    margin: 2px 0;
    color: var(--text-main);
}

/* Barcode styling */
.barcode-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: center;
    margin: 4px 0;
    width: 100%;
}
.barcode-item {
    background: #ffffff !important;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    display: inline-block;
}
.barcode-item svg {
    display: block;
    max-width: 100%;
    height: auto;
}
.extra-barcodes {
    display: none;
    flex-direction: column;
    gap: 8px;
    align-items: center;
    width: 100%;
}
.btn-toggle-barcode {
    background: var(--item-bg);
    border: 1px solid var(--border);
    color: var(--text-main);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 10px;
    cursor: pointer;
    margin-top: 4px;
    font-weight: 500;
}
.btn-toggle-barcode:hover {
    background: var(--item-hover);
    border-color: var(--border-strong);
}

/* Layout Rak (Atas), No Shelf & Kiri Kanan (Sejajar di bawah) */
.location-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 4px;
    width: 100%;
}
.loc-box-top {
    background: var(--item-bg);
    border: 1px solid var(--border);
    padding: 10px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.loc-row-bottom {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.loc-box-sub {
    background: var(--item-bg);
    border: 1px solid var(--border);
    padding: 10px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.loc-lbl {
    font-size: 10px;
    color: var(--text-sub);
    text-transform: uppercase;
    margin-bottom: 2px;
}
.loc-val {
    font-size: 10px;
    font-weight: 600;
    color: var(--text-main);
}

.empty {
    text-align: center;
    color: var(--text-sub);
    padding: 40px 0;
}
.error {
    max-width: 100%;
    margin: 24px auto;
    background: #fbeae5;
    color: #b3402a;
    padding: 12px 16px;
    border-radius: 12px;
    text-align: center;
}
</style>
</head>
<body>
<div class="wrap">
  <header>
    <h1>Scan ITT PJR</h1>
  </header>

  <form class="search-form" method="get" action="">
    <textarea class="search-input" name="q" placeholder="Ketik PLU, Barcode, atau Nama Produk ( Dipisah spasi atau enter )..." autofocus><?= e($q) ?></textarea>
    
    <div class="dropdown-group">
      <!-- Dropdown 1: Pilih Modis (Rak) -->
      <div>
        <select class="filter-select" name="rak">
          <option value="">-- Pilih Modis --</option>
          <?php foreach (array_keys($all_rak) as $rk): ?>
            <option value="<?= e($rk) ?>" <?= $filter_rak === $rk ? 'selected' : '' ?>><?= e($rk) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Dropdown 2 & 3: Dari Shelfing dan Sampai Shelfing -->
      <div class="dropdown-row">
        <div>
          <select class="filter-select" name="from">
            <option value="">-- Dari Shelfing --</option>
            <?php foreach (array_keys($all_shelf) as $sf): ?>
              <option value="<?= e($sf) ?>" <?= $filter_from === $sf ? 'selected' : '' ?>><?= e($sf) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <select class="filter-select" name="to">
            <option value="">-- Sampai Shelfing --</option>
            <?php foreach (array_keys($all_shelf) as $sf): ?>
              <option value="<?= e($sf) ?>" <?= $filter_to === $sf ? 'selected' : '' ?>><?= e($sf) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <!-- Tombol Reset di Kiri, Cari di Kanan -->
    <div class="btn-row">
      <a href="?" class="btn-reset">Reset</a>
      <button type="submit" class="btn-submit">Cari</button>
    </div>
  </form>
  
  <?php if (!$is_searched): ?>
    <p class="hint">Silakan masukkan kata kunci pencarian atau pilih filter di atas lalu klik <b>Cari</b>.</p>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>

  <?php elseif ($is_searched): ?>
    <?php if ($total === 0): ?>
      <div class="empty">Tidak ada data produk yang cocok dengan kriteria pencarian/filter tersebut.</div>
    <?php else: ?>
      
      <!-- Pop-up Melayang (Modal Overlay) -->
      <div class="modal-overlay" id="modalOverlay">
        <div class="modal-content-wrapper">
          
          <!-- Panel Navigasi Vertikal di Kiri (Next [»] di Atas, Info Halaman di Tengah, Prev [«] di Bawah) -->
          <nav class="side-pager" aria-label="Navigasi halaman">
            <?php if ($page < $totalPages): ?>
              <a id="next-btn" class="side-nav-btn" href="<?= e(url(['page' => $page + 1])) ?>" title="Next">»</a>
            <?php else: ?>
              <span class="off">»</span>
            <?php endif; ?>

            <span class="page-info"><?= $page ?><br>dari<br><?= $totalPages ?></span>

            <?php if ($page > 1): ?>
              <a id="prev-btn" class="side-nav-btn" href="<?= e(url(['page' => $page - 1])) ?>" title="Prev">«</a>
            <?php else: ?>
              <span class="off">«</span>
            <?php endif; ?>
          </nav>

          <!-- Cardview Utama di Tengah Pop-up -->
          <?php foreach ($tampil as $r): ?>
            <article class="card">
              <!-- Tombol Close Modal (X) - Menyembunyikan pop-up tanpa mereset form pencarian -->
              <button type="button" class="btn-close-modal" onclick="closeModal()" title="Tutup">✕</button>

              <div class="card-content">
                <!-- Gambar Produk KlikIndomaret berdasarkan PLU -->
                <?php $curr_plu = trim((string)($r['plu'] ?? '')); ?>
                <?php if (!empty($curr_plu)): ?>
                  <img class="product-img" 
                       src="https://cdn-klik.klikindomaret.com/klik-catalog/product/<?= e($curr_plu) ?>_1.jpg" 
                       alt="Produk <?= e($curr_plu) ?>" 
                       onerror="this.style.display='none';">
                <?php endif; ?>

                <div class="val-plu"><?= e($r['plu'] ?? '') ?></div>
                <div class="val-deskripsi"><?= e($r['deskripsi'] ?? '') ?></div>

                <!-- Barcode -->
                <?php 
                  $bc_list = parseFieldToArray($r['barcode'] ?? '');
                ?>
                <?php if (!empty($bc_list)): ?>
                  <div class="barcode-wrapper">
                    <div class="barcode-item">
                      <svg class="barcode-svg" 
                           jsbarcode-value="<?= e($bc_list[0]) ?>"
                           jsbarcode-format="CODE128"
                           jsbarcode-height="45"
                           jsbarcode-fontSize="13"
                           jsbarcode-background="#ffffff"
                           jsbarcode-lineColor="#1f1e1d">
                      </svg>
                    </div>

                    <?php if (count($bc_list) > 1): ?>
                      <div class="extra-barcodes" id="extraBarcodes-<?= e($r['plu'] ?? '') ?>">
                        <?php for($i = 1; $i < count($bc_list); $i++): ?>
                          <div class="barcode-item">
                            <svg class="barcode-svg" 
                                 jsbarcode-value="<?= e($bc_list[$i]) ?>"
                                 jsbarcode-format="CODE128"
                                 jsbarcode-height="45"
                                 jsbarcode-fontSize="13"
                                 jsbarcode-background="#ffffff"
                                 jsbarcode-lineColor="#1f1e1d">
                            </svg>
                          </div>
                        <?php endfor; ?>
                      </div>
                      <button type="button" class="btn-toggle-barcode" onclick="toggleBarcodes('<?= e($r['plu'] ?? '') ?>', this)">
                        Tampilkan Barcode Lainnya (<?= count($bc_list) - 1 ?>)
                      </button>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>

                <!-- Layout Rak, No Shelf & Kiri Kanan -->
                <?php
                  $all_raks   = parseFieldToArray($r['rak'] ?? '');
                  $all_shelfs = parseFieldToArray($r['noshelf'] ?? '');
                  $all_kks    = parseFieldToArray($r['kirikanan'] ?? '');

                  $filtered_rows = [];
                  $maxCount = max(count($all_raks), count($all_shelfs), count($all_kks));

                  for ($i = 0; $i < $maxCount; $i++) {
                      $rk = $all_raks[$i] ?? ($all_raks[0] ?? '-');
                      $sf = $all_shelfs[$i] ?? ($all_shelfs[0] ?? '-');
                      $kk = $all_kks[$i] ?? ($all_kks[0] ?? '-');

                      if ($filter_rak !== '' && strtolower($rk) !== strtolower($filter_rak)) {
                          continue;
                      }
                      if ($filter_from !== '' && strnatcmp($sf, $filter_from) < 0) {
                          continue;
                      }
                      if ($filter_to !== '' && strnatcmp($sf, $filter_to) > 0) {
                          continue;
                      }

                      $filtered_rows[] = ['rak' => $rk, 'noshelf' => $sf, 'kirikanan' => $kk];
                  }

                  if (empty($filtered_rows)) {
                      $filtered_rows[] = [
                          'rak'       => $filter_rak !== '' ? $filter_rak : ($all_raks[0] ?? '-'),
                          'noshelf'   => $all_shelfs[0] ?? '-',
                          'kirikanan' => $all_kks[0] ?? '-'
                      ];
                  }
                ?>
                <div class="location-wrapper">
                  <?php foreach ($filtered_rows as $row_item): ?>
                    <div class="loc-box-top">
                      <span class="loc-lbl">Modis</span>
                      <span class="loc-val"><?= e($row_item['rak']) ?></span>
                    </div>
                    <div class="loc-row-bottom">
                      <div class="loc-box-sub">
                        <span class="loc-lbl">Shelfing</span>
                        <span class="loc-val"><?= e($row_item['noshelf']) ?></span>
                      </div>
                      <div class="loc-box-sub">
                        <span class="loc-lbl">Baris</span>
                        <span class="loc-val"><?= e($row_item['kirikanan']) ?></span>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>

        </div>
      </div>

    <?php endif; ?>
  <?php endif; ?>
</div>

<script>
  // Render Barcode Code 128 menggunakan JsBarcode
  document.addEventListener("DOMContentLoaded", function() {
    JsBarcode(".barcode-svg").init();
  });

  // Fungsi untuk menutup pop-up (X) tanpa mereset form pencarian
  function closeModal() {
    const modal = document.getElementById('modalOverlay');
    if (modal) {
      modal.style.display = 'none';
    }
  }

  // Fungsi toggle tampilkan barcode lainnya
  function toggleBarcodes(plu, btn) {
    const extraDiv = document.getElementById('extraBarcodes-' + plu);
    if (extraDiv.style.display === 'flex' || extraDiv.style.display === 'block') {
      extraDiv.style.display = 'none';
      btn.textContent = btn.textContent.replace('Sembunyikan', 'Tampilkan');
    } else {
      extraDiv.style.display = 'flex';
      btn.textContent = btn.textContent.replace('Tampilkan', 'Sembunyikan');
      setTimeout(() => {
        JsBarcode("#extraBarcodes-" + plu + " .barcode-svg").init();
      }, 50);
    }
  }

  // Navigasi dengan Tombol Volume (Volume Up = Next, Volume Down = Prev)
  document.addEventListener('keydown', function(event) {
    if (['TEXTAREA', 'SELECT', 'INPUT'].includes(document.activeElement.tagName)) {
      return;
    }

    const isVolumeUp = event.key === 'AudioVolumeUp' || event.code === 'AudioVolumeUp' || event.key === 'ArrowUp';
    const isVolumeDown = event.key === 'AudioVolumeDown' || event.code === 'AudioVolumeDown' || event.key === 'ArrowDown';

    if (isVolumeUp) {
      const nextBtn = document.getElementById('next-btn');
      if (nextBtn) {
        event.preventDefault();
        nextBtn.click();
      }
    } else if (isVolumeDown) {
      const prevBtn = document.getElementById('prev-btn');
      if (prevBtn) {
        event.preventDefault();
        prevBtn.click();
      }
    }
  });
</script>
</body>
</html>