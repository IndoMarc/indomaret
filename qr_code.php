<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="referrer" content="no-referrer">
    <title>Buat QRCode</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

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
            padding: 20px 15px;
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
            max-width: 600px;
            margin: 0 auto;
        }

        .search-container {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        .search-textarea {
            width: 100%;
            height: 90px;
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

        .search-textarea:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
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

        .qrcode-display-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .qrcode-result-card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
        }

        #qrcode_box {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 8px;
        }

        #qrcode_box img {
            display: block;
        }

        .qrcode-label-text {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            word-break: break-all;
            text-align: center;
        }

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

        <div class="search-container">
            <textarea 
                id="qr_text_input" 
                class="search-textarea" 
                placeholder="Ketik teks disini ( pisahkan dengan spasi atau enter )..."
                autocomplete="off"
            ></textarea>

            <div class="btn-action-group">
                <button type="button" class="btn-submit" onclick="generateQRCode()">Generate</button>
                <button type="button" class="btn-reset" onclick="resetQRCode()">Reset</button>
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