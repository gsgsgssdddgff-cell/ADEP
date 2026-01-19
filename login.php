<?php
// login.php (Put in Root folder)
session_start();
require_once 'common/config.php';

$mode = $_GET['mode'] ?? 'login'; // login or signup

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    if (isset($_POST['do_signup'])) {
        // SIGNUP LOGIC
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)")->execute([$email, $hash]);
            $_SESSION['user_id'] = $email;
            header("Location: index.php");
        } catch(Exception $e) { $error = "Email already exists!"; }
    } else {
        // LOGIN LOGIC
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$email]);
        $u = $stmt->fetch();
        if ($u && password_verify($pass, $u['password'])) {
            $_SESSION['user_id'] = $u['username'];
            header("Location: index.php");
        } else {
            $error = "Invalid Credentials";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-black text-white flex items-center justify-center h-screen p-4 bg-[url('https://assets.nflxext.com/ffe/siteui/vlv3/f841d4c7-10e1-40af-bcae-07a3f8dc141a/f6d7434e-d6de-4185-a6d4-c77a2d08737b/US-en-20220502-popsignuptwoweeks-perspective_alpha_website_small.jpg')] bg-cover">
    
    <div class="absolute inset-0 bg-black/60"></div>

    <div class="relative bg-black/80 p-8 rounded-xl w-full max-w-sm backdrop-blur border border-gray-700">
        <h1 class="text-3xl font-bold mb-6 text-red-600">ADEPT CINEMA</h1>
        
        <h2 class="text-xl font-bold mb-4"><?= $mode == 'signup' ? 'Create Account' : 'Sign In' ?></h2>
        
        <?php if(isset($error)) echo "<p class='bg-red-600/50 p-2 rounded text-sm mb-4'>$error</p>"; ?>

        <form method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Email Address" class="w-full bg-gray-700 p-3 rounded text-white" required>
            <input type="password" name="password" placeholder="Password" class="w-full bg-gray-700 p-3 rounded text-white" required>
            
            <?php if($mode == 'signup'): ?>
                <button type="submit" name="do_signup" class="w-full bg-red-600 py-3 rounded font-bold">Sign Up</button>
            <?php else: ?>
                <button type="submit" name="do_login" class="w-full bg-red-600 py-3 rounded font-bold">Sign In</button>
            <?php endif; ?>
        </form>

        <div class="mt-6 text-sm text-gray-400">
            <?php if($mode == 'signup'): ?>
                Already have an account? <a href="login.php?mode=login" class="text-white font-bold">Sign In</a>
            <?php else: ?>
                New to Adept Cinema? <a href="login.php?mode=signup" class="text-white font-bold">Sign up now</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>