<?php
require_once 'common/config.php';

try {
    echo "<body style='background:black; color:white; padding:20px; font-family:sans-serif;'>";
    echo "<h1>🚑 EMERGENCY FIXER RUNNING...</h1>";

    // 1. ENSURE CATEGORY EXISTS
    // Hum ek default category "Action" (ID 1) banayenge agar nahi hai
    $pdo->exec("INSERT IGNORE INTO categories (id, name) VALUES (1, 'Action')");
    echo "<p style='color:green'>✅ Category 'Action' Confirmed.</p>";

    // 2. FIX STATUS (Unhide Movies)
    // Jo movies ka status kuch nahi hai, unhe 'approved' kar do
    $sql = "UPDATE movies SET status = 'approved' WHERE status IS NULL OR status = '' OR status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    echo "<p style='color:yellow'>⚡ Updating Status... ({$stmt->rowCount()} Movies Fixed)</p>";

    // 3. FIX CATEGORY LINK
    // Agar kisi movie ki category ID delete ho gayi thi, to use wapas '1' (Action) par set karo
    $sql_cat = "UPDATE movies SET category_id = 1 WHERE category_id NOT IN (SELECT id FROM categories)";
    $stmt_cat = $pdo->prepare($sql_cat);
    $stmt_cat->execute();
    echo "<p style='color:cyan'>🔗 Relinking Categories... ({$stmt_cat->rowCount()} Movies Fixed)</p>";

    // 4. CHECK TOTAL VISIBLE
    $total = $pdo->query("SELECT COUNT(*) FROM movies WHERE status='approved'")->fetchColumn();
    
    echo "<hr>";
    echo "<h2 style='color:#4ade80'>🎉 SUCCESS! AB $total MOVIES VISIBLE HAIN.</h2>";
    
    echo "<div style='margin-top:20px;'>
            <a href='index.php' style='background:blue; color:white; padding:10px 20px; text-decoration:none; margin-right:10px;'>GO TO HOME PAGE</a>
            <a href='admin/movies.php' style='background:red; color:white; padding:10px 20px; text-decoration:none;'>GO TO ADMIN PANEL</a>
          </div>";

} catch (Exception $e) {
    die("<h3>Error: " . $e->getMessage() . "</h3>");
}
?>