<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="referrer" content="no-referrer">
    <title>Buat QRCode</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        :root {
            --bg: #faf9f5;
            --surface: #ffffff;
            --surface-soft: #f5f4ed;
            --border: #e8e6dc;
            --border-strong: #d6d3c4;
            --text: #141413;
            --text-muted: #73726c;
            --accent: #c6613f;
            --accent-hover: #b5553a;
            --accent-soft: rgba(198, 97, 63, 0.14);
            --shadow: 0 4px 24px rgba(20, 20, 19, 0.04), 0 0 0 0.5px rgba(20, 20, 19, 0.04);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #262624;
                --surface: #30302e;
                --surface-soft: #3a3a37;
                --border: #413f3b;
                --border-strong: #55524c;
                --text: #faf9f5;
                --text-muted: #a8a69d;
                --accent: #d97757;
                --accent-hover: #e38a6c;
                --accent-soft: rgba(217, 119, 87, 0.25);
                --shadow: 0 4px 24px rgba(0, 0, 0, 0.25);
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            padding: 40px 16px 32px;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .page-title {
            text-align: center;
            font-family: 'Source Serif 4', Georgia, 'Times New Roman', serif;
            font-size: 32px;
            font-weight: 400;
            letter-spacing: -0.01em;
            color: var(--text);
            margin-bottom: 6px;
        }

        .page-subtitle {
            text-align: center;
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        /* Kotak input seperti chat box Claude */
        .search-container {
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow: var(--shadow);
            padding: 14px 14px 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            margin-bottom: 20px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-container:focus-within {
            border-color: var(--border-strong);
            box-shadow: 0 6px 28px rgba(20, 20, 19, 0.07), 0 0 0 0.5px rgba(20, 20, 19, 0.06);
        }

        .search-textarea {
            width: 100%;
            height: 96px;
            padding: 6px 8px;
            border: none;
            background: transparent;
            color: var(--text);
            font-size: 15px;
            line-height: 1.55;
            font-family: 'Inter', sans-serif;
            outline: none;
            resize: none;
        }

        .search-textarea::placeholder {
            color: var(--text-muted);
        }

        .btn-action-group {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .btn-reset {
            background-color: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-strong);
            border-radius: 10px;
            padding: 9px 16px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
        }

        .btn-reset:hover {
            background-color: var(--surface-soft);
            color: var(--text);
        }

        .btn-submit {
            background-color: var(--accent);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 9px 20px;
            font-size: 14px;
            font-weight: 500;
            font-family: inherit;
            cursor: pointer;
            transition: background-color 0.15s, transform 0.05s;
        }

        .btn-submit:hover {
            background-color: var(--accent-hover);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* Hasil */
        .qrcode-display-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            width: 100%;
        }

        .qrcode-result-card {
            background-color: var(--surface);
            border-radius: 22px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 28px 20px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            width: 100%;
        }

        #qrcode_box {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
        }

        #qrcode_box img {
            display: block;
        }

        .qrcode-label-text {
            font-family: 'Source Serif 4', Georgia, serif;
            font-size: 18px;
            font-weight: 500;
            color: var(--text);
            word-break: break-all;
            text-align: center;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            width: 100%;
        }

        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: 1px solid var(--border);
            background-color: var(--surface);
            color: var(--text);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            font-family: inherit;
            text-decoration: none;
            transition: all 0.15s ease;
            min-width: 38px;
            height: 38px;
            cursor: pointer;
        }

        .pagination-btn:hover:not(.disabled):not(.active) {
            background-color: var(--surface-soft);
            border-color: var(--border-strong);
        }

        .pagination-btn.active {
            background-color: var(--accent-soft);
            color: var(--accent);
            border-color: transparent;
            font-weight: 600;
        }

        .pagination-btn.disabled {
            color: var(--border-strong);
            background-color: transparent;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 4px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
        }

        .menu-icon-svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="page-title">Buat QRCode</h1>
        <p class="page-subtitle"></p>

        <div class="search-container">
            <textarea
                id="qr_text_input"
                class="search-textarea"
                placeholder="Ketik teks disini ( pisahkan dengan spasi atau enter )..."
                autocomplete="off"
            ></textarea>

            <div class="btn-action-group">
                <button type="button" class="btn-reset" onclick="resetQRCode()">Reset</button>
                <button type="button" class="btn-submit" onclick="generateQRCode()">Generate</button>
            </div>
        </div>

        <div class="result-area">
            <div class="qrcode-display-wrapper" id="qr_display_wrapper" style="display: none;">
                <div class="qrcode-result-card">
                    <div id="qrcode_box"></div>
                    <div class="qrcode-label-text" id="qrcode_label_text"></div>
                </div>
                <div class="pagination-container" id="qr_pagination_container" style="display: none;"></div>
            </div>
        </div>
    </div>

    <script>
        let qrList = [];
        let currentQrIndex = 0;

        function formatCodeText(str) {
            if (str.includes(':')) {
                const parts = str.split(':');
                const part1 = parts[0].trim();
                const part2 = parts[1].trim();
                const paddedPart2 = part2.padStart(4, '0');
                return 'B' + part1 + paddedPart2;
            }
            return str;
        }

        function generateQRCode() {
            const rawText = document.getElementById('qr_text_input').value;
            const qrWrapper = document.getElementById('qr_display_wrapper');

            const tokens = rawText.split(/[\s\r\n]+/).map(t => t.trim()).filter(t => t !== '');
            qrList = tokens.map(t => formatCodeText(t));

            if (qrList.length === 0) {
                alert('Silakan masukkan teks terlebih dahulu.');
                return;
            }

            currentQrIndex = 0;
            qrWrapper.style.display = 'flex';
            renderCurrentQRCode();
        }

        function renderCurrentQRCode() {
            const qrBox = document.getElementById('qrcode_box');
            const qrLabel = document.getElementById('qrcode_label_text');
            const navContainer = document.getElementById('qr_pagination_container');

            qrBox.innerHTML = '';
            const textToRender = qrList[currentQrIndex];
            qrLabel.textContent = textToRender;

            new QRCode(qrBox, {
                text: textToRender,
                width: 180,
                height: 180,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            if (qrList.length > 1) {
                navContainer.style.display = 'flex';
                renderQrPagination(navContainer);
            } else {
                navContainer.style.display = 'none';
            }
        }

        function renderQrPagination(container) {
            let html = '';
            const total = qrList.length;

            if (currentQrIndex > 0) {
                html += `<button type="button" class="pagination-btn" onclick="goToQrPage(${currentQrIndex - 1})"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>`;
            } else {
                html += `<span class="pagination-btn disabled"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></span>`;
            }

            if (total <= 3) {
                for (let i = 0; i < total; i++) {
                    const activeClass = (i === currentQrIndex) ? 'active' : '';
                    html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToQrPage(${i})">${i + 1}</button>`;
                }
            } else {
                if (currentQrIndex <= 1) {
                    for (let i = 0; i < 3; i++) {
                        const activeClass = (i === currentQrIndex) ? 'active' : '';
                        html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToQrPage(${i})">${i + 1}</button>`;
                    }
                    html += `<span class="pagination-ellipsis">...</span>`;
                } else if (currentQrIndex >= total - 2) {
                    html += `<button type="button" class="pagination-btn" onclick="goToQrPage(0)">1</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                    for (let i = total - 3; i < total; i++) {
                        const activeClass = (i === currentQrIndex) ? 'active' : '';
                        html += `<button type="button" class="pagination-btn ${activeClass}" onclick="goToQrPage(${i})">${i + 1}</button>`;
                    }
                } else {
                    html += `<button type="button" class="pagination-btn" onclick="goToQrPage(0)">1</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                    html += `<button type="button" class="pagination-btn active" onclick="goToQrPage(${currentQrIndex})">${currentQrIndex + 1}</button>`;
                    html += `<span class="pagination-ellipsis">...</span>`;
                }
            }

            if (currentQrIndex < total - 1) {
                html += `<button type="button" class="pagination-btn" onclick="goToQrPage(${currentQrIndex + 1})"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>`;
            } else {
                html += `<span class="pagination-btn disabled"><svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></span>`;
            }

            container.innerHTML = html;
        }

        function goToQrPage(index) {
            currentQrIndex = index;
            renderCurrentQRCode();
        }

        function resetQRCode() {
            document.getElementById('qr_text_input').value = '';
            document.getElementById('qrcode_box').innerHTML = '';
            document.getElementById('qrcode_label_text').textContent = '';
            document.getElementById('qr_pagination_container').innerHTML = '';
            document.getElementById('qr_pagination_container').style.display = 'none';
            document.getElementById('qr_display_wrapper').style.display = 'none';
            qrList = [];
            currentQrIndex = 0;
        }

        window.addEventListener('DOMContentLoaded', () => {
            const qrInput = document.getElementById('qr_text_input');
            if (qrInput) {
                qrInput.focus();
            }
        });
    </script>

</body>
</html>