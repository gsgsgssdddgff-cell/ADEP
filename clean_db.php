<?php
require_once 'common/config.php';

try {
    echo "<h1>🧹 CLEANING DATABASE...</h1>";

    // 1. Saari Movies ko Temporary Category (ID: 9999) par move karo
    // Taaki jab hum categories delete karein to movies delete na ho jayein
    // Note: Agar foreign key constant hai to pehle drop karna padega, 
    // lekin simple setup ke liye hum seedha categories reset karenge.

    // Sabse pehle purani categories uda do
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0"); // Safety off
    $pdo->exec("TRUNCATE TABLE categories");   // Delete all categories
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1"); // Safety on
    echo "✅ Duplicate Categories Deleted.<br>";

    // 2. Fresh 30 Categories Daalo
    $genres = [
        "Action", "Adventure", "Anime", "Animation", "Biography", 
        "Bollywood", "Comedy", "Crime", "Documentary", "Drama", 
        "Family", "Fantasy", "Film-Noir", "History", "Hollywood", 
        "Horror", "Korean Drama", "Musical", "Mystery", "Romance", 
        "Sci-Fi", "Short", "Sport", "South Indian", "Superhero", 
        "Thriller", "War", "Western", "Web Series", "18+ Adult"
    ];

    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    foreach ($genres as $g) {
        $stmt->execute([$g]);
    }
    echo "✅ New Clean Categories Added.<br>";

    // 3. AB SABSE ZAROORI KAAM
    // Database mein jitni bhi movies hain, un sabko PEHLI CATEGORY (Action) mein daal do.
    // Isse wo sab turant EK LINE mein aa jayengi.
    
    // Pehli category ki ID nikalo
    $first_cat = $pdo->query("SELECT id FROM categories WHERE name='Action'")->fetchColumn();

    // Update movies
    $update = $pdo->prepare("UPDATE movies SET category_id = ?");
    $update->execute([$first_cat]);

    echo "✅ All Movies Moved to 'Action'. Now they will be in ONE ROW.<br>";
    echo "<h2 style='color:green'>🎉 FIXED! AB HOME PAGE CHECK KARO.</h2>";
    echo "<a href='index.php'>Go to Home Page</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>