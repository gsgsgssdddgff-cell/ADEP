<?php
session_start();
require_once 'common/config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$amount = $_GET['amount'] ?? 0;
$plan = $_GET['plan'] ?? 'Premium';
$movie_id = $_GET['movie_id'] ?? 0;

// Tax Logic
$tax = $amount * 0.05;
$total = $amount + $tax;

// ADMIN/CREATOR UPI
// (Yahan hum Admin ki settings se UPI le rahe hain)
try {
    $set = $pdo->query("SELECT * FROM payment_settings WHERE id=1")->fetch();
    $upi_id = $set['upi_id'] ?? '7822957378@fam';
    $phone = $set['contact_no'] ?? '7822957378';
} catch (Exception $e) { $upi_id = '7822957378@fam'; }

$upi_link = "upi://pay?pa=$upi_id&pn=AdeptCinema&am=$total&cu=INR&tn=$plan";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay ₹<?= $total ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{background:#000;color:white;}</style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-sm bg-[#111] p-6 rounded-2xl border border-yellow-600 shadow-2xl text-center">
        
        <h1 class="text-2xl font-bold mb-2 text-yellow-500">UNLOCK CONTENT</h1>
        <p class="text-gray-400 text-sm mb-4">You are buying: <b><?= htmlspecialchars($plan) ?></b></p>
        
        <div class="bg-gray-900 p-4 rounded-lg mb-6 text-sm text-left">
            <div class="flex justify-between mb-1"><span>Price:</span> <span>₹<?= $amount ?></span></div>
            <div class="flex justify-between mb-1 border-b border-gray-700 pb-2"><span>Tax (5%):</span> <span>+ ₹<?= $tax ?></span></div>
            <div class="flex justify-between font-bold text-lg text-green-400 mt-1"><span>PAYABLE:</span> <span>₹<?= $total ?></span></div>
        </div>

        <p class="text-xs text-blue-400 font-bold mb-2 uppercase">Pay to: <?= $upi_id ?></p>

        <!-- UPI BUTTON -->
        <a href="<?= $upi_link ?>" class="block w-full bg-green-600 hover:bg-green-500 py-3 rounded-xl font-bold mb-6 transition transform hover:scale-105">
            <i class="fas fa-wallet mr-2"></i> PAY VIA UPI APP
        </a>

        <!-- VERIFICATION FORM -->
        <form action="process_payment.php" method="POST" class="border-t border-gray-700 pt-4">
            <input type="hidden" name="amount" value="<?= $total ?>">
            <input type="hidden" name="plan" value="<?= $plan ?>">
            <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
            
            <label class="block text-left text-xs text-gray-500 mb-1 ml-1">Enter Transaction ID (UTR):</label>
            <input type="number" name="utr" placeholder="12-Digit UTR Number" class="w-full bg-black border border-gray-600 p-3 rounded-lg text-white text-center tracking-widest mb-3 focus:border-yellow-500 outline-none" required>
            
            <button class="w-full bg-yellow-600 hover:bg-yellow-500 text-black font-bold py-3 rounded-xl transition">
                VERIFY PAYMENT
            </button>
        </form>

    </div>
</body>
</html>