<?php
session_start();
require_once '../common/config.php';

// 🔒 THE SECRET PASSWORD
$SECRET_PASS = "_____@4252582£:&*£&-£+£72(+#&#&2--£";

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if(!$m) die("Movie not found");

// --- HANDLE SAVE PROCESS ---
if (isset($_POST['auth_pass'])) {
    if ($_POST['auth_pass'] === $SECRET_PASS) {
        
        // 1. Create Backup Folder
        $backup_root = "../saved_movies/";
        $movie_folder = $backup_root . str_replace(" ", "_", $m['title']) . "/";
        
        if (!file_exists($movie_folder)) {
            mkdir($movie_folder, 0777, true);
        }

        // 2. Copy Video File
        $msg = "";
        if (!empty($m['video_file']) && file_exists("../" . $m['video_file'])) {
            $video_name = basename($m['video_file']);
            copy("../" . $m['video_file'], $movie_folder . $video_name);
            $msg .= "✅ Video Saved. ";
        } else {
            $msg .= "⚠️ Video not found (Link Mode). ";
        }

        // 3. Copy Poster
        if (!empty($m['poster_url']) && file_exists("../" . $m['poster_url'])) {
            $poster_name = basename($m['poster_url']);
            copy("../" . $m['poster_url'], $movie_folder . $poster_name);
            $msg .= "✅ Poster Saved. ";
        }

        // 4. Save Description & Details to Text File
        $info = "TITLE: " . $m['title'] . "\n";
        $info .= "RATING: " . $m['rating'] . "\n";
        $info .= "YEAR: " . $m['release_year'] . "\n";
        $info .= "DESCRIPTION:\n" . $m['description'] . "\n";
        
        file_put_contents($movie_folder . "details.txt", $info);
        $msg .= "✅ Details Saved.";

        echo "<script>alert('$msg'); window.location='movies.php';</script>";
        exit;

    } else {
        $error = "❌ WRONG PASSWORD! ACCESS DENIED.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Save</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white flex flex-col items-center justify-center h-screen p-6">

    <div class="bg-[#111] p-8 rounded-2xl border border-red-900 text-center max-w-md w-full shadow-2xl">
        <div class="w-20 h-20 bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-lock text-4xl text-red-600"></i>
        </div>

        <h1 class="text-2xl font-bold text-white mb-2">SECURE BACKUP</h1>
        <p class="text-gray-400 text-sm mb-6">Saving: <b class="text-yellow-500"><?= $m['title'] ?></b></p>

        <?php if(isset($error)): ?>
            <div class="bg-red-600 text-white p-2 rounded mb-4 text-xs font-bold"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="password" name="auth_pass" placeholder="Enter Secret Password" class="w-full bg-black border border-gray-700 p-3 rounded text-white text-center focus:border-red-500 outline-none" required>
            
            <button class="w-full bg-gradient-to-r from-red-600 to-red-800 py-3 rounded-lg font-bold hover:scale-105 transition">
                CONFIRM SAVE
            </button>
        </form>

        <a href="movies.php" class="block mt-6 text-gray-500 text-sm">Cancel</a>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>