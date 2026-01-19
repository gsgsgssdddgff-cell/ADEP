<?php
require_once 'common/config.php';
$file_param = $_GET['file'] ?? '';
$filepath = __DIR__ . '/' . $file_param;
$filename = basename($filepath);
$clean_name = preg_replace('/^\d+_vid_/', '', $filename);
$clean_name = str_replace('_', ' ', $clean_name);
$filesize = file_exists($filepath) ? round(filesize($filepath)/(1024*1024), 2).' MB' : 'Unknown';

// MOVIE TITLE (URL se nikalne ki koshish, ya filename use karein)
$movie_title = $_GET['title'] ?? $clean_name;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Ready</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { background: #000; color: #fff; } </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-sm bg-[#111] p-8 rounded-2xl border border-gray-800 text-center shadow-2xl relative">
        <a href="index.php" class="absolute top-4 right-4 text-gray-500 hover:text-white"><i class="fas fa-times text-xl"></i></a>

        <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-file-video text-2xl text-white"></i>
        </div>

        <h1 class="text-xl font-bold mb-2 break-words leading-tight"><?= $clean_name ?></h1>
        <div class="inline-block bg-gray-800 px-3 py-1 rounded text-xs font-bold text-gray-300 mb-8">
            <?= $filesize ?> • 1080p
        </div>

        <!-- DOWNLOAD BUTTON with MEMORY -->
        <a href="download.php?file=<?= $file_param ?>" onclick="startDownload('<?= $clean_name ?>', '<?= $filesize ?>')" class="block w-full bg-[#E50914] hover:bg-red-700 text-white font-bold py-4 rounded-xl shadow-lg transition mb-4">
            <i class="fas fa-download mr-2"></i> DOWNLOAD NOW
        </a>

        <p class="text-[10px] text-gray-600">Check Notification Bar for progress.</p>
    </div>

    <!-- TOAST -->
    <div id="toast" class="fixed bottom-10 bg-green-600 text-white px-6 py-3 rounded-full shadow-xl transform translate-y-20 opacity-0 transition-all duration-500">
        <i class="fas fa-check-circle"></i> Downloading Started...
    </div>

    <script>
        function startDownload(name, size) {
            // 1. Show Toast
            let toast = document.getElementById('toast');
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => { toast.classList.add('translate-y-20', 'opacity-0'); }, 3000);

            // 2. SAVE TO HISTORY (Smart App Feature)
            let downloads = JSON.parse(localStorage.getItem('my_downloads')) || [];
            
            // Duplicate check
            let exists = downloads.some(d => d.name === name);
            if(!exists) {
                let today = new Date().toLocaleDateString();
                downloads.push({ name: name, size: size, date: today });
                localStorage.setItem('my_downloads', JSON.stringify(downloads));
            }
        }
    </script>

</body>
</html>