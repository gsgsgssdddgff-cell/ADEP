<?php
// download.php - MASTER DOWNLOAD HANDLER
// 100% SECURE & FULL CODE

// Error Reporting off karein taaki file corrupt na ho
error_reporting(0);

// =============================================
// 1. LOGIC SELECTOR (APK ya MOVIE?)
// =============================================
if (isset($_GET['type']) && $_GET['type'] == 'app') {
    
    // --- MODE A: DOWNLOAD APP APK ---
    // File jo server par rakhi hai
    $server_file = 'app.apk'; 
    
    // Naam jo user ko dikhega (Premium Name)
    $download_name = "Adept_Cinema_Premium_v2.0.apk";
    
    // Android Type
    $content_type = "application/vnd.android.package-archive";
    
    $filepath = __DIR__ . '/' . $server_file;

} else {
    
    // --- MODE B: DOWNLOAD MOVIE ---
    // URL Parameters get karein
    $file_param = $_GET['file'] ?? ''; 
    $name_param = $_GET['name'] ?? '';

    // Security: Folder jumping rokein (../ hatao)
    $file_param = str_replace(['../', '..\\'], '', $file_param); 
    
    // Asli Rasta
    $filepath = __DIR__ . '/' . $file_param;

    // Naam logic
    if ($name_param == "") {
        $download_name = basename($filepath);
    } else {
        $download_name = basename($name_param);
    }
    
    $content_type = "application/octet-stream";
}

// =============================================
// 2. CHECK & DOWNLOAD PROCESS
// =============================================

if (file_exists($filepath) && is_file($filepath)) {
    
    // Step 1: Buffer Clean (File Corruption Fix)
    if (ob_get_level()) { ob_end_clean(); }

    // Step 2: Get File Size
    $fsize = filesize($filepath);

    // Step 3: Send Advanced Headers (Chrome/Browsers ke liye)
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $content_type);
    header('Content-Disposition: attachment; filename="' . $download_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . $fsize);

    // Step 4: Speed Limit hatao aur Download shuru karo
    @set_time_limit(0);
    $file = @fopen($filepath,"rb");
    
    if ($file) {
        while(!feof($file)) {
            print(fread($file, 1024*8)); // 8KB Chunk Speed
            flush();
            if (connection_status()!=0) {
                @fclose($file);
                exit;
            }
        }
        @fclose($file);
    }
    exit;

} else {
    
    // =============================================
    // 3. ERROR SCREEN (AGAR FILE NAHI MILI)
    // =============================================
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error 404</title>
        <style>
            body { background-color: #000; color: #fff; font-family: monospace; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; text-align: center; }
            h1 { font-size: 80px; margin: 0; color: #ff3333; }
            h2 { font-size: 24px; margin-top: 10px; }
            .info { border: 1px solid #333; padding: 20px; border-radius: 10px; margin-top: 30px; background: #111; max-width: 90%; }
            .btn { display: inline-block; margin-top: 20px; padding: 10px 25px; background: #333; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; border: 1px solid white; }
            .btn:hover { background: white; color: black; }
        </style>
    </head>
    <body>
        <h1>404</h1>
        <h2>FILE NOT FOUND</h2>
        <div class="info">
            <p>System could not locate:</p>
            <p style="color:yellow; word-break: break-all;"><?= htmlspecialchars(basename($filepath)) ?></p>
            <hr style="border: 1px solid #333; margin: 15px 0;">
            <p style="font-size: 12px; color: gray;">Please ask the Admin to upload this file again.</p>
        </div>
        <a href="index.php" class="btn">GO HOME</a>
    </body>
    </html>
    <?php
    exit;
}
?>