<?php
require_once 'common/config.php';

try {
    echo "<h3>🛠 Fixiing Database for Emojis...</h3>";

    // 1. Database ka Character Set badlo
    $pdo->exec("ALTER DATABASE adept_cinema_db CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
    echo "✅ Database Updated.<br>";

    // 2. Movies Table ko update karo (Description column ke liye)
    $pdo->exec("ALTER TABLE movies CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Movies Table Updated (Emojis Supported Now).<br>";

    // 3. Categories Table ko update karo
    $pdo->exec("ALTER TABLE categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Categories Table Updated.<br>";

    echo "<h2 style='color:green'>🎉 DONE! Ab aap Emojis Upload kar sakte hain.</h2>";
    echo "<a href='admin/movies.php'>Go Back to Upload</a>";

} catch (PDOException $e) {
    echo "<h3 style='color:red'>Error: " . $e->getMessage() . "</h3>";
}
?>