<?php
session_start();
// 1. SECURITY & CONFIG
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// ==========================================
// 🔐 PASSWORDS
// ==========================================
$DELETE_KEY = "VortexMaster2026";
$SAVE_KEY = "kya tum ya hai";

// ==========================================
// 🗑️ ADVANCED DELETE LOGIC (DB ONLY - FILES SAFE)
// ==========================================
if(isset($_GET['del_id']) && isset($_GET['key'])) {
    
    $input_key = $_GET['key'];

    // VERIFY PASSWORD
    if($input_key === $DELETE_KEY) {
        $id = $_GET['del_id'];
        
        try {
            // Step 1: Movie/Series ka naam nikalo (for series deletion)
            $stmt = $pdo->prepare("SELECT title FROM movies WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            
            if ($item) {
                // Name Cleaning ("Naruto - Ep 1" -> "Naruto")
                $base_name = explode(' - Ep', $item['title'])[0]; 
                $base_name = explode(' - Episode', $base_name)[0];

                // NOTE: We are NOT deleting unlink() files here.
                // Files will remain in 'uploads' folder safe.
                
                // Step 2: DATABASE SE SAFAYA
                $pdo->prepare("DELETE FROM movies WHERE title LIKE ?")->execute(["$base_name%"]);
                
                echo "<script>alert('✅ SUCCESS! Series Removed from App Library.\\n(Video files are SAFE in storage).'); window.location='movies.php';</script>";
                exit;
            } else {
                echo "<script>alert('❌ ID Not Found!'); window.location='movies.php';</script>";
            }

        } catch(Exception $e) { die($e->getMessage()); }

    } else {
        // Password Error UI
        die("<body style='background:#000;color:red;display:flex;justify-content:center;align-items:center;height:100vh;'><h1>⛔ ACCESS DENIED: WRONG PASSWORD</h1></body>");
    }
}

// ==========================================
// 🔍 SEARCH & GROUPING SYSTEM (REST IS SAME)
// ==========================================
$search_query = $_GET['q'] ?? '';
$cat_filter = $_GET['cat'] ?? 'all';

$sql = "SELECT * FROM movies WHERE 1=1";
$params = [];

if ($search_query) { $sql .= " AND title LIKE ?"; $params[] = "%$search_query%"; }
if ($cat_filter != 'all') { $sql .= " AND category_id = ?"; $params[] = $cat_filter; }

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$raw_data = $stmt->fetchAll();

// --- 🔥 GROUPING ENGINE ---
$library = [];
foreach ($raw_data as $row) {
    $base = explode(' - Ep', $row['title'])[0];
    if (!isset($library[$base])) {
        $library[$base] = $row;
        $library[$base]['type'] = 'Movie';
        $library[$base]['ep_count'] = 1;
    } else {
        $library[$base]['type'] = 'Series';
        $library[$base]['ep_count']++;
    }
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #02040a; color: white; font-family: 'Inter', sans-serif; }
        .card { transition: 0.3s; background: linear-gradient(145deg, #111, #0f0f0f); }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(229, 9, 20, 0.2); border: 1px solid #333; }
        .badge-premium { background: linear-gradient(90deg, #ffc107, #b45309); color: black; }
        .badge-free { background: linear-gradient(90deg, #22c55e, #14532d); color: white; }
    </style>
</head>
<body class="p-4 pb-32">

    <!-- 🔥 DASHBOARD HEADER -->
    <div class="sticky top-0 z-50 bg-[#02040a]/95 backdrop-blur-md pb-4 pt-2 border-b border-gray-800">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            
            <div class="flex items-center gap-4">
                <div class="bg-gray-800 p-3 rounded-lg"><i class="fas fa-video text-2xl text-red-600"></i></div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-wider">LIBRARY X</h1>
                    <p class="text-xs text-gray-500 font-bold"><?= count($library) ?> TITLES | TOTAL DB ITEMS: <?= count($raw_data) ?></p>
                </div>
            </div>

            <div class="flex gap-2 mt-4 md:mt-0">
                <a href="manage_backups.php" class="bg-[#1a1a1a] border border-gray-700 px-4 py-2 rounded text-xs font-bold hover:bg-gray-800 transition">📦 BACKUPS</a>
                <a href="dashboard.php" class="bg-red-600 px-6 py-2 rounded text-xs font-bold shadow-lg shadow-red-900/40 hover:bg-red-500 transition">DASHBOARD</a>
            </div>
        </div>

        <!-- SEARCH ENGINE -->
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-3.5 text-gray-500"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" class="w-full bg-[#111] border border-gray-700 p-3 pl-12 rounded-xl text-white placeholder-gray-500 focus:border-red-600 outline-none transition" placeholder="Search Database...">
            </div>
            
            <div class="relative">
                <select name="cat" class="bg-[#111] border border-gray-700 p-3 rounded-xl text-white outline-none w-full md:w-48 appearance-none font-bold text-xs">
                    <option value="all">ALL CATEGORIES</option>
                    <?php foreach($cats as $c): ?><option value="<?=$c['id']?>" <?= $cat_filter==$c['id']?'selected':'' ?>><?=strtoupper($c['name'])?></option><?php endforeach; ?>
                </select>
                <i class="fas fa-filter absolute right-4 top-3.5 text-gray-500"></i>
            </div>
            <button class="bg-blue-600 px-6 rounded-xl font-bold hover:bg-blue-500 shadow-lg">FIND</button>
        </form>
    </div>

    <!-- MOVIE GRID -->
    <div class="grid grid-cols-1 gap-4 mt-6">
        
        <?php if(empty($library)): ?>
            <div class="text-center py-20 border-2 border-dashed border-gray-800 rounded-2xl">
                <i class="fas fa-ghost text-6xl text-gray-800 mb-4"></i>
                <h2 class="text-gray-500 text-lg font-bold">LIBRARY EMPTY</h2>
            </div>
        <?php else: ?>
            
            <?php foreach($library as $title => $m): 
                $is_series = ($m['ep_count'] > 1);
                $is_paid = ($m['price'] > 0);
                $is_link = empty($m['video_file']) || strpos($m['video_file'], 'http') !== false;
                $poster = (!empty($m['poster_url'])) ? "../".$m['poster_url'] : "https://via.placeholder.com/150";
            ?>
            
            <div class="card p-4 rounded-xl border border-gray-800 flex flex-col md:flex-row gap-4 relative overflow-hidden group">
                
                <div class="absolute inset-0 bg-gradient-to-r from-red-900/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500 pointer-events-none"></div>

                <!-- THUMBNAIL -->
                <div class="relative shrink-0 w-24 md:w-32 h-36 md:h-48 rounded-lg overflow-hidden bg-black border border-gray-700 shadow-xl mx-auto md:mx-0">
                    <img src="<?= $poster ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute bottom-0 w-full bg-black/80 text-[9px] text-center text-gray-300 py-1 font-mono">ID: <?= $m['id'] ?></div>
                </div>

                <!-- INFO BODY -->
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-xl font-black text-white leading-none mb-2"><?= htmlspecialchars($title) ?></h2>
                    
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start mb-3">
                        <?php if($is_series): ?>
                            <span class="bg-purple-900/50 text-purple-400 text-[10px] font-bold px-2 py-1 rounded border border-purple-800"><i class="fas fa-layer-group"></i> SERIES (<?= $m['ep_count'] ?> EPS)</span>
                        <?php else: ?>
                            <span class="bg-blue-900/50 text-blue-400 text-[10px] font-bold px-2 py-1 rounded border border-blue-800"><i class="fas fa-film"></i> MOVIE</span>
                        <?php endif; ?>

                        <?php if($is_paid): ?>
                            <span class="badge-premium text-[10px] font-black px-2 py-1 rounded flex items-center gap-1 shadow"><i class="fas fa-gem"></i> PAID ₹<?= $m['price'] ?></span>
                        <?php else: ?>
                            <span class="badge-free text-[10px] font-black px-2 py-1 rounded shadow">FREE</span>
                        <?php endif; ?>

                         <span class="bg-gray-800 text-gray-400 text-[10px] font-bold px-2 py-1 rounded border border-gray-600"><?= $m['quality'] ?? 'HD' ?></span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-400 bg-[#0a0a0a] p-3 rounded-lg border border-gray-800 inline-block text-left w-full">
                        <p><i class="fas fa-calendar text-blue-500"></i> Year: <b><?= $m['release_year'] ?></b></p>
                        <p><i class="fas fa-star text-yellow-500"></i> Rating: <b><?= $m['rating'] ?></b></p>
                        <p><i class="fas fa-hdd text-green-500"></i> File: <b><?= $is_link ? 'External Link' : 'Server Storage' ?></b></p>
                        <p><i class="fas fa-user text-purple-500"></i> By: <b><?= $m['creator_name'] ?></b></p>
                    </div>

                    <p class="text-xs text-gray-600 mt-2 italic line-clamp-1">"<?= $m['description'] ?>"</p>
                </div>

                <!-- ACTIONS PANEL -->
                <div class="flex flex-row md:flex-col gap-2 shrink-0 justify-center min-w-[120px]">
                    
                    <a href="edit_movie.php?id=<?= $m['id'] ?>" class="flex-1 bg-blue-600/20 text-blue-400 border border-blue-600 hover:bg-blue-600 hover:text-white px-3 py-2 rounded text-xs font-bold transition text-center flex items-center justify-center gap-2">
                        <i class="fas fa-pen"></i> EDIT
                    </a>

                    <button onclick="secureAction('save', <?= $m['id'] ?>)" class="flex-1 bg-green-600/20 text-green-400 border border-green-600 hover:bg-green-600 hover:text-white px-3 py-2 rounded text-xs font-bold transition flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> BACKUP
                    </button>
                    
                    <button onclick="secureAction('delete', <?= $m['id'] ?>)" class="flex-1 bg-red-600 text-white border border-red-500 px-3 py-2 rounded text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg shadow-red-900/30 hover:scale-105">
                        <i class="fas fa-trash-alt"></i> DELETE
                    </button>

                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    </div>

    <!-- JS SECURE HANDLER -->
    <script>
        function secureAction(action, id) {
            
            // DELETE ACTION
            if(action === 'delete') {
                let pass = prompt("⚠️ REMOVE FROM APP ONLY\n(Files will remain in storage)\n\nEnter Password:");
                if (pass === "<?= $DELETE_KEY ?>") {
                    if (confirm("Are you sure? This will hide the movie/series from the App.")) {
                        window.location.href = `movies.php?del_id=${id}&key=` + encodeURIComponent(pass);
                    }
                } else if(pass) alert("❌ ACCESS DENIED!");
            }
            
            // SAVE ACTION
            if (action === 'save') {
                let pass = prompt("💾 SYSTEM BACKUP:\nEnter Security Key:");
                if (pass === "<?= $SAVE_KEY ?>") {
                    window.location.href = `backup_action.php?id=${id}&key=` + encodeURIComponent(pass);
                } else if(pass) alert("❌ WRONG KEY!");
            }
        }
    </script>

</body>
</html>