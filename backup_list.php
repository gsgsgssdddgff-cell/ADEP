<?php
session_start();
// 1. SECURITY CHECK
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// 2. SEARCH LOGIC
$search = $_GET['q'] ?? '';
$sql = "SELECT * FROM movies WHERE title LIKE ? ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%"]);
$movies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select to Backup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{background-color:#050505; color:white;}</style>
</head>
<body class="p-6 pb-20">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-green-500 uppercase tracking-widest"><i class="fas fa-database"></i> Create Backup</h1>
            <p class="text-xs text-gray-400">Select a movie to secure (ZIP)</p>
        </div>
        <div class="flex gap-2">
            <a href="manage_backups.php" class="bg-blue-600 px-3 py-2 rounded text-sm font-bold hover:bg-blue-500">View Vault</a>
            <a href="save_menu.php" class="bg-gray-800 px-3 py-2 rounded text-sm font-bold">Back</a>
        </div>
    </div>

    <!-- SEARCH BAR -->
    <form method="GET" class="mb-6 relative">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
               class="w-full bg-[#111] border border-green-800 p-4 pl-12 rounded-xl text-white focus:border-green-500 outline-none shadow-lg shadow-green-900/20" 
               placeholder="Search database to backup...">
        <i class="fas fa-search absolute left-5 top-5 text-gray-500"></i>
        <button class="absolute right-3 top-3 bg-green-700 px-4 py-1.5 rounded-lg font-bold">SEARCH</button>
    </form>

    <!-- MOVIE LIST -->
    <?php if(empty($movies)): ?>
        <div class="text-center py-20 border border-dashed border-gray-800 rounded-xl">
            <i class="fas fa-search text-5xl text-gray-700 mb-4"></i>
            <p class="text-gray-500">No movies found matching "<?= htmlspecialchars($search) ?>"</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-3">
            <?php foreach($movies as $m): ?>
            <div class="bg-[#111] p-4 rounded-xl border border-gray-800 hover:border-green-600 transition flex items-center justify-between group shadow-md">
                
                <div class="flex items-center gap-4">
                    <img src="../<?= $m['poster_url'] ?>" class="w-12 h-16 object-cover rounded bg-black border border-gray-700">
                    <div>
                        <h3 class="font-bold text-white text-md group-hover:text-green-400 transition"><?= $m['title'] ?></h3>
                        <span class="text-[10px] bg-gray-900 px-2 py-0.5 rounded text-gray-400">ID: <?= $m['id'] ?></span>
                        <span class="text-[10px] text-gray-500">
                            <?= !empty($m['video_file']) ? 'Contains Local File' : 'Link Only' ?>
                        </span>
                    </div>
                </div>

                <!-- SAVE BUTTON -->
                <button onclick="startBackup(<?= $m['id'] ?>)" class="bg-green-900/40 text-green-500 border border-green-800 px-5 py-2 rounded-lg font-bold text-xs hover:bg-green-600 hover:text-white transition shadow-[0_0_10px_rgba(0,255,0,0.2)]">
                    <i class="fas fa-file-zipper"></i> SAVE ZIP
                </button>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <script>
        function startBackup(id) {
            let pass = prompt("🔒 SECURE ACCESS:\n\nEnter Backup Password:");
            
            // YOUR PASSWORD
            if (pass === "kya tum ya hai") {
                // REDIRECT TO PROCESSING PAGE
                window.location.href = `create_zip.php?id=${id}&key=` + encodeURIComponent(pass);
            } else if (pass) {
                alert("❌ ACCESS DENIED: WRONG PASSWORD");
            }
        }
    </script>

</body>
</html>