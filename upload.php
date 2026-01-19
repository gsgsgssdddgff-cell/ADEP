<?php
session_start();
require_once '../common/config.php';

// 1. LOGIN & CONFIG
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) { header("Location: ../login.php"); exit; }
$is_admin = isset($_SESSION['admin_logged_in']);
$creator_id = $is_admin ? 'Admin' : $_SESSION['user_id'];

// 2. SERVER POWER
@ini_set('upload_max_filesize', '10000M'); @ini_set('post_max_size', '10000M'); @ini_set('max_execution_time', '0'); @ini_set('memory_limit', '-1');

// 3. SCAN FILES (BLUE BOX)
$folder_path='../uploads/'; if(!is_dir($folder_path)) mkdir($folder_path); $found_files=[]; $scan=scandir($folder_path); foreach($scan as $f){ if(preg_match('/\.(mp4|mkv|avi|mov|webm)$/i',$f)){ $found_files[]=$f; } }

// --- AJAX SAVE (BACKGROUND) ---
if (isset($_POST['ajax_save'])) {
    try {
        $video = "uploads/" . $_POST['video_file'];
        $poster = $_POST['poster_path'];
        $screenshots = $_POST['screen_path'];
        
        $sql = "INSERT INTO movies (title, category_id, description, rating, release_year, watch_link, video_file, poster_url, screenshots, price, is_premium, creator_name, creator_phone, creator_upi, creator_bank, status, file_size_label) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $pdo->prepare($sql)->execute([
            $_POST['title'], $_POST['cat_id'], $_POST['desc'], $_POST['rating'], $_POST['year'], 
            $_POST['link'], $video, $poster, $screenshots, $_POST['price'], 
            ($_POST['price']>0?1:0), $creator_id, $_POST['c_phone'], $_POST['c_upi'], $_POST['c_bank'], 
            ($is_admin ? 'approved' : 'pending'), $_POST['manual_size']
        ]);
        echo "SAVED";
    } catch(Exception $e) { echo "DB_ERROR"; }
    exit;
}

// POSTER/SCREEN UPLOAD
if (isset($_FILES['ajax_poster'])) {
    $td = "../uploads/";
    if(!file_exists($td)) mkdir($td, 0777, true);
    
    $p_path = "uploads/default.jpg";
    if(!empty($_FILES['ajax_poster']['name'])) {
        $n = time() . "_p_" . preg_replace('/[^A-Za-z0-9.]/', '_', $_FILES['ajax_poster']['name']);
        move_uploaded_file($_FILES['ajax_poster']['tmp_name'], $td.$n);
        $p_path = "uploads/".$n;
    }
    
    $s_paths = [];
    if(!empty($_FILES['ajax_screen']['name'][0])) {
        foreach($_FILES['ajax_screen']['name'] as $k => $name) {
            $n = time() . "_s_" . $k . ".jpg";
            move_uploaded_file($_FILES['ajax_screen']['tmp_name'][$k], $td.$n);
            $s_paths[] = "uploads/".$n;
        }
    }
    echo json_encode(['poster' => $p_path, 'screens' => implode(',', $s_paths)]);
    exit;
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Uploader</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-4 pb-20">

    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
        <h1 class="text-xl font-bold text-red-600">MASTER UPLOAD</h1>
        <div class="flex gap-2">
            <a href="../ai_dub.php" class="bg-blue-600 px-3 py-1 rounded text-xs font-bold">AI</a>
            <a href="<?= $is_admin ? 'dashboard.php' : '../index.php' ?>" class="bg-gray-800 px-3 py-1 rounded text-sm">Back</a>
        </div>
    </div>

    <form id="uploadForm" class="space-y-5 max-w-3xl mx-auto">
        
        <input type="text" id="title" class="w-full bg-[#111] border border-gray-700 p-3 rounded text-white font-bold" placeholder="Movie Title" required>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
                <select id="cat_id" class="w-full bg-[#111] border border-gray-700 p-3 rounded text-white">
                    <?php foreach($cats as $c): ?><option value="<?=$c['id']?>"><?=$c['name']?></option><?php endforeach; ?>
                </select>
                <div class="flex gap-2">
                    <input type="number" id="price" class="w-1/2 bg-green-900/20 border border-green-600 p-3 rounded text-green-400 font-bold" placeholder="Price (0=Free)">
                    <input type="text" id="manual_size" class="w-1/2 bg-yellow-900/20 border border-yellow-600 p-3 rounded text-yellow-400 font-bold" placeholder="Size (e.g 2GB)">
                </div>
            </div>

            <!-- CREATOR INFO -->
            <div class="bg-blue-900/20 p-4 rounded border border-blue-600 space-y-2">
                <p class="text-blue-400 text-xs font-bold uppercase mb-1">Creator / Bank Details</p>
                <input type="number" id="c_phone" class="w-full bg-black border border-blue-500 p-2 text-sm text-white" placeholder="Phone" value="<?= $is_admin?'Admin':'' ?>">
                <input type="text" id="c_upi" class="w-full bg-black border border-blue-500 p-2 text-sm text-white" placeholder="UPI ID">
                <input type="text" id="c_bank" class="w-full bg-black border border-blue-500 p-2 text-sm text-white" placeholder="Bank Details">
            </div>
        </div>
        
        <textarea id="desc" class="w-full bg-[#111] border border-gray-700 p-3 rounded h-20 text-gray-300" placeholder="Description..."></textarea>

        <div class="grid grid-cols-2 gap-4">
            <input type="text" id="rating" class="bg-[#111] border border-gray-700 p-2 rounded" placeholder="Rating (9.5)">
            <input type="text" id="year" class="bg-[#111] border border-gray-700 p-2 rounded" placeholder="Year">
        </div>

        <div class="bg-[#111] p-4 rounded border border-gray-800 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-xs text-yellow-500 mb-1">1. Poster (Required)</label><input type="file" id="poster" accept="image/*" class="w-full text-sm text-gray-400"></div>
            <div><label class="block text-xs text-blue-400 mb-1">2. Screenshots</label><input type="file" id="screenshots" multiple class="w-full text-sm text-gray-400"></div>
        </div>

        <!-- 🚀 GREEN BOX -->
        <div class="bg-green-900/10 p-5 rounded border border-green-800">
            <label class="block text-green-500 font-bold mb-2">Upload File (Gallery/Files - 10X Speed)</label>
            <input type="file" id="bigFile" class="w-full text-sm text-gray-300 mb-3">
            <div id="progressBox" class="w-full bg-gray-700 h-3 rounded hidden"><div id="progressBar" class="bg-green-500 h-3 w-0 transition-all duration-200"></div></div>
            <p id="statusText" class="text-xs text-center mt-1"></p>
            <input type="hidden" id="finalVideoName">
            <button type="button" onclick="uploadBigFile()" class="bg-green-600 w-full py-2 rounded font-bold mt-2">Start Upload</button>
        </div>

        <div class="text-center text-xs text-gray-600 py-1">- OR -</div>

        <!-- 🚀 BLUE BOX -->
        <div class="bg-blue-900/20 p-4 rounded border border-blue-800">
            <label class="block text-blue-400 text-xs font-bold mb-2">Select from ZArchiver</label>
            <select id="existing_video" class="w-full bg-black border border-blue-500 p-2 rounded text-white text-sm"><option value="">-- Select File --</option><?php foreach($found_files as $f): ?><option value="<?=$f?>">📂 <?=$f?></option><?php endforeach; ?></select>
        </div>

        <input type="text" id="link" class="w-full bg-black border border-gray-700 p-3 rounded" placeholder="Or Paste External Link">

        <button type="button" onclick="finalizePublish()" id="pubBtn" class="w-full bg-red-600 py-4 rounded font-bold text-xl mt-4">PUBLISH MOVIE</button>
    </form>

    <script>
        async function uploadBigFile() {
            let file = document.getElementById('bigFile').files[0];
            if (!file) return alert("Select File");
            
            document.getElementById('progressBox').classList.remove('hidden');
            let bar=document.getElementById('progressBar'), txt=document.getElementById('statusText');
            let name = Date.now()+"_"+file.name.replace(/\s+/g,'_');
            const chunk=10*1024*1024; // 10MB
            
            txt.innerText = "Uploading...";
            for(let start=0; start<file.size; start+=chunk){
                let fd=new FormData(); fd.append("file",file.slice(start,start+chunk)); fd.append("filename",name);
                await fetch("upload_chunk.php", {method:"POST", body:fd});
                bar.style.width = Math.round(((start+chunk)/file.size)*100)+"%";
            }
            txt.innerText = "✅ Upload Complete";
            document.getElementById('finalVideoName').value = name;
        }

        async function finalizePublish() {
            let btn = document.getElementById('pubBtn');
            btn.disabled = true; btn.innerText = "Saving...";

            // Upload Poster First
            let mediaData = new FormData();
            mediaData.append('ajax_poster', document.getElementById('poster').files[0]);
            let screens = document.getElementById('screenshots').files;
            for(let i=0; i<screens.length; i++) mediaData.append('ajax_screen[]', screens[i]);

            let res = await fetch('upload.php', {method:'POST', body:mediaData}).then(r=>r.json());

            // Save Data
            let fd = new FormData();
            fd.append('ajax_save', 1);
            fd.append('video_file', document.getElementById('finalVideoName').value || document.getElementById('existing_video').value || '');
            fd.append('poster_path', res.poster);
            fd.append('screen_path', res.screens);
            
            // Append Inputs
            ['title','cat_id','desc','rating','year','price','manual_size','link','c_phone','c_upi','c_bank'].forEach(id => {
                fd.append(id, document.getElementById(id).value);
            });

            let finalRes = await fetch('upload.php', {method:'POST', body:fd}).then(r=>r.text());
            
            if(finalRes.trim() == "SAVED") { alert("✅ DONE!"); window.location.href="movies.php"; }
            else { alert("Error: "+finalRes); btn.disabled=false; }
        }
    </script>
</body>
</html>