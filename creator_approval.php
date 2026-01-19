<?php
session_start();
require_once '../common/config.php';

// PASSWORD GATE (Same as before)
$ACCESS_KEY = "_____@4252582£:&*£&-£+£72(+#&#&2--£"; 
if (!isset($_SESSION['approval_unlocked'])) {
    if (isset($_POST['auth_pass']) && $_POST['auth_pass'] === $ACCESS_KEY) { $_SESSION['approval_unlocked'] = true; }
    else { /* Show Login Form */ }
}
// ... (Login Form Code Hidden for Brevity) ...

// FETCH PENDING
$pending = $pdo->query("SELECT * FROM movies WHERE status = 'pending'")->fetchAll();
?>
<!-- ... HTML ... -->
<div class="mt-4 bg-gray-800 p-3 rounded border border-gray-600">
    <p class="text-xs text-blue-400 font-bold mb-2">CREATOR BANK DETAILS:</p>
    <p class="text-sm">📞 Phone: <span class="text-white"><?= $m['creator_phone'] ?></span></p>
    <p class="text-sm">💸 UPI: <span class="text-white"><?= $m['creator_upi'] ?></span></p>
    <p class="text-sm">🏦 Bank: <span class="text-white"><?= $m['creator_bank'] ?></span></p>
    <p class="text-sm mt-2">Requested Price: <span class="text-green-400 font-bold">₹<?= $m['price'] ?></span></p>
</div>
<!-- ... Approve Form ... -->