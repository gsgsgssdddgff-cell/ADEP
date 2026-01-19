<?php
require_once 'common/config.php';

$genres = [
    "Action", "Adventure", "Anime", "Animation", "Biography", 
    "Bollywood", "Comedy", "Crime", "Documentary", "Drama", 
    "Family", "Fantasy", "Film-Noir", "History", "Hollywood", 
    "Horror", "Korean Drama", "Musical", "Mystery", "Romance", 
    "Sci-Fi", "Short", "Sport", "South Indian", "Superhero", 
    "Thriller", "War", "Western", "Web Series", "18+ Adult"
];

try {
    echo "<h1>⚙️ FIXING CATEGORIES...</h1>";

    // 1. Purani Categories Delete karo
    $pdo->exec("DELETE FROM categories");
    $pdo->exec("ALTER TABLE categories AUTO_INCREMENT = 1");
    echo "✅ Old Categories Deleted.<br>";

    // 2. 30 Nayi Categories Daalo
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    
    foreach ($genres as $g) {
        $stmt->execute([$g]);
    }
    echo "✅ 30 New Categories Added.<br>";

    // 3. Purani Movies ko 'Action' (ID: 1) mein shift karo taaki wo delete na ho
    // Kyunki purani category ID ab exist nahi karti
    $pdo->exec("UPDATE movies SET category_id = 1");
    echo "✅ Existing Movies Moved to 'Action' (You can edit them later).<br>";

    echo "<h2 style='color:green'>🎉 PROCESS COMPLETE!</h2>";
    echo "<a href='index.php'>Go to Home Page</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>