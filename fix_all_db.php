<?php
require_once 'common/config.php';

try {
    echo "<body style='background:black;color:green;padding:20px;font-family:sans-serif;'>";
    echo "<h1>🛠 FIXING DATABASE...</h1>";

    // 1. Transactions Table (Payment Records)
    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50),
        utr VARCHAR(50),
        amount VARCHAR(20),
        plan VARCHAR(50),
        status VARCHAR(20) DEFAULT 'PENDING',
        movie_id INT DEFAULT 0,
        viewed INT DEFAULT 0,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Transactions Table OK<br>";

    // 2. Fix Movies Table (Add Price, Creator Info)
    $cols = [
        "ALTER TABLE movies ADD COLUMN price INT DEFAULT 0",
        "ALTER TABLE movies ADD COLUMN is_premium TINYINT DEFAULT 0",
        "ALTER TABLE movies ADD COLUMN creator_name VARCHAR(100) DEFAULT 'Admin'",
        "ALTER TABLE movies ADD COLUMN status VARCHAR(20) DEFAULT 'approved'"
    ];

    foreach ($cols as $sql) {
        try { $pdo->exec($sql); } catch (Exception $e) {}
    }
    echo "✅ Movie Columns OK<br>";

    echo "<h2>🎉 DATABASE READY! AB PAYMENT KAAM KAREGA.</h2>";
    echo "<a href='index.php' style='color:white;background:red;padding:10px;'>GO HOME</a>";

} catch (Exception $e) { echo $e->getMessage(); }
?>