<?php
require_once 'common/config.php';

try {
    echo "<body style='background:black; color:white; padding:20px; font-family:sans-serif;'>";
    echo "<h1>🔧 FIXING BAN SYSTEM...</h1>";

    // 1. Add 'is_banned' (0 = No, 1 = Yes)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_banned TINYINT DEFAULT 0");
        echo "<p style='color:green'>✅ Added 'is_banned' column.</p>";
    } catch (Exception $e) { echo "<p style='color:yellow'>ℹ️ 'is_banned' already exists.</p>"; }

    // 2. Add 'ban_expiry' (Time Limit)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN ban_expiry DATETIME DEFAULT NULL");
        echo "<p style='color:green'>✅ Added 'ban_expiry' column.</p>";
    } catch (Exception $e) { echo "<p style='color:yellow'>ℹ️ 'ban_expiry' already exists.</p>"; }

    // 3. Add 'uid' & 'full_name' (If missing)
    try { $pdo->exec("ALTER TABLE users ADD COLUMN uid VARCHAR(20)"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN full_name VARCHAR(100)"); } catch (Exception $e) {}

    echo "<hr>";
    echo "<h2 style='color:#4ade80'>🎉 DATABASE FIXED! AB BAN KAAM KAREGA.</h2>";
    echo "<a href='admin/users.php' style='background:red; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>GO TO USER CONTROL</a>";
    echo "</body>";

} catch (PDOException $e) {
    die("<h3>Error: " . $e->getMessage() . "</h3>");
}
?>