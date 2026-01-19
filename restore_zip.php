<?php
session_start();
require_once '../common/config.php';
if(isset($_POST['restore'])){
    $zip=new ZipArchive; if($zip->open($_FILES['f']['tmp_name'])===TRUE){
        $zip->extractTo("../uploads/"); $txt=$zip->getFromName("info.txt");
        preg_match('/TITLE: (.*)/',$txt,$t); preg_match('/DESC: (.*)/s',$txt,$d);
        $sql="INSERT INTO movies (title,description,video_file,poster_url,status,category_id) VALUES (?,?,?,?,'approved',1)";
        // Simple search for mp4/jpg
        $v=""; $p=""; for($i=0;$i<$zip->numFiles;$i++){ $n=$zip->getNameIndex($i); if(strpos($n,'.mp4'))$v="uploads/".$n; if(strpos($n,'.jpg'))$p="uploads/".$n; }
        $pdo->prepare($sql)->execute([$t[1],$d[1],$v,$p]);
        echo "<script>alert('✅ RESTORED!'); window.location='movies.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><script src="https://cdn.tailwindcss.com"></script><meta name="viewport" content="width=device-width"></head>
<body class="bg-black text-white p-6">
    <h1 class="text-xl font-bold text-yellow-500 mb-6">RESTORE ZIP</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="f" class="block w-full text-sm text-gray-400 bg-[#111] p-3 rounded border border-gray-700 mb-4" required>
        <button name="restore" class="w-full bg-yellow-600 py-3 rounded font-bold">START RESTORE</button>
    </form>
    <a href="save_menu.php" class="block text-center mt-6 text-gray-500 text-sm">Cancel</a>
</body>
</html>