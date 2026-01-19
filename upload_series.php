<?php
session_start();
require_once '../common/config.php';

// 1. LOGIN & SECURITY
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) { header("Location: ../login.php"); exit; }
$is_admin = isset($_SESSION['admin_logged_in']);
$creator_id = $is_admin ? 'Admin' : $_SESSION['user_id'];

// 2. UNLIMITED POWER
@ini_set('upload_max_filesize', '500G'); 
@ini_set('post_max_size', '500G');
@ini_set('max_execution_time', '0'); 
@ini_set('memory_limit', '-1');

// --- AJAX HANDLER (SAVES TO DB) ---
if (isset($_POST['ajax_series'])) {
    try {
        $video_db = "uploads/" . $_POST['video_file'];
        $title = $_POST['title']; // Name like "Naruto - Episode 1"
        $poster_path = $_POST['poster_path'];
        
        // Data
        $price = $_POST['price'] ?? 0;
        $c_phone = $_POST['c_phone'] ?? ''; 
        $c_upi = $_POST['c_upi'] ?? ''; 
        $c_bank = $_POST['c_bank'] ?? '';
        
        // Logic
        $status = $is_admin ? 'approved' : ($price > 0 ? 'pending' : 'approved');
        $is_premium = ($price > 0) ? 1 : 0;

        $sql = "INSERT INTO movies (title, category_id, description, rating, release_year, watch_link, video_file, poster_url, price, is_premium, creator_name, creator_phone, creator_upi, creator_bank, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([
            $title, $_POST['cat_id'], $_POST['desc'], $_POST['rating'], $_POST['year'], 
            "", $video_db, $poster_path, $price, $is_premium, $creator_id, $c_phone, $c_upi, $c_bank, $status
        ]);
        
        echo "SAVED";
    } catch(Exception $e) { echo "DB_ERROR"; }
    exit;
}

// POSTER HANDLER
if (isset($_FILES['series_poster'])) {
    $td = "../uploads/";
    if(!file_exists($td)) mkdir($td, 0777, true);
    $n = time() . "_sp_" . basename($_FILES["series_poster"]["name"]);
    move_uploaded_file($_FILES["series_poster"]["tmp_name"], $td.$n);
    echo "uploads/".$n;
    exit;
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Series Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-4 pb-40">

    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
        <h1 class="text-xl font-bold text-purple-500"><?= $is_admin ? 'ADMIN' : 'CREATOR' ?> SERIES UPLOAD</h1>
        <a href="<?= $is_admin?'dashboard.php':'../index.php' ?>" class="bg-gray-800 px-3 py-1 rounded text-sm">Back</a>
    </div>

    <!-- MAIN SETTINGS -->
    <div class="max-w-4xl mx-auto space-y-5">

        <input type="text" id="series_name" class="w-full bg-[#111] border border-gray-700 p-3 rounded text-white font-bold" placeholder="Series Name (e.g. Demon Slayer)" required>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-4">
                <select id="cat_id" class="w-full bg-[#111] border border-gray-700 p-3 rounded text-white">
                    <?php foreach($cats as $c): ?><option value="<?=$c['id']?>"><?=$c['name']?></option><?php endforeach; ?>
                </select>
                <input type="number" id="price" class="w-full bg-green-900/20 border border-green-600 p-3 rounded text-green-400 font-bold" placeholder="Price (0=Free)">
            </div>
            
            <!-- CREATOR BANK INFO (VISIBLE) -->
            <div class="bg-blue-900/20 p-4 rounded border border-blue-600 space-y-2">
                <p class="text-blue-400 text-xs font-bold uppercase mb-1">Payment Details (Required for Earnings)</p>
                <input type="number" id="c_phone" class="w-full bg-black border border-blue-500 p-2 text-sm text-white" placeholder="Phone" value="<?= $is_admin?'Admin':'' ?>">
                <input type="text" id="c_upi" class="w-full bg-black border border-blue-500 p-2 text-sm text-white" placeholder="UPI ID">
                <input type="text" id="c_bank" class="w-full bg-black border border-blue-500 p-2 text-sm text-white" placeholder="Bank Details">
            </div>
        </div>

        <textarea id="desc" class="w-full bg-[#111] border border-gray-700 p-3 rounded h-20 text-gray-300" placeholder="Description..."></textarea>
        
        <div class="grid grid-cols-2 gap-4">
            <input type="text" id="rating" class="bg-[#111] border border-gray-700 p-2 rounded" placeholder="Rating (9.5)">
            <input type="text" id="year" class="bg-[#111] border border-gray-700 p-2 rounded" placeholder="Year (2026)">
        </div>

        <div class="bg-[#111] p-3 rounded border border-gray-800">
            <label class="block text-xs text-yellow-500 mb-1 font-bold">Series Poster (Applied to ALL Episodes)</label>
            <input type="file" id="poster" accept="image/*" class="w-full text-sm">
        </div>

        <!-- 🚀 20X SPEED UPLOADER -->
        <div class="bg-purple-900/10 p-6 rounded-xl border-2 border-purple-700 relative hover:border-purple-500 transition">
            <label class="block text-purple-400 font-bold mb-3 flex flex-col items-center gap-2 cursor-pointer" onclick="document.getElementById('multiFiles').click()">
                <i class="fas fa-layer-group text-4xl"></i>
                <span>SELECT ALL EPISODES (GALLERY / FILES)</span>
            </label>
            
            <p class="text-[10px] text-center text-gray-500 mb-4">Supports 256GB+. Auto Naming: Series Name - Ep 1, Ep 2...</p>

            <input type="file" id="multiFiles" multiple class="hidden" onchange="previewFiles()">
            
            <div id="queueList" class="space-y-2 mt-4 hidden"></div>

            <button type="button" onclick="startSeriesUpload()" id="startBtn" class="hidden bg-gradient-to-r from-purple-700 to-blue-700 w-full py-4 rounded-xl font-bold shadow-lg mt-4 text-lg">
                UPLOAD & ADD TO APP
            </button>
        </div>

    </div>

    <!-- JS ENGINE -->
    <script>
        async function previewFiles() {
            let files = document.getElementById('multiFiles').files;
            let list = document.getElementById('queueList');
            let startBtn = document.getElementById('startBtn');
            let baseName = document.getElementById('series_name').value || "Episode";

            list.innerHTML = "";
            list.classList.remove('hidden');
            startBtn.classList.remove('hidden');
            startBtn.innerText = `START UPLOAD (${files.length} Episodes)`;

            Array.from(files).forEach((f, i) => {
                let html = `
                <div id="file_${i}" class="bg-gray-800 p-3 rounded border border-gray-600 flex items-center justify-between">
                    <div class="truncate w-3/4">
                        <p class="text-xs text-white font-bold">Ep ${i+1}: ${f.name}</p>
                        <div class="w-full bg-black h-1.5 rounded mt-1 overflow-hidden"><div class="bar bg-purple-500 h-full w-0"></div></div>
                    </div>
                    <span class="status text-[10px] text-gray-400">WAITING</span>
                </div>`;
                list.insertAdjacentHTML('beforeend', html);
            });
        }

        async function startSeriesUpload() {
            let files = document.getElementById('multiFiles').files;
            if(files.length === 0) return;
            if(!document.getElementById('series_name').value) return alert("Enter Series Name First!");
            if(!document.getElementById('poster').files[0]) return alert("Select Poster First!");

            let pData = new FormData();
            pData.append('series_poster', document.getElementById('poster').files[0]);
            let uploadedPosterPath = await fetch('upload_series.php', {method:'POST', body:pData}).then(r=>r.text());

            let btn = document.getElementById('startBtn');
            btn.disabled = true;
            btn.innerText = "PROCESSING...";

            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                let row = document.getElementById(`file_${i}`);
                let status = row.querySelector('.status');
                let bar = row.querySelector('.bar');

                status.innerText = "UPLOADING...";
                status.classList.add('text-purple-400');
                
                let uniqueName = Date.now() + "_ep" + (i+1) + "_" + file.name.replace(/[^a-zA-Z0-9.]/g, "_");
                const chunkSize = 20 * 1024 * 1024; // 20MB Fast Chunks
                
                for (let start = 0; start < file.size; start += chunkSize) {
                    let chunk = file.slice(start, start + chunkSize);
                    let fd = new FormData();
                    fd.append("file", chunk);
                    fd.append("filename", uniqueName);
                    
                    try {
                        await fetch("upload_chunk.php", {method: "POST", body: fd});
                        let pct = Math.round(((start+chunkSize)/file.size)*100);
                        if(pct>100) pct=100;
                        bar.style.width = pct + "%";
                    } catch(e) {}
                }

                status.innerText = "SAVING TO APP...";
                let finalTitle = `${document.getElementById('series_name').value} - Episode ${i+1}`;
                
                let db = new FormData();
                db.append('ajax_series', 1);
                db.append('title', finalTitle);
                db.append('video_file', uniqueName);
                db.append('poster_path', uploadedPosterPath);
                
                // Add All Form Data
                db.append('cat_id', document.getElementById('cat_id').value);
                db.append('desc', document.getElementById('desc').value);
                db.append('price', document.getElementById('price').value);
                db.append('rating', document.getElementById('rating').value);
                db.append('year', document.getElementById('year').value);
                
                // Add Creator Bank Details
                db.append('c_phone', document.getElementById('c_phone').value);
                db.append('c_upi', document.getElementById('c_upi').value);
                db.append('c_bank', document.getElementById('c_bank').value);

                await fetch('upload_series.php', { method:'POST', body:db });

                status.innerText = "✅ ADDED TO APP";
                status.classList.replace('text-purple-400', 'text-green-500');
            }
            alert("ALL EPISODES LIVE!");
            window.location.href = "movies.php";
        }
    </script>
</body>
</html>