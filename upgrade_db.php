<?php
require_once 'common/config.php';

try {
    echo "<h3>⚙️ UPGRADING SYSTEM...</h3>";

    // 1. Movies table mein 'video_file' column add karo
    // Hum try/catch use karenge taaki agar column pehle se ho to error na aaye
    try {
        $pdo->exec("ALTER TABLE movies ADD COLUMN video_file VARCHAR(255) DEFAULT NULL");
        echo "✅ Video Upload Support Added.<br>";
    } catch (PDOException $e) { /* Ignore if exists */ }

    // 2. Banned Users ki Table banao
    $pdo->exec("CREATE TABLE IF NOT EXISTS banned_ips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL UNIQUE,
        reason VARCHAR(255),
        banned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Banning System Added.<br>";

    echo "<h2 style='color:green'>🎉 SYSTEM UPGRADED SUCCESSFULLY!</h2>";
    echo "<a href='admin/movies.php'>Go to Admin Panel</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>