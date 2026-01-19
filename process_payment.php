<?php
session_start();
require_once 'common/config.php';

// DATA RECEIVE
$utr = $_POST['utr'] ?? '';
$plan = $_POST['plan'] ?? 'Unknown';
$amount = $_POST['amount'] ?? 0;
$movie_id = $_POST['movie_id'] ?? 0;
$user = $_SESSION['user_id'] ?? 'Guest';

// VALIDATION
if (strlen($utr) < 8) { // UTR kam se kam 8-12 digits ka hota hai
    die("<script>alert('❌ Invalid Transaction ID! Please check.'); window.history.back();</script>");
}

// SAVE TO DATABASE
try {
    $stmt = $pdo->prepare("INSERT INTO transactions (username, utr, amount, plan, status, movie_id) VALUES (?, ?, ?, ?, 'PENDING', ?)");
    $stmt->execute([$user, $utr, $amount, $plan, $movie_id]);
} catch (Exception $e) {
    die("Error Saving Payment: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Submitted</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{background:#000; color:white; display:flex; flex-direction:column; justify-content:center; align-items:center; height:100vh; text-align:center;}</style>
</head>
<body>

    <div class="animate-spin rounded-full h-24 w-24 border-t-4 border-b-4 border-yellow-500 mb-6"></div>
    
    <h1 class="text-3xl font-bold text-yellow-500 mb-2">PAYMENT UNDER REVIEW</h1>
    <p class="text-gray-400">UTR: <span class="font-mono text-white bg-gray-800 px-2 rounded"><?= $utr ?></span> submitted.</p>
    
    <div class="bg-gray-900 border border-yellow-600/30 p-4 rounded-xl mt-6 max-w-xs mx-auto">
        <p class="text-sm"><b>Status:</b> <span class="text-yellow-400">⏳ Pending Approval</span></p>
        <p class="text-xs text-gray-500 mt-2">Admin will verify your payment manually. Please wait 10-30 mins.</p>
    </div>

    <!-- WHATSAPP CONTACT -->
    <a href="https://wa.me/917822957378?text=Hello Admin, I paid for movie ID <?= $movie_id ?>. UTR: <?= $utr ?>" class="mt-8 bg-green-600 hover:bg-green-500 text-white px-6 py-3 rounded-full font-bold shadow-lg flex items-center gap-2">
        <i class="fab fa-whatsapp"></i> Chat with Admin
    </a>

    <a href="index.php" class="mt-4 text-gray-500 text-sm hover:text-white underline">Back to Home</a>

</body>
</html>