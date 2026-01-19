<?php require_once 'common/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background: #000; color: white; }</style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-6 text-center bg-[url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_small.jpg')] bg-cover">

    <div class="absolute inset-0 bg-black/90"></div>

    <div class="relative z-10 w-full max-w-sm">
        <div class="bg-[#111] border border-red-900 p-8 rounded-3xl shadow-2xl">
            <img src="https://cdn-icons-png.flaticon.com/512/2503/2503508.png" class="w-24 h-24 mx-auto mb-4 rounded-xl shadow-lg border border-gray-700">
            
            <h1 class="text-3xl font-black text-red-600 mb-1">ADEPT CINEMA</h1>
            <p class="text-gray-400 text-xs tracking-widest uppercase">The Future of Streaming</p>

            <div class="mt-6 space-y-2 text-sm text-left px-4">
                <p><i class="fas fa-check text-green-500 mr-2"></i> 4K HDR & Dolby Audio</p>
                <p><i class="fas fa-check text-green-500 mr-2"></i> No Ads for Premium</p>
                <p><i class="fas fa-check text-green-500 mr-2"></i> 30GB+ File Support</p>
            </div>

            <!-- DOWNLOAD BUTTON -->
            <a href="download_apk.php" class="block w-full bg-gradient-to-r from-red-600 to-red-900 hover:from-red-500 hover:to-red-800 text-white py-4 rounded-full font-bold text-lg mt-8 shadow-xl transform transition hover:scale-105">
                <i class="fab fa-android mr-2"></i> DOWNLOAD APP
            </a>

            <p class="text-[10px] text-gray-500 mt-4">Version 2.0 • 15 MB • 100% Safe</p>
        </div>
        
        <a href="index.php" class="block mt-6 text-gray-400 text-sm hover:text-white">Continue to Website ></a>
    </div>

</body>
</html>