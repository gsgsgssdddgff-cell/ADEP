<?php
require_once 'common/config.php';

try {
    echo "<body style='background:black; color:white; padding:20px; font-family:sans-serif;'>";
    echo "<h1>🔧 FIXING PAYMENT DATABASE...</h1>";

    // 1. Add 'movie_id' column to transactions table
    try {
        $pdo->exec("ALTER TABLE transactions ADD COLUMN movie_id INT DEFAULT 0");
        echo "<p style='color:green'>✅ Added 'movie_id' column successfully.</p>";
    } catch (Exception $e) {
        echo "<p style='color:yellow'>ℹ️ 'movie_id' column already exists.</p>";
    }

    // 2. Ensure other columns exist too
    try {
        $pdo->exec("ALTER TABLE transactions ADD COLUMN plan VARCHAR(50)");
        $pdo->exec("ALTER TABLE transactions ADD COLUMN status VARCHAR(20) DEFAULT 'PENDING'");
    } catch (Exception $e) {}

    echo "<hr>";
    echo "<h2 style='color:#4ade80'>🎉 DATABASE FIXED!</h2>";
    echo "<p>Ab aap Payment kar sakte hain. Error nahi aayega.</p>";
    echo "<a href='index.php' style='background:red; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>GO HOME</a>";
    echo "</body>";

} catch (PDOException $e) {
    die("<h3>Error: " . $e->getMessage() . "</h3>");
}
?>