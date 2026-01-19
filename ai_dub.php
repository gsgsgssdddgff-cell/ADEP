<?php
session_start();
require_once 'common/config.php';

// 1. USER CHECK
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user_id = $_SESSION['user_id'];

// 2. CHECK CREDITS
try {
    $u = $pdo->prepare("SELECT ai_credits FROM users WHERE username = ?");
    $u->execute([$user_id]);
    $credits = $u->fetchColumn();
} catch(Exception $e) { $credits = 0; }

// 3. SCAN FILES FOR BLUE BOX
$folder_path = 'uploads/';
if (!is_dir($folder_path)) mkdir($folder_path); 
$found_files = [];
$scan = scandir($folder_path);
foreach($scan as $file) {
    if ($file !== '.' && $file !== '..' && preg_match('/\.(mp4|mkv|avi|mov|webm)$/i', $file)) {
        $found_files[] = $file;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 8GB Converter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background: #000; color: white; }</style>
</head>
<body class="p-4 pb-20">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
        <h1 class="text-xl font-bold text-blue-500"><i class="fas fa-robot"></i> AI DUB PRO</h1>
        <a href="index.php" class="bg-gray-800 px-3 py-1 rounded text-sm">Home</a>
    </div>

    <!-- LOCKED SCREEN -->
    <?php if($credits <= 0): ?>
        <div class="text-center mt-10 p-8 bg-gray-900 rounded-2xl border border-red-900">
            <i class="fas fa-lock text-5xl text-red-600 mb-4"></i>
            <h2 class="text-2xl font-bold">CREDITS EXHAUSTED</h2>
            <p class="text-gray-400 mb-6">You need credits to convert 8GB+ Movies.</p>
            <a href="payment.php?amount=580&plan=AI_Dubbing_Credit" class="block w-full bg-green-600 py-3 rounded-xl font-bold text-lg animate-bounce">
                BUY CREDIT ($7)
            </a>
        </div>
    <?php else: ?>

        <!-- UNLOCKED: 8GB UPLOAD SYSTEM -->
        <div class="bg-blue-900/20 p-3 rounded border border-blue-800 mb-6 flex justify-between items-center">
            <span class="text-green-400 font-bold text-sm">✅ Credits: <?= $credits ?></span>
            <span class="text-xs text-gray-400">Supports 8GB+ Files</span>
        </div>

        <form action="ai_result.php" method="POST" id="conversionForm">
            
            <!-- 1. LANGUAGE SELECTOR -->
            <div class="mb-6">
                <label class="block text-gray-400 text-xs font-bold mb-2 uppercase">1. Target Language</label>
                <select name="language" class="w-full bg-[#111] border border-gray-700 p-3 rounded text-white h-12">
                    <option value="Hindi">🇮🇳 Hindi</option>
                    <option value="English">🇺🇸 English</option>
                    <option value="Tamil">🇮🇳 Tamil</option>
                    <option value="Telugu">🇮🇳 Telugu</option>
                    <option value="Japanese">🇯🇵 Japanese</option>
                    <option value="Korean">🇰🇷 Korean</option>
                    <option value="Chinese">🇨🇳 Chinese</option>
                    <option value="Spanish">🇪🇸 Spanish</option>
                    <option value="French">🇫🇷 French</option>
                    <option value="Russian">🇷🇺 Russian</option>
                </select>
            </div>

            <!-- 2. UPLOAD OPTIONS -->
            <label class="block text-gray-400 text-xs font-bold mb-2 uppercase">2. Select Movie Source</label>

            <!-- OPTION A: GALLERY UPLOAD (GREEN) -->
            <div class="bg-green-900/10 p-4 rounded border border-green-800 mb-4">
                <p class="text-green-500 text-xs font-bold mb-2"><i class="fas fa-upload"></i> Upload from Gallery (Progress Bar)</p>
                <input type="file" id="bigFile" class="w-full text-sm text-gray-400 mb-2">
                
                <div id="progressBox" class="w-full bg-gray-700 rounded-full h-3 hidden">
                    <div id="progressBar" class="bg-green-500 h-3 rounded-full" style="width: 0%"></div>
                </div>
                <p id="statusText" class="text-[10px] text-center mt-1 text-gray-400"></p>
                
                <button type="button" onclick="uploadBigFile()" class="bg-green-700 px-4 py-2 rounded text-xs font-bold mt-2 w-full">
                    Start Uploading
                </button>
            </div>

            <div class="text-center text-gray-600 text-xs mb-4">- OR -</div>

            <!-- OPTION B: ZARCHIVER SELECT (BLUE) -->
            <div class="bg-blue-900/10 p-4 rounded border border-blue-800 mb-6">
                <p class="text-blue-500 text-xs font-bold mb-2"><i class="fas fa-folder"></i> Select from ZArchiver (Instant)</p>
                <select id="existingSelect" name="existing_video" class="w-full bg-black border border-blue-500 p-2 rounded text-white text-sm">
                    <option value="">-- Select File --</option>
                    <?php foreach($found_files as $f): ?>
                        <option value="<?= $f ?>">📂 <?= $f ?> (<?= round(filesize("uploads/$f")/1024/1024, 2) ?> MB)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- HIDDEN INPUT FOR FINAL FILENAME -->
            <input type="hidden" name="final_filename" id="finalFilename">

            <!-- SUBMIT -->
            <button type="button" onclick="submitConversion()" id="convertBtn" class="w-full bg-gray-600 py-4 rounded-xl font-bold text-lg cursor-not-allowed" disabled>
                CONVERT MOVIE 🚀
            </button>

        </form>

    <?php endif; ?>

    <!-- 🚀 JS UPLOADER (10MB CHUNKS) -->
    <script>
        // Handle Blue Box Selection
        document.getElementById('existingSelect').addEventListener('change', function() {
            if(this.value) {
                document.getElementById('finalFilename').value = this.value;
                enableButton();
            }
        });

        function enableButton() {
            let btn = document.getElementById('convertBtn');
            btn.disabled = false;
            btn.classList.remove('bg-gray-600', 'cursor-not-allowed');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-500');
        }

        async function uploadBigFile() {
            let fileInput = document.getElementById('bigFile');
            let file = fileInput.files[0];
            if (!file) { alert("Select a video!"); return; }

            document.getElementById('progressBox').classList.remove('hidden');
            let status = document.getElementById('statusText');
            let bar = document.getElementById('progressBar');
            
            let uniqueName = Date.now() + "_ai_" + file.name.replace(/\s+/g, '_');
            const chunkSize = 10 * 1024 * 1024; // 10MB Fast Chunks
            
            status.innerText = "Uploading...";

            for (let start = 0; start < file.size; start += chunkSize) {
                let chunk = file.slice(start, start + chunkSize);
                let fd = new FormData();
                fd.append("file", chunk);
                fd.append("filename", uniqueName);

                try {
                    await fetch("upload_chunk_user.php", { method: "POST", body: fd });
                    let percent = Math.round(((start + chunkSize) / file.size) * 100);
                    if(percent > 100) percent = 100;
                    bar.style.width = percent + "%";
                    status.innerText = percent + "%";
                } catch(e) {
                    status.innerText = "Retrying...";
                }
            }

            status.innerText = "✅ Upload Complete!";
            document.getElementById('finalFilename').value = uniqueName;
            enableButton();
        }

        function submitConversion() {
            if(!document.getElementById('finalFilename').value) {
                alert("Please upload or select a video first!");
                return;
            }
            document.getElementById('conversionForm').submit();
        }
    </script>

</body>
</html>