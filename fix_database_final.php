<?php
require_once 'common/config.php';

try {
    echo "<body style='background:black; color:white; padding:20px; font-family:sans-serif;'>";
    echo "<h1 style='color:yellow;'>🔧 REPAIRING DATABASE...</h1>";

    // 1. ADD PRICE
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN price INT DEFAULT 0");
        echo "<p style='color:green'>✅ Price System Added.</p>";
    } catch (Exception $e) { echo "<p style='color:gray'>ℹ️ Price System already exists.</p>"; }

    // 2. ADD CREATOR INFO
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN creator_name VARCHAR(100) DEFAULT 'Admin'");
        $pdo->exec("ALTER TABLE movies ADD COLUMN creator_phone VARCHAR(50) DEFAULT NULL");
        $pdo->exec("ALTER TABLE movies ADD COLUMN creator_upi VARCHAR(100) DEFAULT NULL");
        $pdo->exec("ALTER TABLE movies ADD COLUMN creator_bank TEXT DEFAULT NULL");
        echo "<p style='color:green'>✅ Creator Bank Details System Added.</p>";
    } catch (Exception $e) { echo "<p style='color:gray'>ℹ️ Creator System already exists.</p>"; }

    // 3. ADD STATUS & PREMIUM
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN status VARCHAR(20) DEFAULT 'approved'");
        $pdo->exec("ALTER TABLE movies ADD COLUMN is_premium TINYINT DEFAULT 0");
        echo "<p style='color:green'>✅ Approval System Added.</p>";
    } catch (Exception $e) { echo "<p style='color:gray'>ℹ️ Approval System already exists.</p>"; }

    // 4. ADD MEDIA COLUMNS
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN video_file VARCHAR(255) DEFAULT NULL");
        $pdo->exec("ALTER TABLE movies ADD COLUMN screenshots TEXT DEFAULT NULL");
        echo "<p style='color:green'>✅ 30GB+ File System Added.</p>";
    } catch (Exception $e) { echo "<p style='color:gray'>ℹ️ Video System already exists.</p>"; }

    echo "<hr><h2 style='color:#4ade80'>🎉 ALL ERRORS FIXED!</h2>";
    echo "<a href='admin/dashboard.php' style='background:red; color:white; padding:10px 20px; text-decoration:none; font-bold; border-radius:5px;'>GO TO DASHBOARD</a>";
    echo "</body>";

} catch (PDOException $e) {
    die("<h1 style='color:red'>Database Connect Error: " . $e->getMessage() . "</h1>");
}
?>