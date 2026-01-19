<?php
// cloud_download.php - THE VEGAMOVIES CLOUD UI
require_once 'common/config.php';

$file = $_GET['file'] ?? '';
$title = $_GET['title'] ?? 'Unknown File';
$filepath = __DIR__ . '/' . $file;

// Calculate File Size
$filesize = "Unknown Size";
if(file_exists($filepath)) {
    $bytes = filesize($filepath);
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    $filesize = round($bytes, 2) . ' ' . $units[$pow];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>V-Cloud - Secure Download</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0d1117; color: #c9d1d9; font-family: sans-serif; }
        .cloud-card { background: #161b22; border: 1px solid #30363d; }
        .btn-gen { background: #238636; color: white; }
        .btn-dl { background: #1f6feb; color: white; display: none; }
        
        /* Loading Animation */
        .loader { border: 3px solid #f3f3f3; border-top: 3px solid #3498db; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite; display: inline-block; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="cloud-card w-full max-w-md p-6 rounded-lg shadow-2xl text-center">
        <!-- HEADER -->
        <div class="mb-6 border-b border-gray-700 pb-4">
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-cloud text-blue-500"></i> V-CLOUD</h1>
            <p class="text-xs text-gray-500">Fast & Secure Cloud Storage</p>
        </div>

        <!-- FILE INFO -->
        <div class="bg-[#0d1117] p-4 rounded mb-6 border border-gray-700 text-left">
            <p class="text-sm text-gray-400">File Name:</p>
            <h3 class="text-white font-bold truncate mb-2"><?= htmlspecialchars($title) ?>.mp4</h3>
            
            <div class="flex justify-between text-xs text-gray-500">
                <span>Size: <b class="text-green-400"><?= $filesize ?></b></span>
                <span>Type: <b class="text-blue-400">MP4 Video</b></span>
            </div>
        </div>

        <!-- VIRUS SCAN MOCKUP -->
        <div id="scan-box" class="mb-6 text-yellow-500 text-sm font-mono hidden">
            <div class="loader mr-2 align-middle"></div> Scanning for viruses...
        </div>
        
        <div id="safe-msg" class="mb-6 text-green-500 text-sm font-bold hidden">
            <i class="fas fa-check-circle"></i> File is Safe. 100% Clean.
        </div>

        <!-- BUTTONS -->
        <button id="btn-1" onclick="startProcess()" class="btn-gen w-full py-3 rounded font-bold shadow-lg hover:bg-green-700 transition">
            GENERATE DIRECT LINK
        </button>

        <a href="download.php?file=<?= urlencode($file) ?>" id="btn-2" class="btn-dl w-full py-3 rounded font-bold shadow-lg hover:bg-blue-600 transition text-center block">
            <i class="fas fa-download"></i> DOWNLOAD NOW
        </a>

        <p class="mt-4 text-[10px] text-gray-600">
            By downloading you agree to our Terms of Service. <br> Server IP: <?= $_SERVER['SERVER_ADDR'] ?>
        </p>
    </div>

    <script>
        function startProcess() {
            let btn1 = document.getElementById('btn-1');
            let scan = document.getElementById('scan-box');
            let safe = document.getElementById('safe-msg');
            let btn2 = document.getElementById('btn-2');

            // Step 1: Hide Button, Show Scan
            btn1.style.display = 'none';
            scan.style.display = 'block';

            // Step 2: Wait 3 seconds (Fake Scan)
            setTimeout(() => {
                scan.style.display = 'none';
                safe.style.display = 'block';
                
                // Step 3: Show Download
                setTimeout(() => {
                    btn2.style.display = 'block';
                }, 500);
            }, 2500);
        }
    </script>
</body>
</html>