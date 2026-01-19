<?php
require_once 'common/config.php';

// 30 PRE-LOADED CATEGORIES LIST
$genres = [
    "Action", "Adventure", "Anime", "Animation", "Biography", 
    "Bollywood", "Comedy", "Crime", "Documentary", "Drama", 
    "Family", "Fantasy", "Film-Noir", "History", "Hollywood", 
    "Horror", "K-Drama", "Kids", "Martial Arts", "Musical", 
    "Mystery", "Mythology", "Romance", "Sci-Fi", "Sports", 
    "Superhero", "Thriller", "War", "Web Series", "18+ Adult"
];

try {
    echo "<h1>⚙️ SETTING UP 30 CATEGORIES...</h1>";

    // 1. Purani Categories Delete karo (Clean Start)
    // Hum Foreign Key check band kar rahe hain taaki error na aaye
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE categories");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "✅ Old Categories Cleared.<br>";

    // 2. Nayi 30 Categories Insert karo
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    
    foreach ($genres as $g) {
        $stmt->execute([$g]);
    }
    echo "✅ 30 New Categories Added (Action, Anime, Horror, etc).<br>";

    // 3. Purani Movies ko 'Action' (ID: 1) par set karo taaki wo gayab na hon
    // Kyunki purani IDs delete ho gayi hain
    $pdo->exec("UPDATE movies SET category_id = 1");
    echo "✅ Existing Movies moved to 'Action' category safely.<br>";

    echo "<h2 style='color:green'>🎉 COMPLETE! AB UPLOAD PAGE CHECK KAREIN.</h2>";
    echo "<a href='admin/upload.php' style='font-size:20px; font-weight:bold; background:red; color:white; padding:10px;'>GO TO UPLOAD PAGE</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>