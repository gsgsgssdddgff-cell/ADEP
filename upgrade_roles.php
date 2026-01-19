<?php
require_once 'common/config.php';

try {
    echo "<h1>⚙️ UPGRADING FOR OWNER CONTROL...</h1>";

    // 1. ADD ROLE COLUMN TO USERS
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
        echo "✅ User Roles Added.<br>";
    } catch (Exception $e) {}

    // 2. CREATE MESSAGES TABLE (Broadcast System)
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(100),
        message TEXT,
        sender_name VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Admin Message System Ready.<br>";

    // 3. SET YOUR ACCOUNT AS SUPER OWNER
    // Apna username 'admin' ko OWNER bana raha hoon
    $pdo->prepare("UPDATE users SET role = 'owner' WHERE username = 'admin'")->execute();
    echo "✅ Admin is now SUPER OWNER.<br>";

    echo "<h2>🎉 DONE! SYSTEM READY.</h2>";
    echo "<a href='admin/manage_users.php' style='color:red;'>Go to User Manager</a>";

} catch (PDOException $e) { echo $e->getMessage(); }
?>