<?php
require_once 'common/config.php';

try {
    echo "<body style='background:black; color:white; font-family:sans-serif; text-align:center; padding-top:50px;'>";
    echo "<h1 style='color:yellow;'>🔧 FIXING MOVIE VISIBILITY...</h1>";

    // 1. SABKO 'APPROVED' KAR DO (Show on Home)
    $sql = "UPDATE movies SET status = 'approved'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $count = $stmt->rowCount();

    echo "<p>✅ Status Updated: <b style='color:green;'>$count Movies set to Live</b></p>";

    // 2. CHECK CATEGORIES (Agar Category ID tooti hui hai to fix karo)
    // Pehle check karo 'Action' (ID 1) hai ya nahi
    $check_cat = $pdo->query("SELECT COUNT(*) FROM categories WHERE id=1")->fetchColumn();
    if($check_cat == 0) {
        $pdo->exec("INSERT INTO categories (id, name) VALUES (1, 'Action')");
        echo "<p>✅ Default Category Created.</p>";
    }

    // Jin movies ki category gayab hai unhe ID 1 mein daal do
    $pdo->exec("UPDATE movies SET category_id = 1 WHERE category_id = 0 OR category_id IS NULL");
    echo "<p>✅ Broken Categories Fixed.</p>";

    // 3. DONE BUTTON
    echo "<hr style='border-color:#333; margin:20px auto; width:50%;'>";
    echo "<h2 style='color:#4ade80;'>🎉 SAB THIK HO GAYA!</h2>";
    echo "<p>Ab Home Page check karein.</p>";
    echo "<br><br>";
    echo "<a href='index.php' style='background:red; color:white; padding:15px 30px; text-decoration:none; font-weight:bold; border-radius:10px; font-size:18px;'>OPEN HOME PAGE</a>";

} catch (Exception $e) {
    echo "<h3 style='color:red'>Error: " . $e->getMessage() . "</h3>";
}
?>