<?php
require_once 'common/config.php';
session_start();

$id = $_GET['id'] ?? 0;
// Fetch Movie
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if(!$m) die("<body style='background:black;color:white;display:flex;justify-content:center;align-items:center;height:100vh;'><h1>❌ Movie Not Found</h1></body>");

// --- 🔎 SERIES NAME DETECTOR ---
// "Stranger Things - Ep 1"  ---> "Stranger Things"
$base_name = explode(' - Ep', $m['title'])[0]; 
$base_name = explode(' - Episode', $base_name)[0]; // Dual Check
$search_name = $base_name . "%"; // Database mein dhoondne ke liye

// --- 🔒 SMART PAYMENT LOGIC ---
$user_id = $_SESSION['user_id'] ?? null;
$price = $m['price'];
$status = "LOCKED"; // Default Locked

if ($price <= 0) {
    // 1. Agar Free hai
    $status = "FREE";
} elseif ($user_id) {
    
    // 2. Agar Creator khud dekh raha hai
    if ($user_id == $m['creator_name']) {
        $status = "OWNED";
    } else {
        
        // 3. CHECK SERIES PAYMENT (THE FIX)
        // Ye query check karegi: Kya user ne IS SERIES ka koi bhi part approve karaya hai?
        // Hum transactions table ko movies table se jod kar check kar rahe hain
        $sql = "SELECT t.status FROM transactions t 
                JOIN movies mov ON t.movie_id = mov.id 
                WHERE t.username = ? 
                AND mov.title LIKE ? 
                AND t.status = 'APPROVED' 
                LIMIT 1";

        $chk = $pdo->prepare($sql);
        $chk->execute([$user_id, $search_name]);

        if ($chk->rowCount() > 0) {
            $status = "PURCHASED"; // ✅ Series Unlocked!
        } else {
            // Check for Pending status
            $sql_pend = "SELECT t.status FROM transactions t 
                         JOIN movies mov ON t.movie_id = mov.id 
                         WHERE t.username = ? AND mov.title LIKE ? AND t.status = 'PENDING' LIMIT 1";
            $pnd = $pdo->prepare($sql_pend);
            $pnd->execute([$user_id, $search_name]);
            
            if ($pnd->rowCount() > 0) $status = "PENDING";
        }
    }
}

// 4. REDIRECT IF STILL LOCKED
if ($status == "LOCKED") {
    // Sirf is episode ka price maangega, par isse puri series khul jayegi
    header("Location: payment.php?amount=$price&plan=" . urlencode($base_name . " Full Series") . "&movie_id=$id");
    exit();
}

// --- 🎬 EPISODES LIST FETCH ---
$all_eps = [];
if (!empty($base_name)) {
    // Find all episodes sharing the base name
    $eq = $pdo->prepare("SELECT id, title, poster_url, is_premium, video_file FROM movies WHERE title LIKE ? ORDER BY id ASC");
    $eq->execute(["$base_name%"]);
    $all_eps = $eq->fetchAll();
}

// --- 🎥 VIDEO SOURCE LOGIC ---
$video_src = "";
$clean_path = str_replace("../", "", $m['video_file']); 
$player_type = "video"; // Default

if (!empty($clean_path) && file_exists($clean_path)) {
    $video_src = $clean_path; // Local MP4
} else {
    $link = !empty($m['video_file']) ? $m['video_file'] : $m['watch_link'];
    $video_src = $link;
    // Smart Embeds
    if (strpos($link, 'zustic')) { $video_src = str_replace("/watch/", "/e/", $link); $player_type = "iframe"; }
    if (strpos($link, 'youtu')) { $video_src = str_replace(["watch?v=", "youtu.be/"], ["embed/", "youtube.com/embed/"], $link); $player_type = "iframe"; }
    if (strpos($link, 'drive')) { $video_src = str_replace("/view", "/preview", $link); $player_type = "iframe"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($m['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{background:#0a0a0a; color:white; font-family:'Segoe UI';} .ep-card{transition:0.2s;} .active-card{border:2px solid #e50914; background:#222;}</style>
    <!-- JAIL SCRIPT -->
    <script>
        document.addEventListener('click', function(e) {
            var t = e.target;
            while (t && t.tagName !== 'A') t = t.parentNode;
            if (t && t.href) {
                if (t.getAttribute('target') === '_blank' || t.href.includes('cloud_download.php')) return;
                e.preventDefault(); window.location.href = t.href;
            }
        });
    </script>
</head>
<body class="pb-24">

    <!-- ⏳ PENDING SCREEN -->
    <?php if ($status == "PENDING"): ?>
        <div class="fixed inset-0 bg-black z-50 flex flex-col items-center justify-center text-center p-6">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-yellow-500 mb-6"></div>
            <h1 class="text-xl font-bold text-yellow-500">PAYMENT PROCESSING</h1>
            <p class="text-gray-400 mt-2 text-sm">Please wait for admin approval to unlock <b><?= $base_name ?></b> Series.</p>
            <a href="index.php" class="mt-8 bg-gray-800 px-6 py-2 rounded-full font-bold">Go Home</a>
        </div>
        <?php exit(); ?>
    <?php endif; ?>

    <!-- 🎥 PLAYER SECTION -->
    <div class="sticky top-0 z-50 w-full bg-black shadow-2xl aspect-video border-b border-gray-800">
        <a href="index.php" class="absolute top-4 left-4 z-50 bg-black/60 p-2 px-3 rounded-full backdrop-blur"><i class="fas fa-arrow-left"></i></a>
        <?php if(!empty($video_src)): ?>
            <?php if($player_type == 'iframe'): ?>
                <iframe src="<?= $video_src ?>" class="w-full h-full border-none" allowfullscreen></iframe>
            <?php else: ?>
                <video controls poster="<?= $m['poster_url'] ?>" class="w-full h-full bg-black" playsinline>
                    <source src="<?= $video_src ?>" type="video/mp4">
                </video>
            <?php endif; ?>
        <?php else: ?>
            <div class="flex items-center justify-center h-full text-red-500 font-bold">Source Unavailable</div>
        <?php endif; ?>
    </div>

    <!-- MAIN INFO -->
    <div class="p-5">
        <h1 class="text-xl font-black text-white leading-tight mb-2"><?= $m['title'] ?></h1>
        
        <div class="flex gap-2 mb-6 text-[10px] font-bold tracking-wide uppercase">
            <span class="bg-gray-800 text-gray-300 px-2 py-1 rounded"><?= $m['release_year'] ?></span>
            <span class="bg-yellow-600/20 text-yellow-500 border border-yellow-600/50 px-2 py-1 rounded">IMDb <?= $m['rating'] ?></span>
            
            <?php if($status == "PURCHASED" || $status == "OWNED"): ?>
                <span class="bg-green-600 text-white px-2 py-1 rounded flex items-center gap-1"><i class="fas fa-check-circle"></i> SERIES UNLOCKED</span>
            <?php else: ?>
                <span class="bg-blue-600 text-white px-2 py-1 rounded">FREE TO WATCH</span>
            <?php endif; ?>
        </div>

        <!-- DOWNLOAD BUTTON -->
        <?php if(!empty($clean_path) && file_exists($clean_path)): ?>
            <a href="cloud_download.php?file=<?= urlencode($clean_path) ?>&id=<?= $id ?>" class="block w-full bg-gray-800 hover:bg-gray-700 py-3 rounded-xl font-bold text-center border border-gray-600 mb-8 flex items-center justify-center gap-2">
                <i class="fas fa-cloud-download-alt text-lg"></i> DOWNLOAD EPISODE
            </a>
        <?php endif; ?>

        <!-- 📑 ALL EPISODES (NETFLIX STYLE SLIDER) -->
        <?php if(count($all_eps) > 1): ?>
        <div class="mb-8">
            <h3 class="font-bold text-white mb-3 text-sm flex items-center gap-2 border-l-4 border-red-600 pl-3">
                EPISODES
            </h3>
            
            <!-- Horizontal Slider -->
            <div class="flex overflow-x-auto gap-3 no-scrollbar pb-2">
                <?php foreach($all_eps as $ep): 
                    $is_active = ($ep['id'] == $id);
                    $ep_name_clean = explode(' - Ep', $ep['title'])[1] ?? $ep['title']; // Show "Episode 1" etc
                    $border = $is_active ? "border-red-600 border-2" : "border-gray-800 border";
                ?>
                <a href="movie_details.php?id=<?= $ep['id'] ?>" class="min-w-[130px] block relative group">
                    <img src="<?= $ep['poster_url'] ?>" class="w-full h-24 object-cover rounded-lg <?= $border ?>">
                    <?php if($is_active): ?>
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <span class="text-xs font-bold text-white">PLAYING</span>
                        </div>
                    <?php endif; ?>
                    <p class="text-[11px] text-gray-300 mt-2 font-bold truncate pl-1"><?= $ep['title'] ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- STORYLINE -->
        <div>
            <h3 class="font-bold text-gray-500 mb-2 text-xs uppercase tracking-wider">Synopsis</h3>
            <p class="text-sm text-gray-300 leading-relaxed font-light"><?= nl2br($m['description']) ?></p>
        </div>

    </div>
</body>
</html>