<?php
require_once 'common/config.php';

try {
    echo "<h1>♻️ RESTORING MOVIES FROM FOLDER...</h1>";

    $folder = "uploads/";
    if (!is_dir($folder)) die("❌ Uploads folder not found!");

    $files = scandir($folder);
    $restored_count = 0;

    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;

        // Sirf Video Files dhoondo
        if (preg_match('/\.(mp4|mkv|avi|mov|webm)$/i', $file)) {
            
            $path = "uploads/" . $file;
            
            // Check karo agar ye movie pehle se Database mein hai
            $check = $pdo->prepare("SELECT id FROM movies WHERE video_file = ?");
            $check->execute([$path]);

            if ($check->rowCount() == 0) {
                // Agar nahi hai, to wapas insert karo
                $title = pathinfo($file, PATHINFO_FILENAME);
                $title = str_replace(['_vid_', '_'], ' ', $title); // Clean Name
                
                // Poster dhoondo (Same naam ka image)
                $poster = "uploads/default_poster.jpg"; 
                $possible_img = str_replace(['.mp4','.mkv'], '.jpg', $file);
                if (file_exists("uploads/" . $possible_img)) { $poster = "uploads/" . $possible_img; }

                // Insert into DB
                $sql = "INSERT INTO movies (title, category_id, description, rating, release_year, video_file, poster_url, status, price, is_premium) 
                        VALUES (?, 1, 'Restored from Backup', 'N/A', 2025, ?, ?, 'approved', 0, 0)";
                
                $pdo->prepare($sql)->execute([$title, $path, $poster]);
                $restored_count++;
                echo "✅ Restored: <b>$title</b><br>";
            }
        }
    }

    if ($restored_count == 0) {
        echo "<h3>⚠️ No new files found to restore (Database is up to date).</h3>";
    } else {
        echo "<h2>🎉 SUCCESS! $restored_count Movies Restored.</h2>";
    }
    echo "<a href='index.php' style='background:green; color:white; padding:10px;'>Go to Home</a>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>