<?php
// FILE NAME ON SERVER
$file = 'app.apk'; 

// FILE NAME FOR USER (PREMIUM NAME)
$download_name = "Adept_Cinema_Official_v2.0.apk";

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="' . $download_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    echo "<h1 style='text-align:center;font-family:sans-serif;'>⚠️ App File Not Found!<br>Please ask Admin to upload 'app.apk'.</h1>";
}
?>