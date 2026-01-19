<?php
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit; 
}

require_once '../common/config.php';

// ==========================================
// 2. HANDLE BAN LOGIC (TIME BASED)
// ==========================================
if (isset($_POST['ban_user'])) {
    $id = $_POST['user_id'];
    $amount = $_POST['time_amount']; // e.g. 1, 10
    $unit = $_POST['time_unit'];     // e.g. minutes, days
    
    $expiry = null;
    
    if ($unit == 'forever') {
        $expiry = '9999-12-31 23:59:59'; // Permanent Ban
    } else {
        // Calculate Time
        $expiry = date('Y-m-d H:i:s', strtotime("+$amount $unit"));
    }

    // Update Database
    $stmt = $pdo->prepare("UPDATE users SET is_banned = 1, ban_expiry = ? WHERE id = ?");
    $stmt->execute([$expiry, $id]);
    
    echo "<script>window.location='users.php';</script>";
}

// ==========================================
// 3. HANDLE UNBLOCK LOGIC
// ==========================================
if (isset($_GET['unban_id'])) {
    $id = $_GET['unban_id'];
    $pdo->prepare("UPDATE users SET is_banned = 0, ban_expiry = NULL WHERE id = ?")->execute([$id]);
    echo "<script>window.location='users.php';</script>";
}

// ==========================================
// 4. HANDLE DELETE USER LOGIC
// ==========================================
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    echo "<script>window.location='users.php';</script>";
}

// ==========================================
// 5. FETCH ALL USERS
// ==========================================
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Control Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6 pb-20">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8 border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-red-600">USER MANAGER</h1>
            <p class="text-xs text-gray-500">Total Users: <?= count($users) ?></p>
        </div>
        <a href="dashboard.php" class="bg-gray-800 px-4 py-2 rounded text-sm hover:bg-gray-700">Back</a>
    </div>

    <!-- USER LIST -->
    <div class="space-y-4">
        <?php foreach($users as $u): 
            // Check if Ban is Active
            $current_time = date('Y-m-d H:i:s');
            $is_banned = ($u['is_banned'] == 1) && ($u['ban_expiry'] > $current_time);
            
            // Display Name Logic
            $display_name = !empty($u['full_name']) ? $u['full_name'] : $u['username'];
            $uid_display = !empty($u['uid']) ? $u['uid'] : "ID: " . $u['id'];
        ?>
        
        <div class="bg-[#111] p-5 rounded-xl border <?= $is_banned ? 'border-red-600' : 'border-green-600' ?> shadow-lg transition hover:bg-[#161616]">
            
            <!-- TOP ROW: INFO & STATUS -->
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-lg text-white flex items-center gap-2">
                        <?= htmlspecialchars($display_name) ?>
                        <?php if($u['is_premium']): ?>
                            <i class="fas fa-crown text-yellow-500 text-xs" title="Premium User"></i>
                        <?php endif; ?>
                    </h3>
                    <p class="text-xs text-gray-500 font-mono bg-black px-2 py-1 rounded inline-block mt-1">
                        <?= $uid_display ?>
                    </p>
                </div>
                
                <div class="text-right">
                    <?php if($is_banned): ?>
                        <span class="bg-red-600 text-white text-[10px] px-2 py-1 rounded font-bold inline-block mb-1">BANNED ☠️</span>
                        <p class="text-[9px] text-red-400">Until: <?= $u['ban_expiry'] ?></p>
                    <?php else: ?>
                        <span class="bg-green-900 text-green-400 text-[10px] px-2 py-1 rounded font-bold inline-block">ACTIVE</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ACTION AREA -->
            <div class="bg-black p-3 rounded border border-gray-800">
                
                <?php if($is_banned): ?>
                    <!-- UNBLOCK BUTTON -->
                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-400">User is currently blocked.</p>
                        <a href="users.php?unban_id=<?= $u['id'] ?>" class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded font-bold text-xs shadow-lg">
                            <i class="fas fa-unlock"></i> UNBLOCK NOW
                        </a>
                    </div>

                <?php else: ?>
                    <!-- BAN FORM -->
                    <form method="POST" class="flex flex-col gap-2">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        
                        <div class="flex gap-2">
                            <!-- Amount -->
                            <input type="number" name="time_amount" class="bg-[#222] border border-gray-600 text-white text-xs p-2 rounded w-16 text-center focus:border-red-500 outline-none" placeholder="1" value="1" required>
                            
                            <!-- Unit -->
                            <select name="time_unit" class="bg-[#222] border border-gray-600 text-white text-xs p-2 rounded w-full focus:border-red-500 outline-none">
                                <option value="minutes">Minutes</option>
                                <option value="hours">Hours</option>
                                <option value="days">Days</option>
                                <option value="weeks">Weeks</option>
                                <option value="months">Months</option>
                                <option value="years">Years</option>
                                <option value="forever" class="text-red-500 font-bold">PERMANENT (Forever)</option>
                            </select>
                        </div>

                        <button type="submit" name="ban_user" class="w-full bg-red-900/50 border border-red-600 text-red-500 hover:bg-red-600 hover:text-white px-4 py-2 rounded font-bold text-xs transition">
                            <i class="fas fa-gavel"></i> BAN USER
                        </button>
                    </form>
                <?php endif; ?>

            </div>

            <!-- DELETE BUTTON (BOTTOM) -->
            <div class="mt-3 text-right">
                <a href="users.php?delete_id=<?= $u['id'] ?>" class="text-gray-600 hover:text-red-500 text-xs" onclick="return confirm('⚠️ WARNING: Are you sure you want to DELETE this user permanently? This cannot be undone.')">
                    <i class="fas fa-trash"></i> Delete User Data
                </a>
            </div>

        </div>
        <?php endforeach; ?>

        <?php if(empty($users)): ?>
            <div class="text-center py-20 text-gray-600">
                <i class="fas fa-users text-4xl mb-3"></i>
                <p>No users found.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>