<?php
session_start();
require_once 'common/config.php';
$msgs = $pdo->query("SELECT * FROM admin_messages ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{background:#000;color:white;}</style>
</head>
<body class="p-4">

    <!-- TOP BAR -->
    <div class="flex items-center gap-3 border-b border-gray-800 pb-3 mb-4 sticky top-0 bg-black z-50 pt-2">
        <a href="index.php"><i class="fas fa-arrow-left text-gray-400 text-lg"></i></a>
        <div class="flex-1">
            <h1 class="text-lg font-bold text-white flex items-center gap-2">
                Adept Official <i class="fas fa-check-circle text-blue-500 text-sm"></i>
            </h1>
            <p class="text-[10px] text-green-500">online</p>
        </div>
        <i class="fas fa-ellipsis-v text-gray-500"></i>
    </div>

    <!-- MESSAGES LIST -->
    <div class="space-y-4 pb-10">
        
        <?php foreach($msgs as $m): ?>
        <div class="flex gap-2">
            <!-- ADMIN DP -->
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-red-600 to-purple-600 flex items-center justify-center shrink-0 border border-white/20">
                <i class="fas fa-crown text-xs text-white"></i>
            </div>
            
            <!-- BUBBLE -->
            <div class="bg-[#1f2c34] text-white p-3 rounded-tr-xl rounded-bl-xl rounded-br-xl max-w-[85%] border border-gray-800 shadow-md">
                <p class="text-xs text-blue-400 font-bold mb-1"><?= htmlspecialchars($m['title']) ?></p>
                <p class="text-sm leading-relaxed text-gray-200"><?= nl2br(htmlspecialchars($m['message'])) ?></p>
                
                <div class="text-[9px] text-gray-500 text-right mt-2 flex items-center justify-end gap-1">
                    <?= date("h:i A", strtotime($m['created_at'])) ?>
                    <i class="fas fa-check-double text-blue-500"></i>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($msgs)): ?>
            <div class="text-center py-20 text-gray-600">
                <p>No messages yet.</p>
                <p class="text-xs mt-2">Important announcements will appear here.</p>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>