<?php
// upload_chunk_user.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "uploads/"; // Root se uploads folder
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

    $file_name = $_POST['filename'];
    $target_file = $target_dir . $file_name;

    $chunk_data = file_get_contents($_FILES['file']['tmp_name']);
    file_put_contents($target_file, $chunk_data, FILE_APPEND);
    echo "OK";
}
?>