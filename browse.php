<?php
require_once 'common/config.php';
$cat_id = $_GET['cat'] ?? 'all';
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();

if($cat_id == 'all') {
    $movies = $pdo->query("SELECT * FROM movies ORDER BY id DESC")->fetchAll();
    $cat_name = "All Movies";
} else {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE category_id = ? ORDER BY id DESC");
    $stmt->execute([$cat_id]);
    $movies = $stmt->fetchAll();
    $c_stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $c_stmt->execute([$cat_id]);
    $cat_name = $c_stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Browse</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#e50914">
    <meta name="mobile-web-app-capable" content="yes">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{background:#050505; color:white;}</style>
    
    <!-- 🚀 JAIL SCRIPT -->
    <script>
        document.addEventListener('click', function(e) {
            var target = e.target;
            while (target && target.tagName !== 'A') { target = target.parentNode; }
            if (target && target.href) {
                e.preventDefault();
                window.location.href = target.href;
            }
        });
    </script>
</head>
<body class="pb-20">

    <div class="sticky top-0 bg-black/90 backdrop-blur z-50 p-4 flex items-center justify-between border-b border-gray-800">
        <a href="index.php" class="text-gray-400 text-xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-lg font-bold text-red-600 uppercase"><?= $cat_name ?></h1>
        <a href="search.php"><i class="fas fa-search"></i></a>
    </div>

    <!-- CATEGORY LIST -->
    <div class="p-4 flex gap-3 overflow-x-auto no-scrollbar">
        <a href="browse.php?cat=all" class="px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap <?= $cat_id == 'all' ? 'bg-red-600' : 'bg-gray-800' ?>">ALL</a>
        <?php foreach($cats as $c): ?>
            <a href="browse.php?cat=<?= $c['id'] ?>" class="px-5 py-2 rounded-full text-sm font-bold whitespace-nowrap <?= $cat_id == $c['id'] ? 'bg-red-600' : 'bg-gray-800' ?>">
                <?= strtoupper($c['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- GRID -->
    <div class="p-4 grid grid-cols-3 gap-3">
        <?php foreach($movies as $m): ?>
        <a href="movie_details.php?id=<?= $m['id'] ?>" class="block group">
            <img src="<?= $m['poster_url'] ?>" class="w-full h-40 object-cover rounded mb-2">
            <h3 class="text-xs font-bold truncate text-gray-300"><?= $m['title'] ?></h3>
        </a>
        <?php endforeach; ?>
    </div>

</body>
</html>