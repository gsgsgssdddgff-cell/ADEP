<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// APPROVE
if (isset($_GET['action']) && $_GET['action'] == 'approve') {
    $id = $_GET['id'];
    $pdo->prepare("UPDATE transactions SET status = 'APPROVED' WHERE id = ?")->execute([$id]);
    echo "<script>alert('✅ Payment Approved!'); window.location='approve_payment.php';</script>";
}

// REJECT
if (isset($_GET['action']) && $_GET['action'] == 'reject') {
    $pdo->prepare("UPDATE transactions SET status = 'REJECTED' WHERE id = ?")->execute([$_GET['id']]);
}

$pending = $pdo->query("SELECT * FROM transactions WHERE status = 'PENDING' ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-4">

    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
        <h1 class="text-xl font-bold text-yellow-500">PAYMENT REQUESTS</h1>
        <a href="dashboard.php" class="bg-gray-800 px-3 py-1 rounded text-sm">Back</a>
    </div>

    <div class="space-y-4">
        <?php foreach($pending as $p): ?>
        <div class="bg-[#111] p-4 rounded-xl border border-yellow-600">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-bold text-lg text-white">₹<?= $p['amount'] ?></h3>
                    <p class="text-xs text-gray-400"><?= $p['plan'] ?></p>
                </div>
                <span class="bg-yellow-900 text-yellow-500 text-[10px] px-2 py-1 rounded">PENDING</span>
            </div>
            
            <div class="bg-black p-2 rounded border border-gray-700 font-mono text-yellow-400 text-lg text-center tracking-widest mb-3 select-all">
                <?= $p['utr'] ?>
            </div>
            
            <p class="text-xs text-gray-500 mb-4">User: <?= $p['username'] ?></p>

            <div class="flex gap-2">
                <a href="approve_payment.php?action=reject&id=<?= $p['id'] ?>" class="flex-1 bg-red-900/50 border border-red-600 text-red-500 py-2 rounded font-bold text-center text-sm">FAKE ❌</a>
                <a href="approve_payment.php?action=approve&id=<?= $p['id'] ?>" class="flex-1 bg-green-600 text-white py-2 rounded font-bold text-center text-sm shadow-lg">APPROVE ✅</a>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($pending)): ?>
            <p class="text-center text-gray-600 mt-10">No Pending Requests.</p>
        <?php endif; ?>
    </div>

</body>
</html>