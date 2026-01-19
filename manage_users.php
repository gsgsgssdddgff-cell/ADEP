<?php
session_start();
// ONLY ALLOW ADMIN OR OWNER
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// HANDLE ROLE CHANGE
if (isset($_POST['update_role'])) {
    $uid = $_POST['user_uid'];
    $role = $_POST['new_role']; // user, admin, owner
    
    $pdo->prepare("UPDATE users SET role = ? WHERE uid = ?")->execute([$role, $uid]);
    echo "<script>alert('✅ Role Updated to $role!'); window.location='manage_users.php';</script>";
}

// HANDLE BAN (Quick Ban)
if (isset($_GET['ban'])) {
    $pdo->prepare("UPDATE users SET is_banned = 1, ban_expiry = '9999-12-31 00:00:00' WHERE uid = ?")->execute([$_GET['ban']]);
    header("Location: manage_users.php");
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users & Roles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6 pb-20">

    <div class="flex justify-between items-center mb-8 border-b border-gray-800 pb-4">
        <h1 class="text-2xl font-bold text-blue-500">USER POSITIONS</h1>
        <a href="dashboard.php" class="bg-gray-800 px-4 py-2 rounded text-sm">Dashboard</a>
    </div>

    <div class="space-y-4">
        <?php foreach($users as $u): 
            $color = "border-gray-800";
            if($u['role'] == 'owner') $color = "border-red-600 shadow-[0_0_15px_red]";
            if($u['role'] == 'admin') $color = "border-blue-600 shadow-[0_0_10px_blue]";
        ?>
        <div class="bg-[#111] p-4 rounded-xl border <?= $color ?>">
            
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-bold text-lg"><?= htmlspecialchars($u['full_name']) ?></h3>
                    <p class="text-xs text-gray-500 font-mono">UID: <?= $u['uid'] ?></p>
                </div>
                <span class="text-xs font-bold uppercase bg-gray-800 px-2 py-1 rounded">
                    <?= $u['role'] ?>
                </span>
            </div>

            <!-- ROLE CHANGER FORM -->
            <form method="POST" class="flex gap-2">
                <input type="hidden" name="user_uid" value="<?= $u['uid'] ?>">
                
                <select name="new_role" class="bg-black border border-gray-600 text-white text-xs p-2 rounded w-full">
                    <option value="user" <?= $u['role']=='user'?'selected':'' ?>>Normal User</option>
                    <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                    <option value="owner" <?= $u['role']=='owner'?'selected':'' ?>>🔥 Super Owner</option>
                </select>
                
                <button type="submit" name="update_role" class="bg-blue-600 px-4 py-2 rounded font-bold text-xs hover:bg-blue-500">
                    SET
                </button>
            </form>

            <div class="mt-3 text-right">
                <a href="?ban=<?= $u['uid'] ?>" onclick="return confirm('Ban this user?')" class="text-red-500 text-xs font-bold hover:text-white">
                    <i class="fas fa-gavel"></i> PERMANENT BAN
                </a>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

</body>
</html>