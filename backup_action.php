<?php
// CLEAR ALL BUFFERS (CRITICAL FIX FOR ERRORS)
while (ob_get_level()) ob_end_clean();
session_start();

require_once '../common/config.php';

// 1. UNLIMITED TIME & MEMORY
@ini_set('memory_limit', '-1'); 
@ini_set('max_execution_time', 0);

// 2. CHECK KEY & ID
$key = $_GET['key'] ?? '';
$id = $_GET['id'] ?? 0;
if ($key !== "kya tum ya hai") { die("❌ WRONG PASSWORD"); }

// 3. MAIN LOGIC (AJAX REQUESTS LAND HERE)
if (isset($_GET['execute']) || isset($_GET['key'])) {
    
    // FETCH MOVIE
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $m = $stmt->fetch();
    
    if (!$m) die("❌ Movie Not Found in Database.");

    // PATH SETUP (FIXED)
    $save_folder = __DIR__ . '/../saved_movies/';
    $uploads_folder = __DIR__ . '/../uploads/';

    // Create Backup Folder
    if (!file_exists($save_folder)) mkdir($save_folder, 0777, true);

    // Create Movie Specific Folder inside saved_movies (NO ZIP - DIRECT COPY IS SAFER FOR LARGE FILES)
    $clean_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $m['title']);
    $backup_dir = $save_folder . "BACKUP_" . $clean_name . "_" . time() . "/";
    
    if (!mkdir($backup_dir, 0777, true)) die("❌ Cannot create backup directory.");

    // A. SAVE TEXT DATA
    $info = "Title: " . $m['title'] . "\r\n";
    $info .= "Description: " . $m['description'] . "\r\n";
    $info .= "Price: " . $m['price'] . "\r\n";
    $info .= "Link: " . $m['watch_link'];
    file_put_contents($backup_dir . "info.txt", $info);

    // B. SAVE POSTER
    if (!empty($m['poster_url'])) {
        $p_src = $uploads_folder . basename($m['poster_url']);
        if (file_exists($p_src)) {
            copy($p_src, $backup_dir . basename($m['poster_url']));
        }
    }

    // C. SAVE VIDEO (If exists)
    // NOTE: Copying is better than Zipping for 30GB files on mobile
    if (!empty($m['video_file'])) {
        $v_src = $uploads_folder . basename($m['video_file']);
        if (file_exists($v_src)) {
            // Check disk space (Optional logic omitted for simplicity)
            if(!copy($v_src, $backup_dir . basename($m['video_file']))) {
                // If copy failed, write a note
                file_put_contents($backup_dir . "ERROR.txt", "Video too large to copy via script. Please verify manual copy.");
            }
        }
    }

    // CREATE A SIMPLE ZIP OF THE FOLDER (Faster method)
    // Mobile PHP ZipArchive crashes on >2GB. We keep it as FOLDER Backup for safety.
    // If you REALLY want zip, only zip the Text & Poster, keep video separate?
    // Current Solution: Creates a FOLDER in saved_movies. It's safer.

    // Return Success
    if (isset($_GET['execute'])) {
        echo "SUCCESS";
    } else {
        // Direct Button Click
        echo "<script>alert('✅ BACKUP SAVED IN FOLDER!'); window.location='manage_backups.php';</script>";
    }
    exit;
}
?>