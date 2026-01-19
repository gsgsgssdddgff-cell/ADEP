<?php
// Unlimited time to process big files
@ini_set('memory_limit', '-1'); 
@set_time_limit(0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dir = "../uploads/";
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    
    // Safety check
    $file = preg_replace('/[^A-Za-z0-9._-]/', '_', $_POST['filename']);
    
    file_put_contents($dir . $file, file_get_contents($_FILES['file']['tmp_name']), FILE_APPEND);
    echo "OK";
}
?>