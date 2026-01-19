<?php
session_start();

// 1. REDIRECT IF NEW USER
if (!isset($_SESSION['user_id'])) { header("Location: welcome.php"); exit; }

require_once 'common/config.php';
$user_id = $_SESSION['user_id'];

// 2. ☠️ BAN CHECKER
try {
    $u = $pdo->prepare("SELECT is_banned, ban_expiry FROM users WHERE uid = ?");
    $u->execute([$user_id]);
    $u = $u->fetch();

    if ($u && $u['is_banned'] == 1) {
        if ($u['ban_expiry'] != '9999-12-31 23:59:59' && new DateTime() > new DateTime($u['ban_expiry'])) {
            $pdo->prepare("UPDATE users SET is_banned = 0 WHERE uid = ?")->execute([$user_id]);
            header("Refresh:0"); exit;
        } else {
            $time_left = ($u['ban_expiry'] == '9999-12-31 23:59:59') ? "FOREVER" : (new DateTime($u['ban_expiry']))->diff(new DateTime())->format('%d D %H:%I:%S');
            die("<body style='background:black;color:#0f0;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;margin:0;font-family:monospace;'><div style='font-size:80px;'>☠️</div><h1>SYSTEM LOCKED</h1><p style='color:red;'>YOU ARE BANNED</p><br><span>TIME LEFT: $time_left</span></body>");
        }
    }
} catch(Exception $e) {}

// --- DATA FETCHING ---
$cats = $pdo->query("SELECT DISTINCT c.id, c.name FROM categories c JOIN movies m ON c.id = m.category_id WHERE m.status='approved' GROUP BY c.id ORDER BY c.name ASC")->fetchAll();
$slider_movies = $pdo->query("SELECT * FROM movies WHERE status='approved' ORDER BY id DESC LIMIT 5")->fetchAll();
$raw_recent = $pdo->query("SELECT * FROM movies WHERE status='approved' ORDER BY id DESC LIMIT 40")->fetchAll();

$recent_movies = [];
$seen_series = [];

foreach($raw_recent as $rm) {
    $base_name = explode(' - Ep', $rm['title'])[0]; 
    $base_name = explode(' - Episode', $base_name)[0]; 

    if(!in_array($base_name, $seen_series)) {
        $rm['display_title'] = $base_name;
        $recent_movies[] = $rm;            
        $seen_series[] = $base_name;       
    }
    if(count($recent_movies) >= 10) break; 
}

$notif_count = 0; $msg_count = 0;
if ($user_id) { 
    try { 
        $notif_count = $pdo->query("SELECT COUNT(*) FROM transactions WHERE username='$user_id' AND status='APPROVED' AND viewed=0")->fetchColumn();
        $msg_count = $pdo->query("SELECT COUNT(*) FROM admin_messages")->fetchColumn();
    } catch(Exception $e){} 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Adept Cinema</title>
    
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#e50914">
    <meta name="mobile-web-app-capable" content="yes">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #050505; color: white; -webkit-tap-highlight-color: transparent; user-select: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .fade-anim { animation: fadeEffect 1s; }
        @keyframes fadeEffect { from {opacity: 0.4;} to {opacity: 1;} }

        /* TELEGRAM FLOAT BUTTON */
        .tg-float {
            position: fixed; bottom: 85px; right: 20px; z-index: 100;
            background: linear-gradient(45deg, #0088cc, #00aaff);
            color: white; padding: 12px 18px; border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 136, 204, 0.5);
            display: flex; align-items: center; gap: 8px; font-weight: bold; font-size: 12px;
            animation: bounce 3s infinite; text-decoration: none;
        }
        @keyframes bounce { 0%, 100% {transform: translateY(0);} 50% {transform: translateY(-5px);} }

        /* CLICK ANIMATION */
        #tap-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
            z-index: 99999; display: none; align-items: center; justify-content: center;
        }
        #tap-icon { font-size: 0px; color: #fff; transition: 0.4s; opacity: 0; }
        .show-tap #tap-icon { font-size: 80px; opacity: 1; transform: scale(1.2); }
    </style>

    <script>
        if ('serviceWorker' in navigator) { navigator.serviceWorker.register('sw.js'); }

        document.addEventListener('click', function(e) {
            let target = e.target;
            while (target && target.tagName !== 'A') { target = target.parentNode; }
            
            if (target && target.href) {
                // ✅ TELEGRAM FIX: This allows your specific link to open
                if (target.href.includes('t.me') || target.href.includes('streamadept')) return;

                if (target.getAttribute('target') === '_blank' || 
                    target.hasAttribute('download') || 
                    target.href.includes('cloud_download.php') || 
                    target.href.includes('payment.php')) { 
                    return; 
                }
                
                if(target.href.includes('movie_details.php')) {
                    e.preventDefault(); 
                    let ov = document.getElementById('tap-overlay'); ov.style.display='flex';
                    void ov.offsetWidth; ov.classList.add('show-tap');
                    setTimeout(() => { window.location.href = target.href; }, 400); 
                } else {
                    e.preventDefault(); 
                    window.location.href = target.href;
                }
            }
        });
        
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault(); deferredPrompt = e;
            setTimeout(() => { document.getElementById('installBtn').classList.remove('hidden'); }, 3000);
        });
    </script>
</head>
<body class="pb-24">

    <!-- 🎬 ANIMATION LAYER -->
    <div id="tap-overlay"><i id="tap-icon" class="fas fa-play-circle text-red-600 drop-shadow-[0_0_20px_rgba(255,0,0,0.8)]"></i></div>

    <!-- ✈️ TELEGRAM BUTTON (UPDATED LINK) -->
    <a href="https://t.me/streamadept" target="_blank" class="tg-float">
        <i class="fab fa-telegram-plane text-xl"></i> JOIN COMMUNITY
    </a>

    <!-- INSTALL BTN -->
    <button id="installBtn" onclick="deferredPrompt.prompt()" class="hidden fixed bottom-24 right-4 bg-red-600 text-white px-4 py-2 rounded-full font-bold shadow-2xl z-[9998] border border-white text-xs animate-bounce">⬇ INSTALL APP</button>

    <!-- HEADER -->
    <nav class="fixed w-full z-50 top-0 px-4 py-3 bg-gradient-to-b from-black to-transparent flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="text-red-600 text-2xl font-extrabold tracking-tighter">ADEPT</div>
            <a href="ai_dub.php" class="bg-blue-600/80 border border-blue-400 text-white text-[10px] font-bold px-2 py-1 rounded shadow-lg"><i class="fas fa-robot"></i> AI</a>
        </div>
        <div class="flex items-center gap-3 text-white">
            <a href="inbox.php" class="relative">
                <i class="fas fa-envelope text-lg"></i>
                <?php if($msg_count > 0): ?><span class="absolute -top-1 -right-1 bg-green-500 text-[7px] font-bold px-1 rounded-full"><?= $msg_count ?></span><?php endif; ?>
            </a>
            <a href="notifications.php" class="relative">
                <i class="fas fa-bell text-lg"></i>
                <?php if($notif_count > 0): ?><span class="absolute -top-1 -right-1 bg-red-600 text-[7px] font-bold px-1 rounded-full animate-pulse"><?= $notif_count ?></span><?php endif; ?>
            </a>
            <a href="subscription.php" class="text-yellow-500 text-lg"><i class="fas fa-crown"></i></a>
            <a href="search.php"><i class="fas fa-search text-lg"></i></a>
            <a href="admin/dashboard.php"><i class="fas fa-user-circle text-lg"></i></a>
        </div>
    </nav>

    <!-- SLIDER -->
    <div class="relative w-full h-[55vh]">
        <?php foreach($slider_movies as $index => $m): 
             $s_title = explode(' - Ep', $m['title'])[0];
        ?>
        <div class="mySlides fade-anim absolute w-full h-full" style="display: <?= $index == 0 ? 'block' : 'none' ?>;">
            <img src="<?= $m['poster_url'] ?>" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[#050505] via-black/20 to-transparent"></div>
            <div class="absolute bottom-0 p-5 w-full bg-gradient-to-t from-black">
                <div class="flex gap-2 mb-2">
                    <span class="bg-red-600 text-[10px] font-bold px-2 py-1 rounded text-white">TRENDING</span>
                    <?php if($m['price']>0): ?><span class="bg-yellow-500 text-black text-[10px] font-bold px-2 py-1 rounded"><i class="fas fa-lock"></i> VIP</span><?php endif; ?>
                </div>
                <h1 class="text-3xl font-black text-white mb-2 leading-none drop-shadow-lg"><?= $s_title ?></h1>
                <div class="flex gap-3 mt-3">
                    <a href="movie_details.php?id=<?= $m['id'] ?>" class="flex-1 bg-white text-black py-2 rounded font-bold text-center flex items-center justify-center gap-1"><i class="fas fa-play"></i> Play</a>
                    <a href="movie_details.php?id=<?= $m['id'] ?>" class="flex-1 bg-gray-800/80 text-white py-2 rounded font-bold text-center">Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 🔥 RECENTLY ADDED -->
    <div class="mt-6 pl-4">
        <h3 class="text-lg font-bold text-white mb-3 border-l-4 border-red-600 pl-2">Recently Added</h3>
        <div class="flex overflow-x-auto gap-3 pr-4 no-scrollbar pb-2">
            <?php foreach($recent_movies as $rm): ?>
            <a href="movie_details.php?id=<?= $rm['id'] ?>" class="min-w-[130px] max-w-[130px] block relative group">
                <?php 
                    $is_paid = ($rm['price'] > 0);
                    $label = $is_paid ? "VIP 💎" : "FREE";
                    $bg = $is_paid ? "bg-yellow-500 text-black" : "bg-green-600 text-white";
                ?>
                <div class="absolute top-2 right-2 <?= $bg ?> text-[8px] font-bold px-1.5 py-0.5 rounded z-10 shadow-md"><?= $label ?></div>
                <img src="<?= $rm['poster_url'] ?>" class="w-full h-48 object-cover rounded-md shadow-lg transition transform group-hover:scale-105">
                <p class="text-xs text-gray-300 mt-2 truncate font-medium"><?= $rm['display_title'] ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 📂 CATEGORY ROWS -->
    <div class="mt-4 space-y-8">
        <?php foreach($cats as $c): 
            $stmt = $pdo->prepare("SELECT * FROM movies WHERE category_id = ? AND status='approved' ORDER BY id DESC");
            $stmt->execute([$c['id']]);
            $all_mov = $stmt->fetchAll();
            $unique_cat_mov = []; $seen = [];
            foreach($all_mov as $mov) {
                $base = explode(' - Ep', $mov['title'])[0];
                if(!in_array($base, $seen)) { $mov['d_title'] = $base; $unique_cat_mov[] = $mov; $seen[] = $base; }
            }
        ?>
        <?php if(!empty($unique_cat_mov)): ?>
        <div class="pl-4">
            <div class="flex items-center justify-between pr-4 mb-3">
                <h3 class="text-lg font-bold text-white capitalize"><?= $c['name'] ?></h3>
                <a href="browse.php?cat=<?= $c['id'] ?>" class="text-xs font-bold text-red-600">See All</a>
            </div>
            <div class="flex overflow-x-auto gap-4 no-scrollbar pb-2 pr-4">
                <?php foreach($unique_cat_mov as $cm): 
                     $label="FREE"; $bg="bg-green-600";
                     if($cm['price']>0) { $label="VIP"; $bg="bg-yellow-500 text-black"; }
                ?>
                <a href="movie_details.php?id=<?= $cm['id'] ?>" class="min-w-[130px] max-w-[130px] block relative group">
                    <div class="absolute top-2 right-2 <?= $bg ?> text-[8px] font-bold px-1.5 py-0.5 rounded z-10"><?= $label ?></div>
                    <img src="<?= $cm['poster_url'] ?>" class="w-full h-48 object-cover rounded-md shadow-lg transition transform group-hover:scale-105">
                    <p class="text-xs text-gray-300 mt-2 truncate font-medium"><?= $cm['d_title'] ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- NAV -->
    <div class="fixed bottom-0 w-full bg-[#0a0a0a] border-t border-gray-800 flex justify-around py-3 z-50">
        <a href="index.php" class="text-red-600 flex flex-col items-center"><i class="fas fa-home text-xl"></i></a>
        <a href="browse.php" class="text-gray-500 flex flex-col items-center"><i class="fas fa-layer-group text-xl"></i></a>
        <a href="search.php" class="text-gray-500 flex flex-col items-center"><i class="fas fa-search text-xl"></i></a>
    </div>

    <script>
        let slideIndex = 0; showSlides();
        function showSlides() {
            let slides = document.getElementsByClassName("mySlides");
            for (let i = 0; i < slides.length; i++) { slides[i].style.display = "none"; }
            slideIndex++; if (slideIndex > slides.length) {slideIndex = 1}    
            slides[slideIndex-1].style.display = "block"; setTimeout(showSlides, 4000); 
        }
    </script>
</body>
</html>