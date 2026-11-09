<?php
header("HTTP/1.1 503 Service Unavailable");
header("Retry-After: 3600");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sedang Dalam Pemeliharaan</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #faf9f5;
            color: #191919;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            max-width: 420px;
            padding: 40px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #cc785c;
            background-color: #fdf3f0;
            padding: 6px 12px;
            border-radius: 20px;
            margin-bottom: 24px;
        }
        .dot {
            width: 6px;
            height: 6px;
            background-color: #cc785c;
            border-radius: 50%;
        }
        h1 {
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }
        p {
            font-size: 15px;
            line-height: 1.6;
            color: #666666;
            margin: 0 0 24px 0;
        }
        .footer {
            font-size: 13px;
            color: #888888;
            border-top: 1px solid #e6e4dc;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">
            <span class="dot"></span>
            Pemeliharaan Sistem
        </div>
        <h1>Halaman sedang Maintenance 😁</h1>
        <p></p>
        <div class="footer">
            ~ m.h.r ~
        </div>
    </div>
</body>
</html>