<?php
// admin/find_folder.php
echo "<h1>📂 FOLDER PATH FINDER</h1>";

// 1. ASLI RASTA NIKALO
$real_path = realpath('../uploads');

echo "<h3>📍 MOVIE YAHAN PASTE KARO:</h3>";
echo "<div style='background:black; color:yellow; padding:20px; font-size:18px; border:2px solid red;'>";
if ($real_path) {
    echo $real_path;
} else {
    // Agar folder nahi mila to bana do
    mkdir('../uploads');
    echo realpath('../uploads');
}
echo "</div>";

// 2. CHECK KARO KI KYA RAKHA HAI WAHAN
echo "<h3>📂 FILES CURRENTLY IN FOLDER:</h3>";
$files = scandir('../uploads');
echo "<ul>";
foreach($files as $f) {
    if($f != '.' && $f != '..') {
        echo "<li>🎥 $f (" . round(filesize("../uploads/$f")/1024/1024, 2) . " MB)</li>";
    }
}
echo "</ul>";

echo "<br><h3>❌ AGAR LIST KHALI HAI:</h3>";
echo "<p>Iska matlab aapne file galat jagah daali hai. Upar diye gaye Yellow Box wale raste par file move karein.</p>";
?>