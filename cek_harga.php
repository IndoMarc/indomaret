<?php
/**
 * Cek Harga Produk
 * Taruh file ini satu folder dengan data_produk.json lalu buka lewat server PHP:
 *   php -S localhost:8000   →   http://localhost:8000/index.php
 */

const JSON_FILE = __DIR__ . '/data_produk.json'; // ganti jika nama file berbeda
const PER_PAGE  = 15;

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$q    = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));

$error = null;
$hasil = [];
$total = 0;

if ($q !== '') {
    if (!is_file(JSON_FILE)) {
        $error = 'File JSON tidak ditemukan: ' . basename(JSON_FILE);
    } else {
        $data = json_decode(file_get_contents(JSON_FILE), true);
        if (!is_array($data)) {
            $error = 'File JSON tidak valid.';
        } else {
            // Semua kata harus cocok (AND) pada plu / barcode / deskripsi
            $kata = preg_split('/\s+/', mb_strtolower($q));
            foreach ($data as $row) {
                $hay = mb_strtolower(($row['plu'] ?? '') . ' ' . ($row['barcode'] ?? '') . ' ' . ($row['deskripsi'] ?? ''));
                $ok = true;
                foreach ($kata as $k) {
                    if ($k !== '' && mb_strpos($hay, $k) === false) { $ok = false; break; }
                }
                if ($ok) $hasil[] = $row;
            }
            $total = count($hasil);
        }
    }
}

$totalPages = max(1, (int)ceil($total / PER_PAGE));
$page       = min($page, $totalPages);
$tampil     = array_slice($hasil, ($page - 1) * PER_PAGE, PER_PAGE);

function url(array $over = []): string {
    global $q, $page;
    return '?' . http_build_query(array_merge(['q' => $q, 'page' => $page], $over));
}
function rp($n): string { return number_format((float)$n, 0, ',', '.'); }
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cek Harga Produk</title>
<!-- Google Fonts: Inter & Source Serif 4 -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600&display=swap" rel="stylesheet">
<!-- html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<style>
:root{
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
  --green: #3d7a4f;
  --green-bg: #e8f1ea;
  --red: #b3402a;
  --red-bg: #fbeae5;
}

@media (prefers-color-scheme:dark){
  :root{
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
    --green: #8cc79c;
    --green-bg: #2c3a31;
    --red: #f0a28e;
    --red-bg: #43302b;
  }
}

*{box-sizing:border-box; margin:0; padding:0;}
body{
  margin:0;
  background-color: var(--bg-color);
  color: var(--text-main);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}

.wrap{max-width:1100px;margin:0 auto;padding:20px 16px 64px}
header{
  text-align:center;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--border);
  width: 100%;
}
h1{
  font-family: 'Source Serif 4', Georgia, serif;
  font-weight: 500;
  font-size: clamp(24px, 4vw, 32px);
  margin: 0;
  letter-spacing: -0.4px;
  color: var(--text-main);
}
body.has-q header{padding:20px 0 16px}

form.search{
  max-width:720px;
  margin:0 auto 16px;
  background: var(--item-bg);
  border:1px solid var(--border);
  border-radius:14px;
  padding:8px 8px 8px 16px;
  display:flex;
  gap:8px;
  align-items:center;
  box-shadow: var(--shadow);
  transition:border-color .2s, box-shadow .2s;
}
form.search:focus-within{border-color:var(--accent); box-shadow: 0 0 0 2px var(--accent-soft);}
form.search svg.search-icon{flex:none;color:var(--text-sub);}
form.search input{
  flex:1;
  min-width:0;
  border:0;
  outline:0;
  background:transparent;
  color:var(--text-main);
  font:inherit;
  padding:8px 0;
}
form.search input::placeholder{color: var(--text-sub); opacity: 0.8;}

button,.btn{
  font:inherit;
  cursor:pointer;
  border:0;
  border-radius:10px;
  padding:10px 14px;
  background:var(--accent);
  color:#fff;
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  transition:background .15s;
}
button:hover,.btn:hover{background:var(--accent-hover)}
.hint{text-align:center;color:var(--text-sub);font-size:14px;margin:10px 0 0}

.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
.card{
  background: var(--card-bg);
  border:1px solid var(--border);
  border-radius:16px;
  padding:18px;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  gap:12px;
  height:100%;
  text-align:left;
  box-shadow: var(--shadow);
  transition:transform .15s,box-shadow .15s;
}
.card:hover{transform:translateY(-2px);box-shadow: 0 8px 24px rgba(0,0,0,.08)}

.card-body-row {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  justify-content: space-between;
}

.card-content {
  display: flex;
  flex-direction: column;
  gap: 6px;
  text-align: left;
  flex: 1;
  min-width: 0;
}

.product-img {
  width: 75px;
  height: 75px;
  object-fit: contain;
  border-radius: 10px;
  background: var(--item-hover);
  flex: none;
  border: 1px solid var(--border);
}

.plu-text { color: var(--text-sub); font-size: 13px; text-align: left; font-weight: 500;}
.desk-text { 
  font-family: 'Source Serif 4', Georgia, serif; 
  font-size: 17px; 
  font-weight: 500;
  line-height: 1.3; 
  margin: 0; 
  text-align: left; 
  color: var(--text-main);
}
.price-normal { font-weight: 600; text-align: left; color: var(--text-main); margin-top: 4px;}
.price-normal.has-promo { text-decoration: line-through; color: var(--text-sub); font-weight: 400; font-size: 13px;}
.price-promo { color: var(--green); font-weight: 600; font-size: 17px; text-align: left; }

.promo-box {
  background: var(--accent-soft);
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 13px;
  text-align: left;
  margin-top: 4px;
  border: 1px solid var(--border);
}
.promo-title { font-weight: 600; color: var(--accent); margin-bottom: 2px; text-align: left; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
.promo-content { display: none; margin-top: 6px; color: var(--text-main); }
.promo-content.show { display: block; }

.empty{text-align:center;color:var(--text-sub);padding:56px 0}
.error{max-width:720px;margin:24px auto;background:var(--red-bg);color:var(--red);padding:12px 16px;border-radius:12px;border:1px solid var(--border)}

nav.pager{display:flex;gap:12px;justify-content:center;align-items:center;margin-top:32px}
nav.pager a,nav.pager span{
  min-width:90px;
  height:40px;
  padding:0 16px;
  border-radius:10px;
  border:1px solid var(--border);
  background: var(--card-bg);
  color:var(--text-main);
  text-decoration:none;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  font-size:14px;
  font-weight: 500;
  box-shadow: var(--shadow);
}
nav.pager a:hover{border-color:var(--accent);color:var(--accent)}
nav.pager .off{opacity:.4;pointer-events:none}

/* Scanner Modal Styling */
#scanner-container {
  display: none;
  position: fixed;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(41, 38, 27, 0.75);
  z-index: 9999;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  padding: 16px;
}
#reader {
  width: 100%;
  max-width: 400px;
  background: var(--card-bg);
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid var(--border);
}
.scanner-close {
  margin-top: 15px;
  background: var(--red);
}
</style>
</head>
<body class="<?= $q !== '' ? 'has-q' : '' ?>">
<div class="wrap">
  <header>
    <h1>Cek Harga Produk</h1>
  </header>

  <form class="search" id="searchForm" method="get" action="">
    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
    <input type="search" id="searchInput" name="q" value="<?= e($q) ?>" placeholder="Ketik PLU, Barcode, atau Nama Produk" autofocus autocomplete="off">
    <!-- Tombol Kamera untuk Scan Barcode -->
    <button type="button" id="btnScan" title="Scan Barcode">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
    </button>
  </form>
  <?php if ($q === ''): ?>
    <p class="hint">Hasil akan tampil setelah melakukan pencarian atau scan barcode.</p>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error"><?= e($error) ?></div>

  <?php elseif ($q !== ''): ?>
    <?php if ($total === 0): ?>
      <div class="empty">Tidak ada data yang cocok dengan “<?= e($q) ?>”.</div>
    <?php else: ?>
      <div class="grid" style="margin-top:24px">
      <?php foreach ($tampil as $r): ?>
        <?php 
          $plu         = $r['plu'] ?? '';
          $hargaNormal = (float)($r['harga_normal'] ?? 0);
          $hargaPromo  = isset($r['harga_promo']) ? (float)$r['harga_promo'] : $hargaNormal;
          $adaPromo    = ($hargaPromo > 0 && $hargaPromo != $hargaNormal);
          $dp          = $r['detail_promosi'] ?? [];
        ?>
        <article class="card">
          <div class="card-body-row">
            <div class="card-content">
              <div class="plu-text"><?= e($plu) ?></div>
              <h2 class="desk-text"><?= e($r['deskripsi'] ?? '') ?></h2>
              
              <div>
                <div class="price-normal <?= $adaPromo ? 'has-promo' : '' ?>">
                  Rp <?= rp($hargaNormal) ?>
                </div>
                <?php if ($adaPromo): ?>
                  <div class="price-promo">
                    Rp <?= rp($hargaPromo) ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Gambar Produk (mengikuti PLU secara dinamis dengan referrerpolicy) -->
            <?php if (!empty($plu)): ?>
              <img src="https://cdn-klik.klikindomaret.com/klik-catalog/product/<?= e($plu) ?>_1.jpg" 
                   alt="<?= e($r['deskripsi'] ?? '') ?>" 
                   class="product-img" 
                   referrerpolicy="no-referrer"
                   onerror="this.style.display='none'">
            <?php endif; ?>
          </div>

          <?php if (!empty($dp)): ?>
            <div class="promo-box">
              <div class="promo-title" onclick="togglePromo(this)">
                <span>Detail Promosi</span>
                <span>▼</span>
              </div>
              <div class="promo-content">
                <?php foreach ($dp as $p): ?>
                  <div style="text-align: left; margin-bottom: 4px;">
                    <?= e($p['mekanisme'] ?? '') ?> 
                    <?php if (!empty($p['tanggal_awal']) && !empty($p['tanggal_akhir'])): ?>
                      <br><small style="color:var(--text-sub);">(<?= e($p['tanggal_awal']) ?> s.d. <?= e($p['tanggal_akhir']) ?>)</small>
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
      </div>

      <?php if ($totalPages > 1): ?>
      <nav class="pager" aria-label="Navigasi halaman">
        <?php if ($page > 1): ?>
          <a href="<?= e(url(['page' => $page - 1])) ?>">Prev</a>
        <?php else: ?>
          <span class="off">Prev</span>
        <?php endif; ?>

        <span style="border:none; background:none; font-size:14px; color:var(--text-sub); box-shadow:none;">
          Hal <?= $page ?> dari <?= $totalPages ?>
        </span>

        <?php if ($page < $totalPages): ?>
          <a href="<?= e(url(['page' => $page + 1])) ?>">Next</a>
        <?php else: ?>
          <span class="off">Next</span>
        <?php endif; ?>
      </nav>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
</div>

<!-- Modal untuk Scanner Kamera (html5-qrcode) -->
<div id="scanner-container">
  <div id="reader"></div>
  <button type="button" class="scanner-close" id="btnCloseScanner">Tutup Kamera</button>
</div>

<script>
// Fungsi Toggle untuk Collapse/Tampilkan Detail Promosi
function togglePromo(element) {
  const content = element.nextElementSibling;
  content.classList.toggle('show');
  const arrow = element.querySelector('span:last-child');
  arrow.textContent = content.classList.contains('show') ? '▲' : '▼';
}

// Auto clear kotak pencarian setelah hasil muncul di halaman
window.addEventListener('DOMContentLoaded', (event) => {
  const searchInput = document.getElementById('searchInput');
  if (searchInput && searchInput.value !== '') {
    setTimeout(() => {
      searchInput.value = '';
    }, 200);
  }
});

// Integrasi html5-qrcode untuk Scan Barcode via Kamera
const btnScan = document.getElementById('btnScan');
const scannerContainer = document.getElementById('scanner-container');
const btnCloseScanner = document.getElementById('btnCloseScanner');
let html5QrCode = null;

btnScan.addEventListener('click', function() {
  scannerContainer.style.display = 'flex';
  html5QrCode = new Html5Qrcode("reader");
  
  html5QrCode.start(
    { facingMode: "environment" },
    {
      fps: 10,
      qrbox: { width: 250, height: 150 }
    },
    (decodedText, decodedResult) => {
      html5QrCode.stop().then(() => {
        scannerContainer.style.display = 'none';
        document.getElementById('searchInput').value = decodedText;
        document.getElementById('searchForm').submit();
      }).catch(err => {
        console.error("Gagal menghentikan scanner", err);
      });
    },
    (errorMessage) => {}
  ).catch(err => {
    console.error("Gagal memulai kamera:", err);
    alert("Gagal mengaktifkan kamera. Pastikan izin kamera diberikan.");
    scannerContainer.style.display = 'none';
  });
});

btnCloseScanner.addEventListener('click', function() {
  if (html5QrCode) {
    html5QrCode.stop().then(() => {
      scannerContainer.style.display = 'none';
    }).catch(err => {
      scannerContainer.style.display = 'none';
    });
  } else {
    scannerContainer.style.display = 'none';
  }
});
</script>
</body>
</html>