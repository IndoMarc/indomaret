<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Indomaret App</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        :root {
            --primary: #0064D2;
            --primary-dark: #004CA0;
            --secondary: #FFC220;
            --bg: #F7F9FC;
            --card-bg: #FFFFFF;
            --text: #1A1D23;
            --text-light: #6B7280;
            --border: #E5E7EB;
        }

        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            touch-action: manipulation;
            margin: 0;
            padding-bottom: 50px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        input[type="text"], input[type="number"], input[type="file"], select, textarea {
            font-size: 16px !important;
        }

        .app-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 100, 210, 0.08);
            border: 1px solid var(--border);
            max-width: 900px;
            margin: 0 auto;
        }

        .app-input, .app-select, .app-textarea {
            padding: 12px 14px;
            width: 100%;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            transition: all 0.2s;
            background: white;
            color: var(--text);
        }

        .app-textarea {
            min-height: 80px;
            resize: vertical;
        }

        .app-input:focus, .app-select:focus, .app-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,100,210,0.1);
        }

        .app-btn {
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .app-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .app-btn-reset {
            background: #E5E7EB;
            color: var(--text);
        }

        .app-btn-reset:hover {
            background: #D1D5DB;
        }

        .loading-indicator { 
            display: block; 
            text-align: center; 
            font-size: 0.95rem; 
            color: #0d6efd; 
            font-weight: 600; 
            padding: 15px 0; 
            animation: blink 1.2s infinite; 
        }
        @keyframes blink { 0% { opacity: 0.3; } 50% { opacity: 1; } 100% { opacity: 0.3; } }

        .search-section { 
            margin-bottom: 16px; 
            padding: 10px; 
            background: #e9ecef; 
            border-radius: 10px; 
            width: 100%;
        }
        
        .search-wrapper {
            display: flex;
            gap: 8px;
            width: 100%;
            max-width: 100%;
            align-items: stretch;
        }

        .search-wrapper input[type="text"] { 
            flex: 1;
            min-width: 0;
            padding: 10px 12px; 
            border: 2px solid #ced4da; 
            border-radius: 8px;
            outline: none; 
            font-weight: 500;
            background: white;
            color: #212529;
            transition: all 0.2s ease;
            text-align: left;
        }
        
        .search-wrapper input[type="text"]:focus {
            border-color: #0d6efd; 
            box-shadow: 0 0 0 3px rgba(13,110,253,0.15); 
        }
        
        .btn-scan {
            background: #0d6efd;
            border: none;
            padding: 0 14px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
            transition: background-color 0.2s, transform 0.1s;
        }
        .btn-scan:hover { background: #0b5ed7; }
        .btn-scan:active { transform: scale(0.95); }

        #scanner-container {
            display: none;
            margin-bottom: 16px;
            border: 2px dashed #0d6efd;
            border-radius: 10px;
            overflow: hidden;
            background: #000;
            position: relative;
        }
        
        .btn-close-scanner {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
            z-index: 10;
        }
        
        #resTbody { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 10px; 
            margin-bottom: 15px;
            width: 100%;
        }
        
        .result-area { display: none; }
        
        .card { 
            background-color: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            padding: 10px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.03); 
            border-left: 3px solid #0d6efd;
            display: flex;
            flex-direction: column;
            gap: 2px;
            text-align: left;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            animation: cardAppear 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        @keyframes cardAppear {
            to { opacity: 1; transform: translateY(0); }
        }

        .product-image-container {
            width: 100%;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            margin-bottom: 6px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 6px;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .val-plu {
            font-size: 0.75rem;
            color: #64748b;
            font-family: monospace;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .val-deskripsi {
            font-size: 0.88rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
            min-height: 2.4em;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .val-harga-normal-coret { 
            text-decoration: line-through; 
            color: #94a3b8; 
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        .val-potongan { 
            display: inline-block;
            color: #dc3545; 
            background: #ffe3e3;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            width: fit-content;
            margin-bottom: 1px;
        }
        
        .val-harga-final {
            color: #198754; 
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: auto;
            padding-top: 2px;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 16px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-card {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 20px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-left: 5px solid #0d6efd;
            position: relative;
            transform: scale(0.7);
            transition: transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .modal-overlay.active .modal-card { transform: scale(1); }

        .modal-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #64748b;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .modal-card .product-image-container {
            height: 180px;
            margin-bottom: 10px;
        }

        .modal-card .val-plu { font-size: 0.85rem; }
        .modal-card .val-deskripsi {
            font-size: 1.05rem;
            -webkit-line-clamp: unset;
            min-height: auto;
            margin-bottom: 8px;
        }
        .modal-card .val-harga-normal-coret { font-size: 0.95rem; }
        .modal-card .val-potongan { font-size: 0.85rem; padding: 2px 8px; }
        .modal-card .val-harga-final { font-size: 1.4rem; padding-top: 6px; }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #edf2f7;
            animation: fadeIn 0.3s ease forwards;
        }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .btn-nav {
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #0d6efd;
            background-color: #fff;
            border: 2px solid #0d6efd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-nav:hover:not(:disabled) {
            background-color: #0d6efd;
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-nav:disabled {
            border-color: #e2e8f0;
            color: #cbd5e1;
            cursor: not-allowed;
        }
        .page-info {
            font-weight: 600;
            color: #475569;
            font-size: 0.8rem;
            background: #f1f5f9;
            padding: 5px 12px;
            border-radius: 16px;
        }

        .filter-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .filter-item label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }
        .shelf-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .action-group {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: center;
        }
        .message {
            margin: 20px 0;
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
            text-align: center;
        }
        .modal-overlay-itt {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-overlay-itt.show {
            display: flex;
            opacity: 1;
        }
        .modal-content-itt {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            border-top: 4px solid var(--secondary);
            transform: scale(0.9);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .modal-content-itt.compact {
            max-height: 75vh;
        }
        .modal-overlay-itt.show .modal-content-itt {
            transform: scale(1);
        }
        .modal-header-itt {
            padding: 16px 24px 0;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-shrink: 0;
        }
        .modal-close-itt {
            background: #F3F4F6;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            color: var(--text-light);
            padding: 0;
        }
        .modal-close-itt:hover {
            background: #E5E7EB;
            transform: none;
        }
        .card-body-itt {
            padding: 0 24px 24px;
            flex: 1;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.3s ease;
            text-align: center;
            overflow-y: auto;
        }
        .card-body-itt.animate {
            opacity: 1;
            transform: translateX(0);
        }
        .product-image-itt {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin: 0 auto 12px;
            border-radius: 8px;
            background: #F9FAFB;
            padding: 8px;
        }
        .plumd-value-itt {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }
        .card-title-itt {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 16px;
            min-height: 48px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .barcode-container-itt {
            margin: 16px 0;
            padding: 12px;
            background: #F9FAFB;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .barcode-container-itt svg {
            max-width: 100%;
        }
        .barcode-text-itt {
            font-size: 12px;
            color: var(--text-light);
            font-family: monospace;
        }
        .barcode-list-itt {
            display: none;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed var(--border);
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
        }
        .barcode-list-itt.show {
            display: block;
        }
        .barcode-item-itt {
            margin: 8px 0;
            padding: 8px;
            background: white;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .show-all-barcodes-itt {
            color: var(--primary);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .show-all-barcodes-itt:hover {
            text-decoration: underline;
        }
        .card-detail-itt {
            border-top: 1px dashed var(--border);
            padding-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .detail-row-itt {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .detail-item-itt {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .detail-item-itt.full {
            grid-column: span 2;
        }
        .detail-label-itt {
            font-size: 11px;
            color: var(--text-light);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .detail-value-itt {
            color: var(--text);
            font-weight: 600;
            font-size: 14px;
            min-height: 22px;
        }
        .pagination-itt {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }
        .page-info-itt {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-light);
            min-width: 120px;
            text-align: center;
        }

        .plano-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .modal-content-plano {
            background: var(--bg);
            width: 98vw;
            height: 96vh;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        .modal-header-plano {
            background: var(--card-bg);
            padding: 8px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title-group-plano h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }
        .btn-close-plano {
            background: #EF4444;
            padding: 6px 14px;
            font-size: 12px;
            color: white;
            border-radius: 6px;
        }
        .btn-close-plano:hover {
            background: #DC2626;
        }
        .modal-body-wrapper-plano {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: #EBF0F5;
        }
        .modal-body-canvas-plano {
            width: 100%;
            height: 100%;
            overflow: auto;
            cursor: grab;
            user-select: none;
            padding: 40px;
        }
        .modal-body-canvas-plano:active {
            cursor: grabbing;
        }
        .zoom-controls-plano {
            position: absolute;
            top: 50%;
            right: 24px;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 10000;
        }
        .btn-zoom-plano {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            cursor: pointer;
            user-select: none;
            transition: all 0.2s;
            padding: 0;
        }
        .btn-zoom-plano:hover {
            background: #F3F4F6;
            color: var(--primary);
            transform: scale(1.05);
        }
        .planogram-board {
            display: inline-flex;
            flex-direction: column-reverse;
            gap: 24px;
            background: #1E293B;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            min-width: max-content;
            transform-origin: top left;
            transition: transform 0.2s ease-out;
        }
        .shelf-block {
            background: #334155;
            border: 2px solid #475569;
            border-radius: 8px;
            padding: 16px;
            width: max-content;
        }
        .shelf-header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #475569;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .shelf-title {
            font-size: 13px;
            font-weight: 700;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .shelf-rak-name {
            font-size: 12px;
            font-weight: 700;
            color: #38BDF8;
            background: rgba(56, 189, 248, 0.15);
            padding: 2px 10px;
            border-radius: 20px;
        }
        .grid-container {
            display: flex;
            gap: 16px;
        }
        .product-card-plano {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 6px;
            justify-content: space-between;
            width: 160px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .product-image-plano {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin: 0 auto;
            border-radius: 6px;
            background: #FFFFFF;
            padding: 4px;
        }
        .product-plumd-plano {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
        }
        .product-desc-plano {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            min-height: 36px;
        }
        .product-loc-plano {
            font-size: 10px;
            color: var(--text-light);
            background: #E5E7EB;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        .note-container {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px 20px;
            border-radius: 12px;
            max-width: 900px;
            margin: 16px auto 0;
            font-size: 13px;
            line-height: 1.5;
            text-align: left;
        }
        .modal-overlay-qr {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .modal-overlay-qr.show {
            display: flex;
            opacity: 1;
        }
        .modal-content-qr {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 300px;
            position: relative;
            transform: scale(0.7);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay-qr.show .modal-content-qr {
            transform: scale(1);
        }
        #qrcode {
            transition: opacity 0.2s ease, transform 0.2s ease;
            margin-top: 10px;
        }
        #qrcode.fade-in {
            opacity: 1;
            transform: scale(1);
        }
        #qrcode.fade-out {
            opacity: 0;
            transform: scale(0.95);
        }
        .qr-label {
            margin-top: 15px;
            font-size: 15px;
            color: #333;
            word-break: break-all;
            max-width: 100%;
            font-weight: 600;
            line-height: 1.4;
            min-height: 24px;
            transition: opacity 0.2s ease;
        }
        .nav-group-qr {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            width: 100%;
            gap: 10px;
        }
        .counter-qr {
            font-size: 13px;
            color: #777;
            white-space: nowrap;
            flex-grow: 1;
            text-align: center;
            font-weight: 600;
        }

        .main-footer {
            background: #fff;
            height: 50px;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.05);
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top: 1px solid #eee;
        }
        .footer-text {
            font-size: 13px;
            color: #2c3e50;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <header class="bg-white text-gray-800 flex items-center justify-between px-4 py-3 shadow-sm border-b border-gray-200 z-30 sticky top-0 shrink-0">
        <button onclick="toggleSidebar()" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        
        <div class="flex-1 flex justify-center items-center">
            <h1 id="header-title" class="text-black font-bold text-lg md:text-xl tracking-tight">CEK HARGA</h1>
        </div>
        
        <a href="https://stock-opname.wasmer.app/" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 focus:outline-none transition-colors" title="Buka Stock Opname">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
        </a>
    </header>

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity duration-300"></div>

    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white text-gray-800 flex flex-col p-4 z-50 -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl border-r border-gray-200">
        <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-200">
            <div class="flex items-center justify-center flex-1">
                <img src="indomaret.PNG" alt="Indomaret" class="h-8 object-contain max-w-[140px]" onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'120\' height=\'30\' viewBox=\'0 0 120 30\'><rect width=\'120\' height=\'30\' fill=\'%230064D2\'/><text x=\'50%25\' y=\'55%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'white\' font-family=\'sans-serif\' font-weight=\'bold\' font-size=\'12\'>INDOMARET</text></svg>';">
            </div>
            <button onclick="toggleSidebar()" class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="flex-1">
            <ul class="space-y-2">
                <li>
                    <button id="nav-beranda" onclick="switchTab('beranda')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-50 text-blue-600 font-medium hover:bg-blue-100 transition-colors">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span>Cek Harga</span>
                    </button>
                </li>
                <li>
                    <button id="nav-itt" onclick="switchTab('itt')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2m5-8v4m4-4v4m4-4v4"></path>
                        </svg>
                        <span>Scan ITT</span>
                    </button>
                </li>
                <li>
                    <button id="nav-plano" onclick="switchTab('plano')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16M7 6v12M17 6v12"></path>
                        </svg>
                        <span>Planogram</span>
                    </button>
                </li>
                <li>
                    <button id="nav-qrcode" onclick="switchTab('qrcode')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-100 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <span>Buat QRCode</span>
                    </button>
                </li>
            </ul>
        </nav>

        <div class="pt-4 mt-auto border-t border-gray-200 text-center">
            <div id="realtime-clock" class="text-xs font-medium text-gray-500 bg-gray-50 py-2 px-3 rounded-lg border border-gray-200 leading-tight">
                Memuat waktu...
            </div>
        </div>
    </aside>

    <main id="main-content" class="flex-1 p-4 md:p-8 overflow-y-auto">
        
        <div id="page-beranda" class="app-card">
            <div id="scanner-container">
                <button class="btn-close-scanner" onclick="stopScanner()">Tutup Kamera</button>
                <div id="interactive-reader" style="width: 100%;"></div>
            </div>

            <div class="search-section" id="searchBox">
                <div id="loadingDb" class="loading-indicator">Sedang memuat data, mohon tunggu ...</div>
                
                <div class="search-wrapper" id="searchWrapper" style="display: none;">
                    <input type="text" id="keyword" placeholder="Cari produk disini ..." autocomplete="off">
                    <button class="btn-scan" id="startScanBtn" onclick="toggleScanner()" title="Scan Barcode dengan Kamera">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div id="resultArea" class="result-area">
                <div id="resTbody"></div>
                
                <div class="pagination-container" id="paginationBox" style="display: none;">
                    <button class="btn-nav" id="btnPrev" onclick="ubahHalaman(-1)">Prev</button>
                    <span class="page-info" id="pageIndicator">1 / 1</span>
                    <button class="btn-nav" id="btnNext" onclick="ubahHalaman(1)">Next</button>
                </div>
            </div>
        </div>

        <div id="page-itt" class="app-card hidden">
            <div class="filter-group">
                <div class="filter-item">
                    <label for="keywordItt">Cari Produk</label>
                    <textarea id="keywordItt" class="app-textarea" placeholder="Ketik Plu atau Barcode disini ... Pisahkan dengan spasi atau enter ..."></textarea>
                </div>
                <div class="filter-item">
                    <label for="filterNamaRakItt">Pilih Modis</label>
                    <select id="filterNamaRakItt" class="app-select">
                        <option value="">-- Semua --</option>
                    </select>
                </div>
                <div class="filter-item">
                    <div class="shelf-row">
                        <div class="filter-item">
                            <label for="filterDariShelfItt">Dari Shelfing</label>
                            <select id="filterDariShelfItt" class="app-select">
                                <option value="">-- Semua --</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="filterSampaiShelfItt">Sampai Shelfing</label>
                            <select id="filterSampaiShelfItt" class="app-select">
                                <option value="">-- Semua --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="action-group">
                <button class="app-btn" onclick="searchDataItt()">Tampilkan</button>
                <button class="app-btn app-btn-reset" onclick="resetSearchItt()">Reset</button>
            </div>

            <div id="statusMessageItt" class="message"></div>
        </div>

        <div id="page-plano" class="app-card hidden">
            <div class="filter-group">
                <div class="filter-item">
                    <label for="filterNamaRakPlano">Pilih Modis</label>
                    <select id="filterNamaRakPlano" class="app-select">
                        <option value="">-- Semua --</option>
                    </select>
                </div>
                <div class="filter-item">
                    <div class="shelf-row">
                        <div class="filter-item">
                            <label for="filterDariShelfPlano">Dari Shelfing</label>
                            <select id="filterDariShelfPlano" class="app-select">
                                <option value="">-- Semua --</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="filterSampaiShelfPlano">Sampai Shelfing</label>
                            <select id="filterSampaiShelfPlano" class="app-select">
                                <option value="">-- Semua --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="action-group">
                <button class="app-btn" onclick="searchDataPlano()">Tampilkan</button>
                <button class="app-btn app-btn-reset" onclick="resetSearchPlano()">Reset</button>
            </div>

            <div id="statusMessagePlano" class="message"></div>
        </div>

        <div id="page-qrcode" class="app-card hidden">
            <div class="filter-group">
                <div class="filter-item">
                    <label for="file-input">Input file .txt untuk buat QRCode :</label>
                    <input type="file" id="file-input" class="app-input" accept=".txt" onchange="handleFileUpload(this)">
                </div>
                <div class="filter-item">
                    <label for="text-input-qr">Ketik PLU / Barcode :</label>
                    <textarea id="text-input-qr" class="app-textarea" placeholder="Ketik PLU/Barcode disini , atau input file .txt di atas ..."></textarea>
                </div>
            </div>

            <div class="action-group">
                <button class="app-btn" onclick="generateBulkQR()">Buat QRCode</button>
                <button class="app-btn app-btn-reset" onclick="resetFormQR()">Reset</button>
            </div>

            <div class="note-container">
                <strong>Note :</strong> Untuk membuat QRCode contoh air aqua galon qty 2 ( PLU:QTY ) jadi 10036631:2 , Kalau qty nya hanya 1 berarti yg di ketik hanya PLU nya saja ..
            </div>
        </div>

    </main>

    <footer class="main-footer">
        <div class="footer-text">~ m.h.r ~</div>
    </footer>

    <div class="modal-overlay" id="productModal" onclick="closeModal(event)">
        <div class="modal-card" onclick="event.stopPropagation()">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <div id="modalContent"></div>
        </div>
    </div>

    <div id="modalOverlayItt" class="modal-overlay-itt">
        <div class="modal-content-itt" id="modalContentItt">
            <div class="modal-header-itt">
                <button class="modal-close-itt" onclick="closeModalItt()">✕</button>
            </div>
            <div id="modalBodyItt" class="card-body-itt"></div>
            <div class="pagination-itt">
                <button id="prevBtnItt" class="btn-nav-itt app-btn" onclick="changePageItt(-1)">Prev</button>
                <span id="pageInfoItt" class="page-info-itt"></span>
                <button id="nextBtnItt" class="btn-nav-itt app-btn" onclick="changePageItt(1)">Next</button>
            </div>
        </div>
    </div>

    <div id="planoModal" class="plano-modal">
        <div class="modal-content-plano">
            <div class="modal-header-plano">
                <div class="modal-title-group-plano">
                    <h3 id="modalTitlePlano">Visualisasi Planogram</h3>
                </div>
                <button class="btn-close-plano" onclick="closeModalPlano()">Tutup</button>
            </div>
            <div class="modal-body-wrapper-plano">
                <div class="modal-body-canvas-plano" id="modalCanvasPlano">
                    <div class="planogram-board" id="planogramBoard"></div>
                </div>
                <div class="zoom-controls-plano">
                    <button class="btn-zoom-plano" onclick="zoomInPlano()">+</button>
                    <button class="btn-zoom-plano" onclick="zoomOutPlano()">-</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay-qr" id="qrModal">
        <div class="modal-content-qr">
            <button class="modal-close" onclick="closeModalQR()">&times;</button>
            <div id="qrcode" class="fade-in"></div>
            <div class="qr-label" id="qrLabel"></div>
            <div class="nav-group-qr">
                <button class="btn-nav" onclick="prevQR()">Prev</button>
                <div class="counter-qr" id="qrCounter"></div>
                <button class="btn-nav" onclick="nextQR()">Next</button>
            </div>
        </div>
    </div>

    <script>
        function updateRealtimeClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const day = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const clockElement = document.getElementById('realtime-clock');
            if (clockElement) {
                clockElement.innerHTML = `<div>${dayName}, ${day} ${month} ${year}</div><div class="text-sm font-bold text-gray-800 mt-1">${hours}:${minutes}:${seconds} WIB</div>`;
            }
        }
        setInterval(updateRealtimeClock, 1000);
        updateRealtimeClock();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function switchTab(tabName) {
            const pageBeranda = document.getElementById('page-beranda');
            const pageItt = document.getElementById('page-itt');
            const pagePlano = document.getElementById('page-plano');
            const pageQrcode = document.getElementById('page-qrcode');

            const navBeranda = document.getElementById('nav-beranda');
            const navItt = document.getElementById('nav-itt');
            const navPlano = document.getElementById('nav-plano');
            const navQrcode = document.getElementById('nav-qrcode');

            const headerTitle = document.getElementById('header-title');

            pageBeranda.classList.add('hidden');
            pageItt.classList.add('hidden');
            pagePlano.classList.add('hidden');
            pageQrcode.classList.add('hidden');

            const inactiveClass = "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-100 hover:text-gray-900 transition-colors";
            const activeClass = "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-50 text-blue-600 font-medium hover:bg-blue-100 transition-colors";

            navBeranda.className = inactiveClass;
            navItt.className = inactiveClass;
            navPlano.className = inactiveClass;
            navQrcode.className = inactiveClass;

            if (tabName === 'beranda') {
                pageBeranda.classList.remove('hidden');
                navBeranda.className = activeClass;
                if (headerTitle) headerTitle.innerText = "CEK HARGA";
            } else if (tabName === 'itt') {
                pageItt.classList.remove('hidden');
                navItt.className = activeClass;
                if (headerTitle) headerTitle.innerText = "SCAN ITT";
            } else if (tabName === 'plano') {
                pagePlano.classList.remove('hidden');
                navPlano.className = activeClass;
                if (headerTitle) headerTitle.innerText = "PLANOGRAM";
            } else if (tabName === 'qrcode') {
                pageQrcode.classList.remove('hidden');
                navQrcode.className = activeClass;
                if (headerTitle) headerTitle.innerText = "BUAT QRCODE";
            }

            toggleSidebar();
        }

        let db = { 
            barcodeMap: new Map(), 
            prodmast: [], 
            promo: [],
            rakMap: new Map()
        };
        let isDataLoaded = false;
        let searchResults = []; 
        let currentPage = 1;
        const itemsPerPage = 10;
        let html5QrcodeScanner = null;
        
        const nonDiscountKeywords = [
            "I_KUPON", "BELI ITEM HEMAT MINGGU INI", "BELI ITEM PROMO 4 HARI", 
            "SETIAP PEMBELIAN", "TRANSAKSI ALL PAYMENT POINT", "BELI ITEM PALING MURAH SENILAI"
        ];

        const keywordInput = document.getElementById('keyword');
        const resTbody = document.getElementById('resTbody');
        const paginationBox = document.getElementById('paginationBox');
        const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });

        document.addEventListener('DOMContentLoaded', async () => {
            await initAppDatabase();
        });

        async function initAppDatabase() {
            if (isDataLoaded) return;
            const timestamp = Date.now();
            try {
                const [resBarcode, resProdmast, resPromo, resRak] = await Promise.all([
                    fetch(`UGD_BARCODE.CSV?t=${timestamp}`),
                    fetch(`UGD_PRODMAST.CSV?t=${timestamp}`),
                    fetch(`UGD_promo_matriks.CSV?t=${timestamp}`),
                    fetch(`UGD_RAK.CSV?t=${timestamp}`).catch(() => null)
                ]);

                if (!resBarcode.ok || !resProdmast.ok || !resPromo.ok) {
                    throw new Error("Gagal mengunduh database.");
                }

                const barcodeText = await resBarcode.text();
                parseBarcodeToMap(barcodeText);

                db.prodmast = parseCSV(await resProdmast.text());
                db.promo = parseCSV(await resPromo.text());

                if (resRak && resRak.ok) {
                    parseRakToMap(await resRak.text());
                }

                isDataLoaded = true;
                document.getElementById('loadingDb').style.display = "none";
                document.getElementById('searchWrapper').style.display = "flex";
                document.getElementById('resultArea').style.display = "block"; 
                document.getElementById('statusMessageItt').textContent = 'Berhasil memuat data ...';
                document.getElementById('statusMessagePlano').textContent = 'Berhasil memuat data ...';
                keywordInput.focus();
            } catch (err) {
                console.error(err);
                document.getElementById('loadingDb').innerText = "Gagal memuat data ...";
            }
        }

        function parseBarcodeToMap(text) {
            const lines = text.split(/\r?\n/);
            if (lines.length < 2) return;
            const headers = lines[0].split('|').map(h => h.trim().toUpperCase());
            const idxPlu = headers.indexOf('PLU');
            const idxBarcd = headers.indexOf('BARCD');
            if (idxPlu === -1 || idxBarcd === -1) return;

            for (let i = 1; i < lines.length; i++) {
                const line = lines[i].trim();
                if (!line) continue;
                const cols = line.split('|');
                const plu = cols[idxPlu] ? cols[idxPlu].trim() : '';
                const barcd = cols[idxBarcd] ? cols[idxBarcd].trim() : '';
                if (plu && barcd) {
                    if (!db.barcodeMap.has(plu)) db.barcodeMap.set(plu, []);
                    db.barcodeMap.get(plu).push(barcd);
                }
            }
        }

        function parseRakToMap(text) {
            const lines = text.split(/\r?\n/);
            if (lines.length < 2) return;
            const headers = lines[0].split('|').map(h => h.trim().toUpperCase());
            const idxPlumd = headers.indexOf('PLUMD');
            const idxNamaRak = headers.indexOf('NAMA_RAK');
            const idxNoShelf = headers.indexOf('NOSHELF');
            const idxKiriKanan = headers.indexOf('KIRIKANAN');
            if (idxPlumd === -1) return;

            const setNamaRak = new Set();
            const setNoShelf = new Set();

            for (let i = 1; i < lines.length; i++) {
                const line = lines[i].trim();
                if (!line) continue;
                const cols = line.split('|');
                const plumd = cols[idxPlumd] ? cols[idxPlumd].trim() : '';
                const namaRak = idxNamaRak !== -1 && cols[idxNamaRak] ? cols[idxNamaRak].trim() : '-';
                const noShelf = idxNoShelf !== -1 && cols[idxNoShelf] ? cols[idxNoShelf].trim() : '-';
                const kiriKanan = idxKiriKanan !== -1 && cols[idxKiriKanan] ? cols[idxKiriKanan].trim() : '-';

                if (namaRak && namaRak !== '-') setNamaRak.add(namaRak);
                if (noShelf && noShelf !== '-') setNoShelf.add(noShelf);

                if (plumd) {
                    if (!db.rakMap.has(plumd)) db.rakMap.set(plumd, []);
                    db.rakMap.get(plumd).push({ namaRak, noShelf, kiriKanan });
                }
            }
            populateDropdowns(setNamaRak, setNoShelf);
        }

        function populateDropdowns(setNamaRak, setNoShelf) {
            const arrNamaRak = Array.from(setNamaRak).sort((a, b) => a.localeCompare(b, undefined, { numeric: true }));
            const arrNoShelf = Array.from(setNoShelf).sort((a, b) => a.localeCompare(b, undefined, { numeric: true }));
            
            ['Itt', 'Plano'].forEach(type => {
                const selectNamaRak = document.getElementById(`filterNamaRak${type}`);
                const selectDariShelf = document.getElementById(`filterDariShelf${type}`);
                const selectSampaiShelf = document.getElementById(`filterSampaiShelf${type}`);

                arrNamaRak.forEach(rak => {
                    const opt = document.createElement('option');
                    opt.value = rak;
                    opt.textContent = rak;
                    selectNamaRak.appendChild(opt);
                });

                arrNoShelf.forEach(shelf => {
                    const optDari = document.createElement('option');
                    optDari.value = shelf;
                    optDari.textContent = shelf;
                    selectDariShelf.appendChild(optDari);

                    const optSampai = document.createElement('option');
                    optSampai.value = shelf;
                    optSampai.textContent = shelf;
                    selectSampaiShelf.appendChild(optSampai);
                });
            });
        }

        function parseCSV(text) {
            const lines = text.split(/\r?\n/).filter(line => line.trim() !== "");
            if (lines.length < 2) return [];
            const headers = lines[0].split('|').map(h => h.trim().toUpperCase());
            return lines.slice(1).map(line => {
                const data = line.split('|');
                let obj = {};
                headers.forEach((h, i) => obj[h] = data[i] ? data[i].trim() : "");
                return obj;
            });
        }

        function parseTanggal(str, isEnd = false) {
            if (!str) return null;
            const parts = str.split(/[\s/:-]+/);
            if (parts.length < 3) return null;
            const d = parseInt(parts[0], 10), m = parseInt(parts[1], 10) - 1, y = parseInt(parts[2], 10);
            let hh = parseInt(parts[3], 10) || 0, mm = parseInt(parts[4], 10) || 0, ss = parseInt(parts[5], 10) || 0;
            if (isEnd && hh === 0 && mm === 0 && ss === 0) { hh = 23; mm = 59; ss = 59; }
            return new Date(y, m, d, hh, mm, ss);
        }

        function formatIDR(val) { return formatter.format(val); }

        function toggleScanner() {
            const container = document.getElementById('scanner-container');
            if (container.style.display === 'block') {
                stopScanner();
            } else {
                container.style.display = 'block';
                html5QrcodeScanner = new Html5Qrcode("interactive-reader");
                
                const config = { fps: 15, qrbox: { width: 260, height: 140 } };
                
                html5QrcodeScanner.start(
                    { facingMode: "environment" }, 
                    config, 
                    (decodedText) => {
                        keywordInput.value = decodedText; 
                        stopScanner();                     
                        cariData();                        
                    },
                    (errorMessage) => {}
                ).catch(err => {
                    alert("Gagal mengakses Kamera: " + err);
                    stopScanner();
                });
            }
        }

        function stopScanner() {
            const container = document.getElementById('scanner-container');
            container.style.display = 'none';
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner = null;
                }).catch(err => console.error("Gagal menghentikan kamera", err));
            }
        }

        function showModal(itemIndex) {
            const item = searchResults[itemIndex];
            if (!item) return;

            let contentHTML = `
                <div class="product-image-container">
                    <img class="product-image" 
                         src="https://cdn-klik.klikindomaret.com/klik-catalog/product/${item.targetPLU}_1.jpg" 
                         alt="${item.prod ? item.prod.DESC2 : 'Produk'}" 
                         referrerpolicy="no-referrer"
                         onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23cbd5e1\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><polyline points=\'21 15 16 10 5 21\'/></svg>';">
                </div>
                <div class="val-plu">${item.targetPLU}</div>
                <div class="val-deskripsi">${item.prod ? item.prod.DESC2 : 'Produk Tidak Ditemukan'}</div>
            `;

            if (item.potonganSatuan > 0) {
                contentHTML += `
                    <div class="val-harga-normal-coret">${formatIDR(item.hargaNormal)}</div>
                    <div class="val-potongan">-${formatIDR(item.potonganSatuan)}</div>
                    <div class="val-harga-final">${formatIDR(item.hargaNormal - item.potonganSatuan)}</div>
                `;
            } else {
                contentHTML += `
                    <div class="val-harga-final" style="color: #0d6efd;">${formatIDR(item.hargaNormal)}</div>
                `;
            }

            document.getElementById('modalContent').innerHTML = contentHTML;
            const modal = document.getElementById('productModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        function closeModal(event) {
            if (event && event.target !== event.currentTarget && !event.target.classList.contains('modal-close')) return;
            const modal = document.getElementById('productModal');
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 250);
        }

        function renderPage(page) {
            resTbody.innerHTML = "";
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, searchResults.length);
            const pageItems = searchResults.slice(startIndex, endIndex);

            pageItems.forEach((item, index) => {
                const globalIndex = startIndex + index;
                let cardHTML = `
                    <div class="card" data-plu="${item.targetPLU}" style="animation-delay: ${index * 0.04}s" onclick="showModal(${globalIndex})">
                        <div class="product-image-container">
                            <img class="product-image" 
                                 src="https://cdn-klik.klikindomaret.com/klik-catalog/product/${item.targetPLU}_1.jpg" 
                                 alt="${item.prod ? item.prod.DESC2 : 'Produk'}" 
                                 referrerpolicy="no-referrer"
                                 onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'60\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23cbd5e1\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><rect x=\'3\' y=\'3\' width=\'18\' height=\'18\' rx=\'2\' ry=\'2\'/><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'/><polyline points=\'21 15 16 10 5 21\'/></svg>';">
                        </div>
                        <div class="val-plu">${item.targetPLU}</div>
                        <div class="val-deskripsi">${item.prod ? item.prod.DESC2 : 'Produk Tidak Ditemukan'}</div>
                `;

                if (item.potonganSatuan > 0) {
                    cardHTML += `
                        <div class="val-harga-normal-coret">${formatIDR(item.hargaNormal)}</div>
                        <div class="val-potongan">-${formatIDR(item.potonganSatuan)}</div>
                        <div class="val-harga-final">${formatIDR(item.hargaNormal - item.potonganSatuan)}</div>
                    `;
                } else {
                    cardHTML += `
                        <div class="val-harga-final" style="color: #0d6efd;">${formatIDR(item.hargaNormal)}</div>
                    `;
                }

                cardHTML += `</div>`;
                resTbody.insertAdjacentHTML('beforeend', cardHTML);
            });

            const totalPages = Math.ceil(searchResults.length / itemsPerPage);
            document.getElementById('pageIndicator').innerText = `${page} - ${totalPages || 1}`;
            document.getElementById('btnPrev').disabled = (page === 1);
            document.getElementById('btnNext').disabled = (page === totalPages || totalPages === 0);
            paginationBox.style.display = searchResults.length > 0 ? "flex" : "none";
        }

        function ubahHalaman(arah) {
            currentPage += arah;
            renderPage(currentPage);
            document.getElementById('main-content').scrollTo({ top: 0, behavior: 'smooth' }); 
        }

        function cariData() {
            const query = keywordInput.value.trim().toUpperCase();
            if (!query) return;

            searchResults = [];
            currentPage = 1;

            const matchedPLUs = new Set();
            
            db.barcodeMap.forEach((barcodes, plu) => {
                if (plu.includes(query) || barcodes.some(b => b.includes(query))) {
                    matchedPLUs.add(plu);
                }
            });

            db.prodmast.forEach(i => {
                if ((i.PLUMD || "").toUpperCase().includes(query) || (i.DESC2 || "").toUpperCase().includes(query)) {
                    matchedPLUs.add(i.PLUMD);
                }
            });

            if (matchedPLUs.size === 0) { 
                alert("Produk Terkait Tidak Ditemukan!"); 
                resTbody.innerHTML = "";
                paginationBox.style.display = "none";
                keywordInput.value = ""; 
                return; 
            }

            const now = new Date();
            matchedPLUs.forEach(targetPLU => {
                const prod = db.prodmast.find(i => i.PLUMD === targetPLU);
                let hargaNormal = prod ? parseFloat(prod.PRICE) : 0;
                let pots = 0;

                db.promo.filter(i => (i.ITEMSYARATORI || "").includes("PLU=" + targetPLU)).forEach(i => {
                    const isAktif = (now >= parseTanggal(i.TANGGALAWAL, false) && now <= parseTanggal(i.TANGGALAKHIR, true));
                    const isNonDiscount = nonDiscountKeywords.some(k => i.MEKANISME.toUpperCase().includes(k));
                    if (isAktif && parseInt(i.QTYSYARATMIN, 10) === 1 && !isNonDiscount) {
                        pots += parseFloat(i.POTONGANRPTARGET.replace(/[^\d.-]/g, '')) || 0;
                    }
                });

                searchResults.push({ targetPLU, prod, hargaNormal, potonganSatuan: pots });
            });

            renderPage(currentPage);
            keywordInput.value = "";
            document.getElementById('main-content').scrollTo({ top: 0, behavior: 'smooth' }); 
        }

        keywordInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') cariData(); });

        let allResultsItt = [];
        let currentPageItt = 0;

        async function searchDataItt() {
            const keywordRaw = document.getElementById('keywordItt').value.trim();
            const searchTerms = keywordRaw.split(/[\s\n]+/).filter(k => k.length > 0).map(k => k.toLowerCase());
            const filterNamaRak = document.getElementById('filterNamaRakItt').value;
            const filterDariShelf = document.getElementById('filterDariShelfItt').value;
            const filterSampaiShelf = document.getElementById('filterSampaiShelfItt').value;
            const statusMessage = document.getElementById('statusMessageItt');
            
            closeModalItt();
            
            if (!isDataLoaded) {
                statusMessage.textContent = 'Sedang memuat data, silakan tunggu...';
                await initAppDatabase();
                if (!isDataLoaded) return;
            }
            
            statusMessage.textContent = 'Mencari data...';
            allResultsItt = [];

            for (let i = 0; i < db.prodmast.length; i++) {
                const item = db.prodmast[i];
                const plumd = item.PLUMD || '';
                const desc2 = item.DESC2 || '';
                const barcodes = db.barcodeMap.get(plumd) || [];
                
                let matchSearch = searchTerms.length === 0;
                if (searchTerms.length > 0) {
                    matchSearch = searchTerms.some(term => 
                        plumd.toLowerCase().includes(term) || 
                        barcodes.some(bc => bc.toLowerCase().includes(term))
                    );
                }
                
                if (matchSearch) {
                    const rakList = db.rakMap.get(plumd) || [];
                    if (rakList.length > 0) {
                        rakList.forEach(rakInfo => {
                            if (filterNamaRak && rakInfo.namaRak !== filterNamaRak) return;
                            if (filterDariShelf && rakInfo.noShelf.localeCompare(filterDariShelf, undefined, { numeric: true }) < 0) return;
                            if (filterSampaiShelf && rakInfo.noShelf.localeCompare(filterSampaiShelf, undefined, { numeric: true }) > 0) return;
                            allResultsItt.push({ 
                                plumd, 
                                barcodes, 
                                desc2, 
                                namaRak: rakInfo.namaRak, 
                                noShelf: rakInfo.noShelf, 
                                kiriKanan: rakInfo.kiriKanan 
                            });
                        });
                    } else {
                        if (!filterNamaRak && !filterDariShelf && !filterSampaiShelf) {
                            allResultsItt.push({ 
                                plumd, 
                                barcodes, 
                                desc2, 
                                namaRak: '-', 
                                noShelf: '-', 
                                kiriKanan: '-' 
                            });
                        }
                    }
                }
            }
            
            allResultsItt.sort((a, b) => {
                const compareRak = a.namaRak.localeCompare(b.namaRak, undefined, { numeric: true, sensitivity: 'base' });
                if (compareRak !== 0) return compareRak;
                const compareShelf = a.noShelf.localeCompare(b.noShelf, undefined, { numeric: true, sensitivity: 'base' });
                if (compareShelf !== 0) return compareShelf;
                return a.kiriKanan.localeCompare(b.kiriKanan, undefined, { numeric: true, sensitivity: 'base' });
            });

            if (allResultsItt.length > 0) {
                currentPageItt = 0;
                renderPageItt(true);
                openModalItt();
                statusMessage.textContent = `Ditemukan ${allResultsItt.length} data.`;
            } else {
                statusMessage.textContent = 'Data tidak ditemukan.';
            }
        }

        function renderPageItt(isInitial = false) {
            const modalBody = document.getElementById('modalBodyItt');
            if (allResultsItt.length === 0) return;
            
            if (!isInitial) {
                modalBody.classList.remove('animate');
                setTimeout(() => {
                    updateCardContentItt();
                    modalBody.classList.add('animate');
                }, 150);
            } else {
                updateCardContentItt();
                setTimeout(() => modalBody.classList.add('animate'), 50);
            }
            
            document.getElementById('pageInfoItt').textContent = `${currentPageItt + 1} dari ${allResultsItt.length}`;
            document.getElementById('prevBtnItt').disabled = currentPageItt === 0;
            document.getElementById('nextBtnItt').disabled = currentPageItt === allResultsItt.length - 1;
        }

        function updateCardContentItt() {
            const data = allResultsItt[currentPageItt];
            const barcodeId = `barcode-itt-${currentPageItt}`;
            const mainBarcode = data.barcodes.length > 0 ? data.barcodes[0] : '-';
            const hasMultipleBarcodes = data.barcodes.length > 1;
            const imageUrl = `https://cdn-klik.klikindomaret.com/klik-catalog/product/${data.plumd}_1.jpg`;
            
            let barcodeHTML = `
                <div class="barcode-container-itt">
                    <svg id="${barcodeId}"></svg>
                    <div class="barcode-text-itt">${mainBarcode}</div>
            `;
            
            if (hasMultipleBarcodes) {
                barcodeHTML += `<a class="show-all-barcodes-itt" onclick="toggleBarcodeListItt(${currentPageItt})">Tampilkan semua barcode (${data.barcodes.length})</a>`;
                barcodeHTML += `<div class="barcode-list-itt" id="barcodeListItt-${currentPageItt}">`;
                data.barcodes.forEach((bc, idx) => {
                    barcodeHTML += `
                        <div class="barcode-item-itt">
                            <svg id="${barcodeId}-${idx}"></svg>
                            <div class="barcode-text-itt">${bc}</div>
                        </div>
                    `;
                });
                barcodeHTML += `</div>`;
            }
            barcodeHTML += `</div>`;
            
            document.getElementById('modalBodyItt').innerHTML = `
                <img src="${imageUrl}" referrerpolicy="no-referrer" class="product-image-itt" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22%3E%3Crect width=%22120%22 height=%22120%22 fill=%22%23F3F4F6%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Inter%22 font-size=%2212%22 fill=%22%239CA3AF%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                <div class="plumd-value-itt">${data.plumd}</div>
                <h3 class="card-title-itt">${data.desc2}</h3>
                ${barcodeHTML}
                <div class="card-detail-itt">
                    <div class="detail-item-itt full">
                        <span class="detail-label-itt">Modis</span>
                        <span class="detail-value-itt">${data.namaRak}</span>
                    </div>
                    <div class="detail-row-itt">
                        <div class="detail-item-itt">
                            <span class="detail-label-itt">Shelfing</span>
                            <span class="detail-value-itt">${data.noShelf}</span>
                        </div>
                        <div class="detail-item-itt">
                            <span class="detail-label-itt">Baris</span>
                            <span class="detail-value-itt">${data.kiriKanan}</span>
                        </div>
                    </div>
                </div>
            `;
            
            if (mainBarcode !== '-') {
                try {
                    JsBarcode(`#${barcodeId}`, mainBarcode, {
                        format: "CODE128",
                        width: 2,
                        height: 50,
                        displayValue: false,
                        margin: 0
                    });
                } catch(e) {
                    document.getElementById(barcodeId).outerHTML = '<div style="color:#999;font-size:12px;">Barcode tidak valid</div>';
                }
            }
            
            if (hasMultipleBarcodes) {
                data.barcodes.forEach((bc, idx) => {
                    try {
                        JsBarcode(`#${barcodeId}-${idx}`, bc, {
                            format: "CODE128",
                            width: 2,
                            height: 40,
                            displayValue: false,
                            margin: 0
                        });
                    } catch(e) {
                        document.getElementById(`${barcodeId}-${idx}`).outerHTML = '<div style="color:#999;font-size:11px;">Invalid</div>';
                    }
                });
            }
        }

        function toggleBarcodeListItt(pageIdx) {
            const list = document.getElementById(`barcodeListItt-${pageIdx}`);
            const modalContent = document.getElementById('modalContentItt');
            list.classList.toggle('show');
            modalContent.classList.toggle('compact');
        }

        function changePageItt(direction) {
            const newPage = currentPageItt + direction;
            if (newPage >= 0 && newPage < allResultsItt.length) {
                currentPageItt = newPage;
                renderPageItt();
            }
        }

        function openModalItt() {
            document.getElementById('modalOverlayItt').classList.add('show');
        }

        function closeModalItt() {
            document.getElementById('modalOverlayItt').classList.remove('show');
            document.getElementById('modalBodyItt').classList.remove('animate');
            document.getElementById('modalContentItt').classList.remove('compact');
        }

        function resetSearchItt() {
            document.getElementById('keywordItt').value = '';
            document.getElementById('filterNamaRakItt').value = '';
            document.getElementById('filterDariShelfItt').value = '';
            document.getElementById('filterSampaiShelfItt').value = '';
            closeModalItt();
            allResultsItt = [];
            currentPageItt = 0;
            document.getElementById('statusMessageItt').textContent = isDataLoaded ? 'Berhasil memuat data ...' : '';
        }

        document.getElementById('modalOverlayItt').addEventListener('click', function(e) {
            if (e.target === this) closeModalItt();
        });

        let allResultsPlano = [];
        let currentScalePlano = 1;

        async function searchDataPlano() {
            const filterNamaRak = document.getElementById('filterNamaRakPlano').value;
            const filterDariShelf = document.getElementById('filterDariShelfPlano').value;
            const filterSampaiShelf = document.getElementById('filterSampaiShelfPlano').value;
            const statusMessage = document.getElementById('statusMessagePlano');
            
            if (!isDataLoaded) {
                statusMessage.textContent = 'Sedang memuat data, silakan tunggu...';
                await initAppDatabase();
                if (!isDataLoaded) return;
            }

            statusMessage.textContent = 'Mencari data...';
            allResultsPlano = [];

            for (let i = 0; i < db.prodmast.length; i++) {
                const item = db.prodmast[i];
                const plumd = item.PLUMD || '';
                const singkatan = item.SINGKATAN || item.DESC2 || '';
                
                const rakList = db.rakMap.get(plumd) || [];
                if (rakList.length > 0) {
                    rakList.forEach(rakInfo => {
                        if (filterNamaRak && rakInfo.namaRak !== filterNamaRak) return;
                        if (filterDariShelf && rakInfo.noShelf.localeCompare(filterDariShelf, undefined, { numeric: true }) < 0) return;
                        if (filterSampaiShelf && rakInfo.noShelf.localeCompare(filterSampaiShelf, undefined, { numeric: true }) > 0) return;
                        allResultsPlano.push({ 
                            plumd, 
                            singkatan, 
                            namaRak: rakInfo.namaRak, 
                            noShelf: rakInfo.noShelf, 
                            kiriKanan: rakInfo.kiriKanan 
                        });
                    });
                } else {
                    if (!filterNamaRak && !filterDariShelf && !filterSampaiShelf) {
                        allResultsPlano.push({ 
                            plumd, 
                            singkatan, 
                            namaRak: '-', 
                            noShelf: '-', 
                            kiriKanan: '-' 
                        });
                    }
                }
            }

            if (allResultsPlano.length > 0) {
                renderGroupedGridPlano();
                statusMessage.textContent = `Ditemukan ${allResultsPlano.length} data ...`;
                openModalPlano();
            } else {
                statusMessage.textContent = 'Data tidak ditemukan.';
            }
        }

        function renderGroupedGridPlano() {
            const board = document.getElementById('planogramBoard');
            board.innerHTML = '';
            
            const groups = {};
            allResultsPlano.forEach(item => {
                const shelfKey = item.noShelf;
                if (!groups[shelfKey]) {
                    groups[shelfKey] = [];
                }
                groups[shelfKey].push(item);
            });

            const sortedShelves = Object.keys(groups).sort((a, b) => {
                return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
            });

            sortedShelves.forEach(shelf => {
                const itemsInShelf = groups[shelf];
                
                itemsInShelf.sort((a, b) => {
                    const compareRak = a.namaRak.localeCompare(b.namaRak, undefined, { numeric: true, sensitivity: 'base' });
                    if (compareRak !== 0) return compareRak;
                    return a.kiriKanan.localeCompare(b.kiriKanan, undefined, { numeric: true, sensitivity: 'base' });
                });

                const uniqueRakNames = Array.from(new Set(itemsInShelf.map(i => i.namaRak).filter(n => n !== '-')));
                const rakNameText = uniqueRakNames.length > 0 ? uniqueRakNames.join(', ') : '-';

                const shelfBlock = document.createElement('div');
                shelfBlock.className = 'shelf-block';

                const shelfHeaderContainer = document.createElement('div');
                shelfHeaderContainer.className = 'shelf-header-container';

                const shelfTitle = document.createElement('div');
                shelfTitle.className = 'shelf-title';
                shelfTitle.textContent = `Shelfing : ${shelf} (${itemsInShelf.length} Item)`;
                shelfHeaderContainer.appendChild(shelfTitle);

                const shelfRakName = document.createElement('div');
                shelfRakName.className = 'shelf-rak-name';
                shelfRakName.textContent = rakNameText;
                shelfHeaderContainer.appendChild(shelfRakName);

                shelfBlock.appendChild(shelfHeaderContainer);

                const gridContainer = document.createElement('div');
                gridContainer.className = 'grid-container';

                itemsInShelf.forEach(data => {
                    const imageUrl = `https://cdn-klik.klikindomaret.com/klik-catalog/product/${data.plumd}_1.jpg`;
                    const card = document.createElement('div');
                    card.className = 'product-card-plano';
                    card.innerHTML = `
                        <img src="${imageUrl}" referrerpolicy="no-referrer" class="product-image-plano" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22%3E%3Crect width=%22120%22 height=%22120%22 fill=%22%23F3F4F6%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Inter%22 font-size=%2212%22 fill=%22%239CA3AF%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                        <div class="product-plumd-plano">${data.plumd}</div>
                        <div class="product-desc-plano">${data.singkatan}</div>
                        <div class="product-loc-plano">Baris : ${data.kiriKanan}</div>
                    `;
                    gridContainer.appendChild(card);
                });

                shelfBlock.appendChild(gridContainer);
                board.appendChild(shelfBlock);
            });
        }

        function openModalPlano() {
            document.getElementById('planoModal').style.display = 'flex';
            const canvas = document.getElementById('modalCanvasPlano');
            canvas.scrollLeft = 0;
            canvas.scrollTop = 0;
            currentScalePlano = 1;
            applyZoomPlano();
        }

        function closeModalPlano() {
            document.getElementById('planoModal').style.display = 'none';
        }

        function zoomInPlano() {
            if (currentScalePlano < 2) {
                currentScalePlano += 0.1;
                applyZoomPlano();
            }
        }

        function zoomOutPlano() {
            if (currentScalePlano > 0.4) {
                currentScalePlano -= 0.1;
                applyZoomPlano();
            }
        }

        function applyZoomPlano() {
            const board = document.getElementById('planogramBoard');
            board.style.transform = `scale(${currentScalePlano})`;
        }

        function resetSearchPlano() {
            document.getElementById('filterNamaRakPlano').value = '';
            document.getElementById('filterDariShelfPlano').value = '';
            document.getElementById('filterSampaiShelfPlano').value = '';
            document.getElementById('planogramBoard').innerHTML = '';
            allResultsPlano = [];
            document.getElementById('statusMessagePlano').textContent = isDataLoaded ? 'Silakan pilih filter dan klik Tampilkan.' : '';
        }

        const sliderPlano = document.getElementById('modalCanvasPlano');
        let isDownPlano = false;
        let startXPlano;
        let startYPlano;
        let scrollLeftPlano;
        let scrollTopPlano;

        sliderPlano.addEventListener('mousedown', (e) => {
            if (e.target.closest('.product-card-plano') || e.target.tagName === 'BUTTON') return;
            isDownPlano = true;
            startXPlano = e.pageX - sliderPlano.offsetLeft;
            startYPlano = e.pageY - sliderPlano.offsetTop;
            scrollLeftPlano = sliderPlano.scrollLeft;
            scrollTopPlano = sliderPlano.scrollTop;
        });

        sliderPlano.addEventListener('mouseleave', () => { isDownPlano = false; });
        sliderPlano.addEventListener('mouseup', () => { isDownPlano = false; });

        sliderPlano.addEventListener('mousemove', (e) => {
            if (!isDownPlano) return;
            e.preventDefault();
            const x = e.pageX - sliderPlano.offsetLeft;
            const y = e.pageY - sliderPlano.offsetTop;
            const walkX = (x - startXPlano) * 1.5;
            const walkY = (y - startYPlano) * 1.5;
            sliderPlano.scrollLeft = scrollLeftPlano - walkX;
            sliderPlano.scrollTop = scrollTopPlano - walkY;
        });

        let qrItems = [];
        let currentQrIndex = 0;

        function handleFileUpload(input) {
            let file = input.files[0];
            if (!file) return;

            let reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("text-input-qr").value = e.target.result;
            };
            reader.readAsText(file);
        }

        function formatQrText(text) {
            if (text.includes(':')) {
                let parts = text.split(':');
                let before = parts[0];
                let after = parts[1];
                let paddedAfter = after.padStart(4, '0');
                return 'B' + before + paddedAfter;
            }
            return text;
        }

        function generateBulkQR() {
            let input = document.getElementById("text-input-qr").value;
            
            if (input.trim() === "") {
                alert("Silakan masukkan teks atau upload file terlebih dahulu!");
                return;
            }

            qrItems = input.split(/[\s\n,]+/).filter(item => item.trim() !== "");

            if (qrItems.length === 0) {
                alert("Silakan masukkan teks atau upload file terlebih dahulu!");
                return;
            }

            currentQrIndex = 0;
            openModalQR();
            renderCurrentQR();
        }

        function renderCurrentQR() {
            let rawItem = qrItems[currentQrIndex];
            let processedItem = formatQrText(rawItem);
            
            let qrContainer = document.getElementById("qrcode");
            let qrLabel = document.getElementById("qrLabel");
            
            qrContainer.classList.remove("fade-in");
            qrContainer.classList.add("fade-out");
            qrLabel.style.opacity = 0;

            setTimeout(() => {
                qrContainer.innerHTML = "";
                qrLabel.innerText = processedItem;
                document.getElementById("qrCounter").innerText = (currentQrIndex + 1) + " dari " + qrItems.length;

                new QRCode(qrContainer, {
                    text: processedItem,
                    width: 200,
                    height: 200
                });

                qrContainer.classList.remove("fade-out");
                qrContainer.classList.add("fade-in");
                qrLabel.style.opacity = 1;
            }, 150);
        }

        function nextQR() {
            if (currentQrIndex < qrItems.length - 1) {
                currentQrIndex++;
                renderCurrentQR();
            } else {
                alert("Ini QRCode terakhir ...");
            }
        }

        function prevQR() {
            if (currentQrIndex > 0) {
                currentQrIndex--;
                renderCurrentQR();
            } else {
                alert("Ini QRCode pertama ...");
            }
        }

        function openModalQR() {
            let modal = document.getElementById("qrModal");
            modal.style.display = "flex";
            setTimeout(() => {
                modal.classList.add("show");
            }, 10);
        }

        function closeModalQR() {
            let modal = document.getElementById("qrModal");
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300);
        }

        document.getElementById("qrModal").addEventListener("click", function(e) {
            if (e.target === this) {
                closeModalQR();
            }
        });

        function resetFormQR() {
            document.getElementById("text-input-qr").value = "";
            document.getElementById("file-input").value = "";
            closeModalQR();
            qrItems = [];
            currentQrIndex = 0;
        }
    </script>
</body>
</html>