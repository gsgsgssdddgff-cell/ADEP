<?php
require_once 'common/config.php';

// 1. CHECK PASSWORD
$pass = $_GET['key'] ?? '';
if ($pass !== "kya tum ya hai") { die("❌ ACCESS DENIED: Wrong Password"); }

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$m = $stmt->fetch();

if(!$m) die("Movie not found");

// 2. CREATE ZIP
$zip = new ZipArchive();
$zip_name = "backup_" . preg_replace('/[^A-Za-z0-9\-]/', '_', $m['title']) . ".zip";
$zip_path = "uploads/" . $zip_name;

if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
    die("Cannot create ZIP");
}

// 3. ADD FILES TO ZIP
// Add Video
if (!empty($m['video_file']) && file_exists($m['video_file'])) {
    $zip->addFile($m['video_file'], basename($m['video_file']));
}
// Add Poster
if (!empty($m['poster_url']) && file_exists($m['poster_url'])) {
    $zip->addFile($m['poster_url'], basename($m['poster_url']));
}
// Add Details Text File
$details = "TITLE: " . $m['title'] . "\n";
$details .= "DESC: " . $m['description'] . "\n";
$details .= "RATING: " . $m['rating'] . "\n";
$details .= "YEAR: " . $m['release_year'] . "\n";
$zip->addFromString("details.txt", $details);

$zip->close();

// 4. DOWNLOAD ZIP
if (file_exists($zip_path)) {
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="'.basename($zip_path).'"');
    header('Content-Length: ' . filesize($zip_path));
    readfile($zip_path);
    unlink($zip_path); // Delete zip after download to save space
    exit;
} else {
    echo "Error creating ZIP. Maybe file permissions issue.";
}
?>