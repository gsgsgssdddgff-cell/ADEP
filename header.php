<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Adept Cinema</title>
    
    <!-- PWA & MANIFEST -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #050505; color: white; -webkit-tap-highlight-color: transparent; user-select: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        /* NO INTERNET SCREEN */
        #no-internet-screen {
            display: none; 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: #202124; z-index: 999999;
            flex-direction: column; align-items: center; justify-content: center;
            text-align: center; font-family: sans-serif;
        }
        .retry-btn {
            background-color: #8ab4f8; color: #202124;
            padding: 10px 24px; border-radius: 4px;
            font-weight: bold; margin-top: 20px;
            border: none; cursor: pointer;
        }
    </style>

    <script>
        if ('serviceWorker' in navigator) { navigator.serviceWorker.register('sw.js'); }

        // --- 1. STAY IN APP SCRIPT ---
        document.addEventListener('click', function(e) {
            var target = e.target;
            while (target && target.tagName !== 'A') { target = target.parentNode; }
            
            if (target && target.href) {
                if (target.getAttribute('target') === '_blank' || 
                    target.hasAttribute('download') || 
                    target.href.includes('cloud_download.php') || 
                    target.href.includes('payment.php')) {
                    return;
                }
                e.preventDefault();
                window.location.href = target.href;
            }
        });

        // --- 2. REAL INTERNET CHECK (HEARTBEAT) ---
        function checkInternet() {
            if (!navigator.onLine) {
                showOffline(); // WiFi/Data is completely OFF
            } else {
                // WiFi connected but maybe No Internet? Let's Ping Google Image
                var img = new Image();
                img.onload = function() { hideOffline(); }; // Internet working
                img.onerror = function() { 
                    // Note: Localhost might block this ping, but real internet is needed for Tailwind CDN etc.
                    // If you want strict mode: showOffline(); 
                    // But on local server, this is risky. Stick to navigator.onLine for Basic check.
                };
                img.src = "https://www.google.com/favicon.ico?t=" + new Date().getTime();
            }
        }

        // INSTANT EVENT LISTENERS
        window.addEventListener('offline', function() { showOffline(); });
        window.addEventListener('online', function() { hideOffline(); });

        function showOffline() {
            document.getElementById('no-internet-screen').style.display = 'flex';
        }

        function hideOffline() {
            document.getElementById('no-internet-screen').style.display = 'none';
        }

        // Run check immediately
        window.onload = checkInternet;
    </script>
</head>
<body>

    <!-- 🚫 REAL CHROME ERROR SCREEN -->
    <div id="no-internet-screen">
        <i class="fas fa-wifi text-6xl text-gray-500 mb-6 animate-pulse"></i>
        <h1 class="text-xl font-bold text-gray-300">No Internet Connection</h1>
        <p class="text-sm text-gray-500 mt-2">Data/Wi-Fi is off.</p>
        
        <div class="mt-8 border border-gray-600 rounded px-4 py-2 text-xs text-gray-400 font-mono">
            ERR_INTERNET_DISCONNECTED
        </div>

        <button onclick="window.location.reload()" class="retry-btn">Try Again</button>
    </div>

    <!-- MAIN APP STARTS HERE... -->