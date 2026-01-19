<?php
require_once 'common/config.php';
try {
    $pdo->exec("ALTER TABLE movies ADD COLUMN file_size_label VARCHAR(50) DEFAULT NULL");
    echo "<h1 style='color:green'>✅ FILE SIZE OPTION ADDED!</h1>";
} catch (Exception $e) { echo "Already Exists."; }
?>