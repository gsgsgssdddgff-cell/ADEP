<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// Get Premium Users
$users = $pdo->query("SELECT * FROM users WHERE is_premium = 1 ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-green-500">ACTIVE SUBSCRIBERS</h1>
        <a href="dashboard.php" class="bg-gray-800 px-3 py-1 rounded">Back</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-500 border-b border-gray-800 text-xs uppercase">
                    <th class="p-3">User</th>
                    <th class="p-3">Plan</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr class="border-b border-gray-800">
                    <td class="p-3 font-bold"><?= $u['username'] ?></td>
                    <td class="p-3 text-yellow-500"><?= $u['sub_plan'] ?></td>
                    <td class="p-3 text-green-500">✅ Active</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if(empty($users)): ?>
            <p class="text-center text-gray-600 mt-10">No subscribers yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>