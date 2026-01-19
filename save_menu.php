<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6">

    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-800">
        <h1 class="text-2xl font-bold text-green-500">BACKUP AI</h1>
        <a href="dashboard.php" class="text-sm text-gray-500 hover:text-white">Exit</a>
    </div>

    <div class="grid gap-6">
        
        <!-- SAVE NEW -->
        <a href="backup_list.php" class="bg-gray-900 border border-green-800 p-6 rounded-xl hover:bg-black transition flex items-center gap-4 group">
            <div class="bg-green-900/20 p-4 rounded-full text-green-500 group-hover:scale-110 transition">
                <i class="fas fa-save text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-white">New Backup Scan</h3>
                <p class="text-xs text-gray-500">Scan library and create secure ZIPs</p>
            </div>
        </a>

        <!-- MANAGE -->
        <a href="manage_backups.php" class="bg-gray-900 border border-blue-800 p-6 rounded-xl hover:bg-black transition flex items-center gap-4 group">
            <div class="bg-blue-900/20 p-4 rounded-full text-blue-500 group-hover:scale-110 transition">
                <i class="fas fa-server text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-white">Vault Manager</h3>
                <p class="text-xs text-gray-500">View saved movies & restore points</p>
            </div>
        </a>

        <!-- RESTORE -->
        <a href="restore_zip.php" class="bg-gray-900 border border-yellow-800 p-6 rounded-xl hover:bg-black transition flex items-center gap-4 group">
            <div class="bg-yellow-900/20 p-4 rounded-full text-yellow-500 group-hover:scale-110 transition">
                <i class="fas fa-history text-2xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-white">Emergency Restore</h3>
                <p class="text-xs text-gray-500">Restore deleted content from ZIP</p>
            </div>
        </a>

    </div>

</body>
</html>