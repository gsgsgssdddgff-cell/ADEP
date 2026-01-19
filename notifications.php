<?php
session_start();
require_once 'common/config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$user = $_SESSION['user_id'];

// FETCH APPROVED TRANSACTIONS
$notifs = $pdo->prepare("
    SELECT t.*, m.title, m.poster_url 
    FROM transactions t 
    JOIN movies m ON t.movie_id = m.id 
    WHERE t.username = ? AND t.status = 'APPROVED' 
    ORDER BY t.id DESC
");
$notifs->execute([$user]);
$list = $notifs->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background-color: #000; color: white; }</style>
</head>
<body class="p-4">

    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
        <h1 class="text-xl font-bold text-white">NOTIFICATIONS</h1>
        <a href="index.php" class="bg-gray-800 px-3 py-1 rounded text-sm">Back</a>
    </div>

    <div class="space-y-4">
        <?php foreach($list as $n): ?>
        <div class="bg-[#111] p-4 rounded-xl border-l-4 border-green-500 flex gap-4 items-center">
            <img src="<?= $n['poster_url'] ?>" class="w-12 h-16 object-cover rounded">
            <div>
                <h3 class="font-bold text-green-400 text-sm">Purchase Successful!</h3>
                <p class="text-xs text-gray-300 mt-1">You have unlocked <b><?= $n['title'] ?></b>.</p>
                <a href="movie_details.php?id=<?= $n['movie_id'] ?>" class="inline-block mt-2 text-xs bg-blue-600 px-3 py-1 rounded font-bold">WATCH NOW</a>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($list)): ?>
            <div class="text-center py-20 text-gray-600">
                <i class="fas fa-bell-slash text-4xl mb-3"></i>
                <p>No notifications yet.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>