<?php
// upgrade_plans.php - RUN THIS ONCE
require_once 'common/config.php';

try {
    echo "<body style='background:black; color:white; font-family:sans-serif; padding:20px;'>";
    echo "<h1 style='color:red;'>⚙️ SYSTEM UPGRADE IN PROGRESS...</h1>";

    // 1. Movies Table mein 'required_plan' daalo
    // Options: Free, Silver, Gold, Diamond
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN required_plan VARCHAR(50) DEFAULT 'Free'");
        echo "<p>✅ Movie Plan System: <span style='color:green'>INSTALLED</span></p>";
    } catch (Exception $e) {
        echo "<p>ℹ️ Movie Plan System: <span>ALREADY EXISTS</span></p>";
    }

    // 2. Users Table mein 'sub_plan' daalo
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN sub_plan VARCHAR(50) DEFAULT 'Free'");
        echo "<p>✅ User Plan System: <span style='color:green'>INSTALLED</span></p>";
    } catch (Exception $e) {
        echo "<p>ℹ️ User Plan System: <span>ALREADY EXISTS</span></p>";
    }

    // 3. Transactions Table (Payment History)
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50),
        utr VARCHAR(50),
        amount VARCHAR(20),
        plan VARCHAR(50),
        status VARCHAR(20) DEFAULT 'PENDING',
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>✅ Transaction History: <span style='color:green'>READY</span></p>";

    echo "<hr style='border-color:#333;'>";
    echo "<h2 style='color:#4ade80;'>🎉 UPGRADE COMPLETE!</h2>";
    echo "<a href='admin/dashboard.php' style='background:red; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:bold;'>GO TO DASHBOARD</a>";
    echo "</body>";

} catch (PDOException $e) {
    die("<h1 style='color:red'>CRITICAL ERROR: " . $e->getMessage() . "</h1>");
}
?>