<?php
require_once 'common/config.php';

try {
    echo "<h1>⚙️ RESETTING USER SYSTEM...</h1>";

    // 1. Delete All Users (Clean Start)
    $pdo->exec("TRUNCATE TABLE users");
    echo "✅ All Users Wiped. Everyone must Login again.<br>";

    // 2. Add Ban Expiry Column
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN ban_expiry DATETIME DEFAULT NULL");
        echo "✅ Time-Based Ban System Installed.<br>";
    } catch (Exception $e) {}

    echo "<h2 style='color:green'>🎉 DONE!</h2>";
    echo "<a href='admin/users.php'>Go to User Control</a>";

} catch (Exception $e) { echo $e->getMessage(); }
?>