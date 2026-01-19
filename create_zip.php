<?php
session_start();
// CLEAR BUFFERS
while (ob_get_level()) ob_end_clean();
require_once '../common/config.php';

$key = $_GET['key'] ?? '';
$id = $_GET['id'] ?? 0;

if ($key !== "kya tum ya hai") { 
    die("<body style='background:black;color:red;display:flex;justify-content:center;align-items:center;height:100vh;font-size:30px;'>🚫 ACCESS DENIED</body>"); 
}

// Logic Code is inside backup_action.php. This page is purely UI.
// But if user requested execution via internal fetch:
if (isset($_GET['run_real_backup'])) {
    // Forward to backup_action logic
    include 'backup_action.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Secure Backup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body{background:#050505; color:#0f0; font-family:'Courier New', monospace; display:flex; flex-direction:column; align-items:center; justify-content:center; height:100vh;}
        
        .grid-bg { 
            position: absolute; top:0; left:0; width:100%; height:100%; 
            background-image: radial-gradient(circle, #0f0 1px, transparent 1px);
            background-size: 30px 30px; opacity: 0.1; z-index:-1;
        }

        .loader-ring { width:80px; height:80px; border:4px solid #003300; border-top:4px solid #0f0; border-radius:50%; animation:spin 1s linear infinite; margin-bottom:20px; box-shadow:0 0 15px #0f0;}
        @keyframes spin {0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}

        .terminal { width:90%; max-width:500px; height:200px; background:#000; border:1px solid #333; padding:15px; overflow:hidden; position:relative; box-shadow:0 0 20px rgba(0,255,0,0.1); border-radius:10px; text-align:left;}
        .log-item { opacity:0; animation:fadeIn 0.2s forwards; margin-bottom:4px; font-size:12px; }
        @keyframes fadeIn {from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
    </style>
</head>
<body>

    <div class="grid-bg"></div>

    <div id="processing-ui">
        <div class="loader-ring"></div>
        <h1 class="text-2xl font-bold mb-6 text-white animate-pulse">CREATING SECURE BACKUP</h1>
        
        <div class="terminal" id="console">
            <!-- Logs will appear here -->
        </div>

        <div class="w-full max-w-[500px] bg-gray-900 h-2 mt-6 rounded-full border border-green-900">
            <div id="bar" class="h-full bg-green-500 w-0 transition-all duration-300 shadow-[0_0_10px_#0f0]"></div>
        </div>
        <p id="percent" class="mt-2 text-xs text-green-700">0%</p>
    </div>

    <!-- SUCCESS SCREEN (Hidden) -->
    <div id="success-ui" class="hidden text-center z-10">
        <i class="fas fa-check-circle text-6xl text-green-500 mb-4 shadow-[0_0_30px_#0f0]"></i>
        <h2 class="text-3xl font-bold text-white mb-2">BACKUP COMPLETED</h2>
        <p class="text-gray-400 mb-8">Data & Media saved to Vault.</p>
        <button onclick="window.location.href='manage_backups.php'" class="bg-green-600 text-black px-8 py-3 rounded-lg font-bold hover:bg-green-500 shadow-lg shadow-green-900/50">OPEN VAULT</button>
    </div>

    <!-- FontAwesome for Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        const logs = document.getElementById('console');
        const bar = document.getElementById('bar');
        const msgs = [
            "Initiating root access...",
            "Key Verified: 'kya tum ya hai'",
            "Scanning Database ID: <?= $id ?>...",
            "Locating Media Files...",
            "Detected: Large Video File...",
            "Bypassing Memory Limits...",
            "Starting Safe-Copy Protocol...",
            "Transferring Data to /saved_movies/...",
            "Finalizing Checksums..."
        ];

        let i = 0;
        // FAST LOG ANIMATION
        let t = setInterval(() => {
            if(i < msgs.length) {
                logs.innerHTML += `<div class='log-item'>> ${msgs[i]}</div>`;
                logs.scrollTop = logs.scrollHeight;
                let pct = ((i+1)/msgs.length * 90);
                bar.style.width = pct + "%";
                document.getElementById('percent').innerText = Math.floor(pct) + "%";
                i++;
            } else {
                clearInterval(t);
                logs.innerHTML += `<div class='log-item text-yellow-500'>> WAITING FOR SERVER RESPONSE...</div>`;
                logs.scrollTop = logs.scrollHeight;
                
                // CALL PHP TO DO ACTUAL WORK
                fetch("backup_action.php?id=<?= $id ?>&key=<?= urlencode($key) ?>&execute=1")
                .then(r => r.text())
                .then(res => {
                    if (res.includes("SUCCESS")) {
                        bar.style.width = "100%";
                        document.getElementById('percent').innerText = "100%";
                        document.getElementById('processing-ui').classList.add('hidden');
                        document.getElementById('success-ui').classList.remove('hidden');
                    } else {
                        alert("❌ Server Error: " + res);
                        window.location.href = "movies.php";
                    }
                })
                .catch(e => {
                    alert("Connection Error!");
                });
            }
        }, 600); // 0.6 sec delay per log
    </script>

</body>
</html>