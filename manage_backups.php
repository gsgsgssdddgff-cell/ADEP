<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }

$dir = "../saved_movies/";
if (!is_dir($dir)) mkdir($dir);

// HANDLE DELETE
if (isset($_GET['del'])) {
    $file = $_GET['del'];
    $path = $dir . $file;
    
    // Attempt 1: Standard Unlink
    if (unlink($path)) {
        // Success
    } else {
        // Attempt 2: Full Path
        unlink(realpath($path));
    }
    
    header("Location: manage_backups.php");
}

$files = array_diff(scandir($dir), ['.', '..']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Vault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6">

    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
        <h1 class="text-xl font-bold text-green-500">SAVED BACKUPS</h1>
        <a href="save_menu.php" class="bg-gray-800 px-3 py-1 rounded">Back</a>
    </div>

    <?php if(empty($files)): ?>
        <p class="text-center text-gray-500 mt-20">Empty.</p>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach($files as $f): ?>
            <div class="bg-[#111] p-3 rounded flex justify-between items-center border border-gray-700">
                <p class="text-xs text-white truncate w-1/2"><?= $f ?></p>
                <div class="flex gap-2">
                    <a href="<?= $dir.$f ?>" download class="text-blue-500"><i class="fas fa-download"></i></a>
                    <a href="?del=<?= $f ?>" class="text-red-500" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>