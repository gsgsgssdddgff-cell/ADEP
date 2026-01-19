<?php
require_once 'common/config.php';

try {
    echo "<h1>🔧 REPAIRING OLD MOVIES...</h1>";

    // 1. Check agar 'Action' category exist karti hai (ID: 1)
    $check = $pdo->query("SELECT id FROM categories WHERE id = 1");
    if ($check->rowCount() == 0) {
        // Agar nahi hai, to banao
        $pdo->exec("INSERT INTO categories (id, name) VALUES (1, 'Action')");
        echo "✅ Created Default 'Action' Category.<br>";
    }

    // 2. Aisi movies dhoondo jinki Category ID galat hai ya delete ho chuki hai
    // Un sabko 'Action' (ID: 1) mein daal do taaki wo ek line mein aa jayein
    $sql = "UPDATE movies 
            SET category_id = 1 
            WHERE category_id NOT IN (SELECT id FROM categories)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $count = $stmt->rowCount();

    if($count > 0) {
        echo "<h2 style='color:green'>✅ SUCCESS: $count Purani Movies ko fix kar diya gaya!</h2>";
        echo "<p>Wo sab ab 'Action' category mein dikhengi. Aap Admin Panel se unhe edit kar sakte hain.</p>";
    } else {
        echo "<h2 style='color:blue'>👌 Sab kuch pehle se theek hai. Koi purani movie kharab nahi thi.</h2>";
    }

    echo "<br><a href='index.php' style='font-size:20px; font-weight:bold;'>👉 AB HOME PAGE CHECK KAREIN</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>