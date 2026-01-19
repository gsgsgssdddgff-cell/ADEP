<?php
session_start();
require_once 'common/config.php';

// Agar login nahi hai to Login page par bhejo
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Plan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #000; color: white; font-family: sans-serif; }
        .gold-grad { background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); -webkit-background-clip: text; color: transparent; }
        .plan-card { border: 1px solid #333; transition: 0.3s; }
        .plan-card:hover { border-color: #ffd700; transform: scale(1.02); }
    </style>
</head>
<body class="pb-20 p-4">

    <!-- HEADER -->
    <div class="text-center mb-8 mt-4">
        <h1 class="text-3xl font-black gold-grad uppercase">Premium Access</h1>
        <p class="text-gray-400 text-sm mt-1">Unlock "Love Conquest" & "Your Fault"</p>
    </div>

    <!-- DAILY PLANS (MICRO) -->
    <h3 class="text-gray-500 text-xs font-bold uppercase mb-3 ml-1">Short Term</h3>
    <div class="grid grid-cols-2 gap-3 mb-6">
        <a href="payment.php?amount=9&plan=1_Day_Pass" class="plan-card bg-[#111] p-4 rounded-xl text-center">
            <h2 class="text-2xl font-bold text-white">₹9</h2>
            <p class="text-[10px] text-gray-400">24 Hours Access</p>
        </a>
        <a href="payment.php?amount=12&plan=Weekend_Pass" class="plan-card bg-[#111] p-4 rounded-xl text-center">
            <h2 class="text-2xl font-bold text-white">₹12</h2>
            <p class="text-[10px] text-gray-400">Weekend Pass</p>
        </a>
    </div>

    <!-- REGULAR PLANS -->
    <h3 class="text-gray-500 text-xs font-bold uppercase mb-3 ml-1">Recommended</h3>
    <div class="space-y-3">
        <?php 
        $plans = [
            ["200", "Monthly", "Standard Access"],
            ["600", "Quarterly", "3 Months Pack"],
            ["700", "Half-Year", "6 Months Saver"],
            ["1200", "Yearly", "12 Months VIP"],
            ["1800", "Family", "4 Screens 4K"]
        ];
        foreach($plans as $p): ?>
        <a href="payment.php?amount=<?= $p[0] ?>&plan=<?= $p[1] ?>" class="plan-card block bg-[#111] p-4 rounded-xl flex justify-between items-center">
            <div>
                <h3 class="font-bold text-yellow-500"><?= $p[1] ?></h3>
                <p class="text-xs text-gray-400"><?= $p[2] ?></p>
            </div>
            <div class="text-xl font-bold text-white">₹<?= $p[0] ?></div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- ULTRA PREMIUM -->
    <h3 class="text-gray-500 text-xs font-bold uppercase mb-3 mt-6 ml-1">Ultra Rich Only</h3>
    <div class="space-y-3">
        <a href="payment.php?amount=990000&plan=Lifetime_King" class="plan-card block bg-gradient-to-r from-gray-900 to-black border-yellow-600 p-5 rounded-xl text-center border">
            <i class="fas fa-crown text-3xl text-yellow-500 mb-2"></i>
            <h3 class="font-bold text-white">LIFETIME KING</h3>
            <p class="text-2xl font-black text-yellow-400 my-1">₹9,90,000</p>
            <p class="text-[10px] text-gray-400">Unlimited Everything Forever</p>
        </a>

        <a href="payment.php?amount=1000000000&plan=The_Owner" class="plan-card block bg-white text-black p-5 rounded-xl text-center border-4 border-yellow-500">
            <h3 class="font-black text-xl">THE BILLIONAIRE</h3>
            <p class="text-xs">Own The App Experience</p>
            <p class="text-3xl font-black mt-2">₹1 Billion</p>
        </a>
    </div>

    <div class="text-center mt-8 mb-8">
        <a href="index.php" class="text-gray-500 text-sm">Cancel</a>
    </div>

</body>
</html>