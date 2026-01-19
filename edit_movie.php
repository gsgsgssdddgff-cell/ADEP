<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if(!$m) die("Movie not found");

// --- 1. DELETE LOCAL FILE (SPACE SAVER) ---
if (isset($_POST['delete_local_file'])) {
    $file_path = "../" . $m['video_file'];
    if (file_exists($file_path)) {
        unlink($file_path); 
        $pdo->prepare("UPDATE movies SET video_file = NULL WHERE id = ?")->execute([$id]);
        echo "<script>alert('✅ File Deleted! Storage Freed.'); window.location='edit_movie.php?id=$id';</script>";
    } else {
        echo "<script>alert('❌ File already deleted.');</script>";
    }
}

// --- 2. CREATE BACKUP (SAVE MOVIE) ---
if (isset($_POST['create_backup'])) {
    $src = "../" . $m['video_file'];
    $dest_dir = "../backups/";
    if (!is_dir($dest_dir)) mkdir($dest_dir);
    
    if (file_exists($src)) {
        $dest = $dest_dir . basename($src);
        copy($src, $dest);
        echo "<script>alert('✅ BACKUP SAVED in backups folder!');</script>";
    } else {
        echo "<script>alert('❌ No local file to backup.');</script>";
    }
}

// --- 3. UPDATE MOVIE ---
if(isset($_POST['update_movie'])) {
    try {
        $is_premium = isset($_POST['is_premium']) ? 1 : 0;
        $price = $_POST['price'];
        
        $sql = "UPDATE movies SET title=?, description=?, rating=?, release_year=?, category_id=?, is_premium=?, watch_link=?, price=? WHERE id=?";
        $pdo->prepare($sql)->execute([
            $_POST['title'], $_POST['desc'], $_POST['rating'], $_POST['year'], 
            $_POST['cat_id'], $is_premium, $_POST['link'], $price, $id
        ]);
        
        if(!empty($_FILES['poster']['name'])) {
            $name = time() . "_new_" . str_replace(" ", "_", basename($_FILES["poster"]["name"]));
            move_uploaded_file($_FILES["poster"]["tmp_name"], "../uploads/" . $name);
            $pdo->prepare("UPDATE movies SET poster_url=? WHERE id=?")->execute(["uploads/".$name, $id]);
        }

        echo "<script>alert('✅ CHANGES SAVED!'); window.location='movies.php';</script>";
    } catch (Exception $e) { die("Error: " . $e->getMessage()); }
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6">

    <div class="max-w-3xl mx-auto bg-[#111] p-6 rounded-xl border border-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-500">EDIT MOVIE</h1>
            <a href="movies.php" class="bg-gray-800 px-3 py-1 rounded text-sm">Back</a>
        </div>

        <!-- 🚨 STORAGE & BACKUP MANAGER -->
        <div class="bg-gray-900 border border-gray-700 p-4 rounded-xl mb-6">
            <h3 class="text-lg font-bold text-white mb-2"><i class="fas fa-tools"></i> TOOLS</h3>
            
            <?php if(!empty($m['video_file']) && file_exists("../" . $m['video_file'])): 
                $fsize = round(filesize("../" . $m['video_file']) / (1024 * 1024), 2);
                $size_label = ($fsize > 1024) ? round($fsize/1024, 2) . " GB" : $fsize . " MB";
            ?>
                <div class="flex items-center justify-between bg-black p-3 rounded border border-green-900 mb-2">
                    <div>
                        <p class="text-green-400 text-sm font-bold">✅ Local File: <?= $size_label ?></p>
                        <p class="text-xs text-gray-500 truncate w-48"><?= $m['video_file'] ?></p>
                    </div>
                    <div class="flex gap-2">
                        <!-- BACKUP BUTTON -->
                        <form method="POST">
                            <button type="submit" name="create_backup" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-3 py-2 rounded">
                                <i class="fas fa-save"></i> BACKUP
                            </button>
                        </form>
                        <!-- DELETE BUTTON -->
                        <form method="POST" onsubmit="return confirm('⚠️ DELETE FILE? This cannot be undone.');">
                            <button type="submit" name="delete_local_file" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3 py-2 rounded">
                                <i class="fas fa-trash"></i> DELETE
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm">No local file found. (Link Mode)</p>
            <?php endif; ?>
        </div>

        <!-- EDIT FORM -->
        <form method="POST" enctype="multipart/form-data" action="" class="space-y-4">
            
            <div class="flex gap-4">
                <img src="../<?= $m['poster_url'] ?>" class="w-24 h-36 object-cover rounded border border-gray-700">
                <div class="w-full space-y-4">
                    <input type="text" name="title" value="<?= htmlspecialchars($m['title']) ?>" class="w-full bg-black border border-gray-700 p-3 rounded text-white font-bold">
                    
                    <select name="cat_id" class="w-full bg-black border border-gray-700 p-3 rounded text-white">
                        <?php foreach($cats as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $m['category_id'] == $c['id'] ? 'selected' : '' ?>>
                                <?= $c['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- LINK -->
            <div>
                <label class="text-blue-400 text-xs font-bold uppercase">External Link</label>
                <input type="text" name="link" value="<?= $m['watch_link'] ?>" class="w-full bg-blue-900/10 border border-blue-600 p-3 rounded text-white">
            </div>

            <!-- PRICE & PREMIUM -->
            <div class="flex gap-3">
                <div class="w-1/2">
                    <label class="text-gray-400 text-xs">Price (₹)</label>
                    <input type="number" name="price" value="<?= $m['price'] ?>" class="w-full bg-black border border-gray-700 p-3 rounded text-white">
                </div>
                <div class="w-1/2 bg-yellow-900/20 border border-yellow-600 p-3 rounded flex items-center justify-between">
                    <span class="text-xs font-bold text-yellow-500">Premium?</span>
                    <input type="checkbox" name="is_premium" class="w-5 h-5 accent-yellow-500" <?= $m['is_premium'] == 1 ? 'checked' : '' ?>>
                </div>
            </div>

            <textarea name="desc" class="w-full bg-black border border-gray-700 p-3 rounded h-32"><?= htmlspecialchars($m['description']) ?></textarea>

            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="rating" value="<?= $m['rating'] ?>" class="bg-black border border-gray-700 p-3 rounded">
                <input type="text" name="year" value="<?= $m['release_year'] ?>" class="bg-black border border-gray-700 p-3 rounded">
            </div>

            <div class="p-3 bg-black border border-gray-700 rounded">
                <label class="text-xs text-gray-500 block mb-1">Update Poster (Optional)</label>
                <input type="file" name="poster" class="w-full text-sm">
            </div>

            <button type="submit" name="update_movie" class="w-full bg-blue-600 py-3 rounded font-bold hover:bg-blue-700 text-lg">
                SAVE CHANGES
            </button>
        </form>
    </div>

</body>
</html>