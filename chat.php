<?php
$host = getenv("DB_HOST") ?: "db.fr-pari1.bengt.wasmernet.com";
$port = getenv("DB_PORT") ?: "10272";
$user = getenv("DB_USER") ?: "880be7197ad2800032d03b1d193a";
$pass = getenv("DB_PASS") ?: "0698880b-e719-7c40-8000-49ea315d6dfa";
$db   = getenv("DB_NAME") ?: "database_mharyrafli";

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (isset($_GET['action']) && $_GET['action'] == 'fetch') {
    $query = mysqli_query($conn, "SELECT * FROM chat ORDER BY id DESC LIMIT 30");
    $chats = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $chats[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode(array_reverse($chats));
    exit;
}

if (isset($_POST['send_msg'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);
    if (!empty($nama) && !empty($pesan)) {
        mysqli_query($conn, "INSERT INTO chat (nama, pesan) VALUES ('$nama', '$pesan')");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Room Chat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            height: 100vh;
            color: #334155;
            overflow: hidden;
        }

        .chat-container { 
            flex: 1;
            width: 100%; 
            height: 100%; 
            display: flex; 
            flex-direction: column; 
            background: #fff; 
            overflow: hidden;
            position: relative;
        }

        .chat-box {
            flex: 1; 
            overflow-y: auto; 
            padding: 15px;
            display: flex; 
            flex-direction: column; 
            gap: 12px;
            background-color: #f5f7fa;
            scroll-behavior: smooth;
        }

        .chat-bubble { max-width: 85%; align-self: flex-start; }
        .chat-user { font-size: 11px; font-weight: bold; color: #f97316; margin-bottom: 2px; text-transform: uppercase; }

        .chat-text {
            background: #ffffff;
            padding: 10px 14px;
            border-radius: 15px;
            border-top-left-radius: 2px;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e1e8ed;
            word-break: break-word;
        }

        .chat-input-area { 
            padding: 12px 15px; 
            background: #ffffff; 
            border-top: 2px solid #e1e8ed;
            flex-shrink: 0;
        }

        .input-card { background: #f5f7fa; border: 1px solid #e1e8ed; border-radius: 14px; padding: 10px; }
        .input-card input {
            width: 100%; border: none; background: transparent; font-size: 12px;
            font-weight: bold; padding-bottom: 6px; margin-bottom: 6px;
            border-bottom: 1px solid #e1e8ed; outline: none; color: #007bff;
            font-family: inherit;
        }

        .input-row { display: flex; gap: 10px; align-items: flex-end; }
        .input-row textarea { flex-grow: 1; border: none; background: transparent; resize: none; font-family: inherit; font-size: 14px; outline: none; max-height: 80px; }

        .send-btn {
            background: #007bff; color: white; border: none; border-radius: 10px;
            width: 40px; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            transition: background 0.2s;
        }

        .send-btn:hover {
            background: #0056b3;
        }

        #scroll-hint {
            position: absolute; bottom: 110px; left: 50%; transform: translateX(-50%);
            background: #f97316; color: white; padding: 6px 12px; border-radius: 20px;
            font-size: 10px; font-weight: bold; cursor: pointer; display: none; z-index: 50;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

    <div class="chat-container">
        <div id="scroll-hint" onclick="scrollToBottom()">PESAN BARU ↓</div>
        <div class="chat-box" id="chatBox"></div>

        <div class="chat-input-area">
            <form id="chatForm" class="input-card">
                <input type="text" id="nama" placeholder="Nama" required>
                <div class="input-row">
                    <textarea id="pesan" placeholder="Tulis Pesan" rows="1" required></textarea>
                    <button type="submit" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const scrollHint = document.getElementById('scroll-hint');
        let isUserScrolling = false;

        chatBox.addEventListener('scroll', () => {
            const threshold = 100;
            const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < threshold;
            isUserScrolling = !isAtBottom;
            if (!isUserScrolling) scrollHint.style.display = 'none';
        });

        function scrollToBottom() {
            isUserScrolling = false;
            chatBox.scrollTop = chatBox.scrollHeight;
            scrollHint.style.display = 'none';
        }

        async function fetchChats() {
            try {
                const response = await fetch('chat.php?action=fetch');
                const data = await response.json();
                const currentCount = chatBox.children.length;
                
                chatBox.innerHTML = data.map(chat => `
                    <div class="chat-bubble">
                        <div class="chat-user">${chat.nama}</div>
                        <div class="chat-text">${chat.pesan}</div>
                    </div>
                `).join('');

                if (!isUserScrolling) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                } else if (data.length > currentCount && currentCount > 0) {
                    scrollHint.style.display = 'block';
                }
            } catch (e) { }
        }

        document.getElementById('chatForm').onsubmit = async (e) => {
            e.preventDefault();
            const nama = document.getElementById('nama').value;
            const msgInput = document.getElementById('pesan');
            
            localStorage.setItem('user_nama', nama);

            const fd = new FormData();
            fd.append('send_msg', '1');
            fd.append('nama', nama);
            fd.append('pesan', msgInput.value);
            
            msgInput.value = '';
            isUserScrolling = false; 
            await fetch('chat.php', { method: 'POST', body: fd });
            fetchChats();
        };

        const savedNama = localStorage.getItem('user_nama');
        if(savedNama) document.getElementById('nama').value = savedNama;
        
        setInterval(fetchChats, 3000);
        fetchChats();
    </script>
</body>
</html>