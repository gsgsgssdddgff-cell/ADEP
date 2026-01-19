<?php $file = $_GET['file'] ?? ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Link</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { background: #000; color: #fff; } </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-6 text-center">

    <h2 class="text-xl font-bold mb-1">Generating Link</h2>
    <p class="text-xs text-gray-500 mb-8">Please wait while we prepare your high-speed link.</p>

    <!-- FAKE AD CARD -->
    <div class="bg-[#111] border border-gray-800 p-4 rounded-xl w-full max-w-sm mb-8 relative overflow-hidden">
        <span class="absolute top-2 right-2 bg-gray-800 text-[8px] px-1 rounded text-gray-400">AD</span>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center font-bold text-xl">V</div>
            <div class="text-left">
                <h3 class="font-bold text-sm">Fast VPN Proxy</h3>
                <p class="text-[10px] text-gray-400">Secure your browsing instantly.</p>
            </div>
        </div>
        <button class="w-full mt-4 bg-gray-800 py-2 rounded text-xs font-bold text-blue-400">Install Now</button>
    </div>

    <!-- TIMER CIRCLE -->
    <div class="relative w-20 h-20 flex items-center justify-center mb-8">
        <svg class="w-full h-full transform -rotate-90">
            <circle cx="40" cy="40" r="36" stroke="#222" stroke-width="4" fill="none" />
            <circle id="progress" cx="40" cy="40" r="36" stroke="#E50914" stroke-width="4" fill="none" stroke-dasharray="226" stroke-dashoffset="0" />
        </svg>
        <span id="timerText" class="absolute font-bold text-xl">5</span>
    </div>

    <button id="contBtn" onclick="location.href='final_download.php?file=<?= urlencode($file) ?>'" class="hidden w-full max-w-sm bg-white text-black font-bold py-4 rounded-xl shadow-[0_0_20px_rgba(255,255,255,0.3)] animate-bounce">
        GET DOWNLOAD LINK
    </button>

    <script>
        let time = 5;
        const circle = document.getElementById('progress');
        const offset = 226;
        
        let interval = setInterval(() => {
            time--;
            document.getElementById('timerText').innerText = time;
            circle.style.strokeDashoffset = offset - (time / 5) * offset;
            
            if(time <= 0) {
                clearInterval(interval);
                document.getElementById('contBtn').classList.remove('hidden');
                document.getElementById('timerText').innerHTML = "<i class='fas fa-check'></i>";
            }
        }, 1000);
    </script>
</body>
</html>