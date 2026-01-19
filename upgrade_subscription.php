<?php
require_once 'common/config.php';

try {
    echo "<h1>⚙️ UPGRADING DATABASE FOR SUBSCRIPTIONS...</h1>";

    // 1. Movies table mein 'is_premium' column add karo
    // 0 = Free, 1 = Premium (Paid)
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN is_premium TINYINT DEFAULT 0");
        echo "✅ Movie Premium Lock Added.<br>";
    } catch (Exception $e) {}

    // 2. Users table mein Subscription info add karo
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN sub_plan VARCHAR(50) DEFAULT 'free'");
        $pdo->exec("ALTER TABLE users ADD COLUMN sub_expiry DATETIME DEFAULT NULL");
        echo "✅ User Subscription Data Added.<br>";
    } catch (Exception $e) {}

    echo "<h2 style='color:green'>🎉 SYSTEM UPGRADED!</h2>";
    echo "<a href='admin/upload.php'>Go to Upload (Now with Premium Option)</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>