<?php
require_once 'common/config.php';

try {
    echo "<h1>🔧 FIXING EMOJI SUPPORT...</h1>";
    
    // 1. Database Charset Change
    $pdo->exec("ALTER DATABASE adept_cinema_db CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
    
    // 2. Movies Table Fix
    $pdo->exec("ALTER TABLE movies CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 3. Users & Transactions Fix
    $pdo->exec("ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE transactions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    echo "<h2 style='color:green'>✅ DONE! NOW YOU CAN USE EMOJIS.</h2>";
    echo "<a href='admin/movies.php'>Go Back</a>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>