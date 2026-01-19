<?php
require_once 'common/config.php';
$q = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Search</title>
    
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#e50914">
    <meta name="mobile-web-app-capable" content="yes">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{background:#050505; color:white;}</style>

    <!-- 🚀 JAIL SCRIPT (SEARCH KE LIYE BHI ZAROORI HAI) -->
    <script>
        document.addEventListener('click', function(e) {
            var target = e.target;
            while (target && target.tagName !== 'A') { target = target.parentNode; }
            if (target && target.href) {
                if (target.getAttribute('target') === '_blank') return;
                e.preventDefault();
                window.location.href = target.href;
            }
        });
    </script>
</head>
<body class="pb-20">

    <!-- SEARCH BAR HEADER -->
    <div class="sticky top-0 bg-black/90 backdrop-blur z-50 p-4 border-b border-gray-800">
        <form method="GET" class="flex gap-2">
            <a href="index.php" class="p-3 text-gray-400"><i class="fas fa-arrow-left text-xl"></i></a>
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search movies..." class="w-full bg-[#111] border border-gray-700 text-white p-3 rounded-full focus:border-red-600 outline-none">
            <button type="submit" class="bg-red-600 text-white px-5 rounded-full font-bold">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- RESULTS GRID -->
    <div class="p-4 grid grid-cols-3 gap-3">
        <?php
        if($q) {
            $stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? ORDER BY id DESC");
            $stmt->execute(["%$q%"]);
            $movies = $stmt->fetchAll();

            if(!$movies) echo "<p class='col-span-3 text-center text-gray-500 mt-10'>No movies found for \"$q\"</p>";

            foreach($movies as $m):
        ?>
            <a href="movie_details.php?id=<?= $m['id'] ?>" class="block group">
                <img src="<?= $m['poster_url'] ?>" class="w-full h-40 object-cover rounded-md mb-2 shadow-lg">
                <h3 class="text-xs font-bold truncate text-gray-300"><?= $m['title'] ?></h3>
            </a>
        <?php 
            endforeach;
        } else {
            echo "<p class='col-span-3 text-center text-gray-600 mt-20 text-sm'>Type movie name to search...</p>";
        }
        ?>
    </div>

    <!-- BOTTOM NAV -->
    <div class="fixed bottom-0 w-full bg-[#0a0a0a] border-t border-gray-800 flex justify-around py-3 z-50">
        <a href="index.php" class="text-gray-500 flex flex-col items-center"><i class="fas fa-home text-xl"></i></a>
        <a href="browse.php" class="text-gray-500 flex flex-col items-center"><i class="fas fa-layer-group text-xl"></i></a>
        <a href="search.php" class="text-red-600 flex flex-col items-center"><i class="fas fa-search text-xl"></i></a>
    </div>

</body>
</html>