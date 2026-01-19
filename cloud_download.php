<?php
require_once 'common/config.php';

// 1. GET PARAMETERS
$file_param = $_GET['file'] ?? '';
$movie_id = $_GET['id'] ?? 0;

// Security Sanitize
$file_param = str_replace('../', '', $file_param);
$filepath = __DIR__ . '/' . $file_param;

// 2. FETCH MOVIE DATA
$m = $pdo->query("SELECT title, file_size_label FROM movies WHERE id=$movie_id")->fetch();

if(!$m && !file_exists($filepath)) {
    die("<body style='background:black;color:red;display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;'><h1>❌ 404 FILE NOT FOUND</h1></body>");
}

// 3. GENERATE BRANDED NAME
// e.g. Original: 17384_vid.mp4 -> New: [Adept-Cinema]_Iron_Man.mp4
$clean_title = preg_replace('/[^A-Za-z0-9]/', '_', $m['title']);
$download_name = "[Adept-Cinema]_" . $clean_title . ".mp4";

// 4. GET SIZE
$size_text = "Checking...";
if (!empty($m['file_size_label'])) {
    $size_text = $m['file_size_label']; // Creator ka likha hua size (e.g. 1GB)
} elseif (file_exists($filepath)) {
    $size_text = round(filesize($filepath) / (1024 * 1024), 2) . " MB"; // Auto size
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloading: <?= $m['title'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; color: white; font-family: 'Segoe UI', sans-serif; }
        .scanner-line { height: 100%; background: linear-gradient(90deg, #3b82f6, #06b6d4); width: 0%; transition: width 0.1s linear; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-sm bg-[#1e293b] border border-gray-700 rounded-2xl shadow-2xl overflow-hidden relative">
        
        <!-- HEADER -->
        <div class="bg-[#0f172a] p-4 border-b border-gray-700 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-blue-500 font-black text-xl">ADEPT</span>
                <span class="font-bold text-gray-300 text-sm">SECURE CLOUD</span>
            </div>
            <i class="fas fa-lock text-green-500 text-sm"></i>
        </div>

        <div class="p-8 text-center">
            
            <!-- FILE ICON -->
            <div class="mb-6 relative inline-block">
                <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center border-4 border-gray-700" id="iconBox">
                    <i class="fas fa-file-video text-5xl text-blue-400" id="mainIcon"></i>
                </div>
                <div class="absolute -bottom-2 -right-2 bg-black text-xs font-mono px-2 py-1 rounded border border-gray-600">MP4</div>
            </div>

            <!-- INFO -->
            <h2 class="text-xl font-bold text-white leading-tight mb-2"><?= htmlspecialchars($m['title']) ?></h2>
            <p class="text-sm text-gray-400 font-mono mb-8"><?= $size_text ?></p>

            <!-- PROCESS: STEP 1 (SCANNING) -->
            <div id="scanUI">
                <div class="flex justify-between text-[10px] text-blue-300 font-bold uppercase mb-2">
                    <span id="scanText">Initializing...</span>
                    <span id="scanPercent">0%</span>
                </div>
                <div class="w-full bg-gray-800 h-3 rounded-full overflow-hidden border border-gray-700">
                    <div class="scanner-line" id="scanBar"></div>
                </div>
                <p class="text-[10px] text-gray-500 mt-2">Connecting to Adept Servers...</p>
            </div>

            <!-- PROCESS: STEP 2 (DOWNLOAD BUTTON) -->
            <div id="downloadUI" class="hidden animate-bounce-in">
                <div class="bg-green-900/20 border border-green-500/30 p-3 rounded-xl mb-4 flex items-center gap-3 justify-center">
                    <i class="fas fa-shield-check text-green-400 text-lg"></i>
                    <div class="text-left">
                        <p class="text-xs text-green-400 font-bold">File Scanned & Safe</p>
                        <p class="text-[9px] text-gray-400">Zero Malware Detected</p>
                    </div>
                </div>

                <!-- 🚀 REAL DOWNLOAD LINK -->
                <a href="download.php?file=<?= urlencode($file_param) ?>&name=<?= urlencode($download_name) ?>" class="block w-full bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-500 hover:to-blue-700 text-white py-4 rounded-xl font-bold text-lg shadow-xl border-b-4 border-blue-900 active:border-b-0 active:translate-y-1 transition-all">
                    START DOWNLOAD <i class="fas fa-download ml-2"></i>
                </a>

                <p class="text-[10px] text-gray-500 mt-4 break-all">
                    Save as: <span class="text-gray-300"><?= $download_name ?></span>
                </p>
            </div>

        </div>
    </div>

    <!-- SCANNING ANIMATION SCRIPT -->
    <script>
        const bar = document.getElementById('scanBar');
        const txt = document.getElementById('scanPercent');
        const status = document.getElementById('scanText');
        const iconBox = document.getElementById('iconBox');
        
        let width = 0;
        let speed = 40; // milliseconds

        let interval = setInterval(() => {
            width += 1; // Increase by 1%
            
            // Text Updates based on progress
            if(width == 20) status.innerText = "Scanning File...";
            if(width == 50) status.innerText = "Verifying Hash...";
            if(width == 80) status.innerText = "Generating Link...";

            if (width >= 100) {
                width = 100;
                clearInterval(interval);
                showDownloadButton();
            }

            bar.style.width = width + "%";
            txt.innerText = width + "%";
        }, speed);

        function showDownloadButton() {
            setTimeout(() => {
                document.getElementById('scanUI').classList.add('hidden');
                document.getElementById('downloadUI').classList.remove('hidden');
                
                // Icon Glow Effect
                iconBox.classList.replace('border-gray-700', 'border-green-500');
                iconBox.classList.add('shadow-[0_0_20px_rgba(34,197,94,0.5)]');
                document.getElementById('mainIcon').classList.replace('text-blue-400', 'text-green-400');
                document.getElementById('mainIcon').classList.replace('fa-file-video', 'fa-check');
            }, 500);
        }
    </script>
</body>
</html>