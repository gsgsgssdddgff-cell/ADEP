<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// 1. HANDLE ADD TO DB
if (isset($_POST['add_to_db'])) {
    $video_path = "uploads/" . $_POST['filename'];
    $target_dir = "../uploads/";
    
    // Poster Upload
    $poster_db = "";
    if (!empty($_FILES['poster']['name'])) {
        $name = time() . "_p_" . basename($_FILES["poster"]["name"]);
        move_uploaded_file($_FILES["poster"]["tmp_name"], $target_dir . $name);
        $poster_db = "uploads/" . $name;
    }

    // Insert into Database
    try {
        $sql = "INSERT INTO movies (title, category_id, description, rating, release_year, video_file, poster_url, price, is_premium, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')";
        $pdo->prepare($sql)->execute([
            $_POST['title'], $_POST['cat_id'], $_POST['desc'], $_POST['rating'], 
            $_POST['year'], $video_path, $poster_db, $_POST['price'], ($_POST['price']>0?1:0)
        ]);
        echo "<script>alert('✅ MOVIE SYNCED SUCCESSFULLY!'); window.location='pc_sync.php';</script>";
    } catch(Exception $e) { die($e->getMessage()); }
}

// 2. SCAN FOLDER & DATABASE
$folder_files = array_diff(scandir('../uploads/'), ['.', '..']);
$db_files = $pdo->query("SELECT video_file FROM movies")->fetchAll(PDO::FETCH_COLUMN);

// Clean DB paths to match filenames (remove 'uploads/')
$db_filenames = array_map('basename', $db_files);

// Find files that are in Folder but NOT in Database
$new_files = array_diff($folder_files, $db_filenames);

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Sync Tool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6">

    <div class="flex justify-between items-center mb-8 border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-blue-500"><i class="fas fa-desktop"></i> PC SYNC TOOL</h1>
            <p class="text-gray-400 text-sm">Detects files copied via USB Cable</p>
        </div>
        <a href="dashboard.php" class="bg-gray-800 px-4 py-2 rounded text-sm hover:bg-gray-700">Back</a>
    </div>

    <?php if(empty($new_files)): ?>
        <div class="text-center py-20 bg-gray-900 rounded-xl border border-gray-800">
            <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
            <h2 class="text-xl font-bold">All Files are Synced!</h2>
            <p class="text-gray-500 mt-2">Copy more movies to <b>adept_cenema/uploads</b> via USB.</p>
        </div>
    <?php else: ?>
        
        <div class="grid gap-6">
            <?php foreach($new_files as $file): 
                // Only show video files
                if(!preg_match('/\.(mp4|mkv|avi|mov|webm)$/i', $file)) continue;
                $size = round(filesize("../uploads/$file") / (1024 * 1024 * 1024), 2) . " GB";
            ?>
            
            <div class="bg-[#111] border border-blue-900 p-6 rounded-xl shadow-lg">
                <div class="flex items-center gap-4 mb-4">
                    <div class="bg-blue-900/30 p-3 rounded-full text-blue-400">
                        <i class="fas fa-file-video text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-white break-all"><?= $file ?></h3>
                        <span class="bg-blue-600 text-xs px-2 py-1 rounded text-white font-bold"><?= $size ?></span>
                        <span class="text-green-500 text-xs ml-2">● Ready to Add</span>
                    </div>
                </div>

                <!-- QUICK ADD FORM -->
                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-black p-4 rounded border border-gray-800">
                    <input type="hidden" name="filename" value="<?= $file ?>">
                    
                    <input type="text" name="title" placeholder="Movie Title" class="bg-[#111] border border-gray-700 p-2 rounded text-white" required>
                    
                    <select name="cat_id" class="bg-[#111] border border-gray-700 p-2 rounded text-white">
                        <?php foreach($cats as $c): ?><option value="<?=$c['id']?>"><?=$c['name']?></option><?php endforeach; ?>
                    </select>

                    <input type="number" name="price" placeholder="Price (0=Free)" class="bg-[#111] border border-gray-700 p-2 rounded text-white">
                    
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500 block mb-1">Select Poster</label>
                        <input type="file" name="poster" accept="image/*" class="bg-[#111] border border-gray-700 p-1 rounded text-white text-sm w-full" required>
                    </div>

                    <textarea name="desc" placeholder="Description" class="bg-[#111] border border-gray-700 p-2 rounded text-white md:col-span-2 h-20"></textarea>
                    
                    <input type="hidden" name="rating" value="9.0">
                    <input type="hidden" name="year" value="2025">

                    <button type="submit" name="add_to_db" class="md:col-span-2 bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded transition transform hover:scale-[1.02]">
                        ADD TO APP 🚀
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</body>
</html>