<?php
require_once 'common/config.php';

try {
    // 1. Create Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS payment_settings (
        id INT PRIMARY KEY,
        upi_id VARCHAR(100),
        bank_name VARCHAR(100),
        acc_no VARCHAR(100),
        ifsc VARCHAR(100),
        contact_no VARCHAR(20)
    )");

    // 2. Insert Default Data (Agar khali hai)
    $check = $pdo->query("SELECT COUNT(*) FROM payment_settings")->fetchColumn();
    if ($check == 0) {
        $sql = "INSERT INTO payment_settings (id, upi_id, bank_name, acc_no, ifsc, contact_no) 
                VALUES (1, '7822957378@fam', 'SBI Bank', '1234567890', 'SBIN0001234', '7822957378')";
        $pdo->exec($sql);
    }

    echo "<h1 style='color:green'>✅ SETTINGS SYSTEM READY!</h1>";
    echo "<a href='admin/settings.php'>Go to Admin Settings</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>