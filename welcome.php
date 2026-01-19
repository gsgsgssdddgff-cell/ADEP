<?php
session_start();
require_once 'common/config.php';

if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$error = "";
if (isset($_POST['start_app'])) {
    $name = trim($_POST['full_name']);
    
    if (strlen($name) < 3) {
        $error = "⚠️ Name too short!";
    } else {
        // Generate UID
        $uid = "ADEPT-" . rand(10000, 99999);

        // Create User
        try {
            // Ensure columns exist (Auto-Fix)
            try { $pdo->exec("ALTER TABLE users ADD COLUMN uid VARCHAR(20)"); } catch(Exception $e){}
            try { $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(100)"); } catch(Exception $e){}
            try { $pdo->exec("ALTER TABLE users ADD COLUMN is_banned TINYINT DEFAULT 0"); } catch(Exception $e){}
            try { $pdo->exec("ALTER TABLE users ADD COLUMN ban_expiry DATETIME DEFAULT NULL"); } catch(Exception $e){}

            $sql = "INSERT INTO users (username, full_name, uid, password) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$uid, $name, $uid, password_hash('guest', PASSWORD_DEFAULT)]);
            
            $_SESSION['user_id'] = $uid;
            header("Location: index.php");
            exit;
        } catch (Exception $e) { $error = "Error: " . $e->getMessage(); }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { background: #000; color: white; }</style>
</head>
<body class="flex flex-col items-center justify-center h-screen p-6 text-center bg-[url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_small.jpg')] bg-cover">
    <div class="absolute inset-0 bg-black/80"></div>
    <div class="relative z-10 w-full max-w-sm bg-[#111] p-8 rounded-2xl border border-red-900 shadow-2xl">
        <h1 class="text-4xl font-black text-red-600 mb-2 tracking-tighter">ADEPT CINEMA</h1>
        <p class="text-gray-400 mb-6 text-sm">Enter your name to join.</p>
        <?php if($error): ?><div class="bg-red-900/50 text-red-200 p-3 rounded mb-4 text-xs font-bold border border-red-600"><?= $error ?></div><?php endif; ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="full_name" placeholder="e.g. KingCobra" class="w-full bg-black border border-gray-700 p-4 rounded-xl text-white text-center text-lg focus:border-red-600 outline-none" required>
            <button type="submit" name="start_app" class="w-full bg-gradient-to-r from-red-600 to-red-900 py-4 rounded-xl font-bold text-xl shadow-lg hover:scale-105 transition">ENTER APP 🚀</button>
        </form>
    </div>
</body>
</html>