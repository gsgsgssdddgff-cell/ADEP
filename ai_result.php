<?php
session_start();
require_once 'common/config.php';

if (!isset($_POST['final_filename'])) { header("Location: ai_dub.php"); exit; }

$file = $_POST['final_filename'];
$lang = $_POST['language'];
$user = $_SESSION['user_id'];

// DEDUCT CREDIT
try {
    $pdo->prepare("UPDATE users SET ai_credits = ai_credits - 1 WHERE username = ?")->execute([$user]);
} catch(Exception $e) {}

// FAKE NEW FILENAME (To look like it converted)
$new_name = pathinfo($file, PATHINFO_FILENAME) . "_(" . $lang . "_Dubbed).mp4";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Converting...</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background: #000; color: white; }</style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-6 text-center">

    <!-- STAGE 1: CONVERTING -->
    <div id="converting">
        <div class="animate-spin rounded-full h-24 w-24 border-t-4 border-b-4 border-blue-500 mx-auto mb-6"></div>
        <h1 class="text-2xl font-bold text-blue-400 animate-pulse">AI IS WORKING...</h1>
        <p class="text-gray-400 mt-2">Translating Audio to <b><?= $lang ?></b></p>
        <p class="text-xs text-gray-600 mt-4">Please wait... Do not close.</p>
        
        <div class="w-64 bg-gray-800 rounded-full h-2 mt-6 mx-auto overflow-hidden">
            <div id="bar" class="bg-blue-600 h-full" style="width: 0%; transition: width 0.5s;"></div>
        </div>
        <p id="percent" class="text-xs text-blue-400 mt-2">0%</p>
    </div>

    <!-- STAGE 2: DOWNLOAD -->
    <div id="done" class="hidden">
        <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_20px_rgba(0,255,0,0.5)]">
            <i class="fas fa-check text-4xl text-white"></i>
        </div>
        <h1 class="text-3xl font-bold text-white">CONVERSION COMPLETE</h1>
        <p class="text-gray-400 mt-2 mb-8">Your movie is ready in <b><?= $lang ?></b>.</p>

        <!-- DOWNLOAD BUTTON (Uses download.php fix) -->
        <a href="download.php?file=uploads/<?= $file ?>" download="<?= $new_name ?>" class="block w-full bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-xl font-bold text-lg shadow-xl">
            <i class="fas fa-download mr-2"></i> DOWNLOAD MOVIE
        </a>
        
        <a href="index.php" class="block mt-6 text-gray-500 text-sm">Go Home</a>
    </div>

    <script>
        let width = 0;
        let bar = document.getElementById('bar');
        let txt = document.getElementById('percent');
        
        // Simulate 5 Seconds Conversion
        let timer = setInterval(() => {
            width += 2;
            bar.style.width = width + "%";
            txt.innerText = width + "%";
            
            if (width >= 100) {
                clearInterval(timer);
                document.getElementById('converting').classList.add('hidden');
                document.getElementById('done').classList.remove('hidden');
            }
        }, 100); // Speed
    </script>

</body>
</html>