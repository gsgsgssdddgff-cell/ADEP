<?php
session_start();
// 1. SECURITY CHECK
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

$generated_desc = "";
$selected_movie_id = "";

// ==========================================
// 2. GENERATE LOGIC (THE BRAIN)
// ==========================================
if (isset($_POST['generate'])) {
    $movie_id = $_POST['movie_id'];
    $selected_movie_id = $movie_id;

    // Fetch Movie & Category from Database
    $stmt = $pdo->prepare("SELECT m.title, c.name as genre FROM movies m JOIN categories c ON m.category_id = c.id WHERE m.id = ?");
    $stmt->execute([$movie_id]);
    $data = $stmt->fetch();
    
    if ($data) {
        $movie = $data['title'];
        $genre = $data['genre']; // Auto-Detected Genre

        // --- SMART TEMPLATES BASED ON GENRE ---
        
        // A. INTRO (Common)
        $intro = "🎬 **$movie** is finally here! Get ready for an unforgettable experience. 🔥 This is not just a movie, it's a masterpiece in the **$genre** world! ✨ From the very first scene, it grabs your attention and never lets go.";

        // B. BODY (Genre Specific Logic)
        $body = "";
        
        // ACTION
        if (stripos($genre, 'Action') !== false || stripos($genre, 'Thriller') !== false) {
            $body = "💥 The action sequences are mind-blowing! From high-speed chases to intense combat, **$movie** keeps you on the edge of your seat. 🚀 The stunts are performed with perfection, making every moment feel real. 🥊 If you love adrenaline, this is for you! The pacing is fast, furious, and absolutely relentless. 🏎️";
        } 
        // ROMANCE
        elseif (stripos($genre, 'Romance') !== false || stripos($genre, 'Drama') !== false) {
            $body = "❤️ A beautiful tale of love and passion. **$movie** explores the depth of emotions and relationships. The chemistry between the leads is magical ✨. It will make you laugh, cry, and fall in love all over again. 🌹 A perfect date-night watch! The emotional depth of the characters will stay with you long after the movie ends. 💑";
        } 
        // HORROR
        elseif (stripos($genre, 'Horror') !== false || stripos($genre, 'Mystery') !== false) {
            $body = "👻 Prepare to be terrified! **$movie** delivers spine-chilling moments that will haunt you. The atmosphere is dark and eerie 🌑. Every shadow hides a secret. Don't watch this alone at night! 🕯️ True horror fans will love the suspense and the jump scares. 💀";
        } 
        // COMEDY
        elseif (stripos($genre, 'Comedy') !== false) {
            $body = "😂 Get ready to laugh until your stomach hurts! **$movie** is packed with hilarious jokes and perfect comic timing. 🎭 The characters are quirky and lovable. A complete family entertainer that will lift your mood instantly! 🥳 It's the perfect movie to watch with friends and family.";
        } 
        // ANIME / ANIMATION
        elseif (stripos($genre, 'Anime') !== false || stripos($genre, 'Animation') !== false) {
            $body = "🎌 The animation quality in **$movie** is top-tier! The world-building is immersive and the battles are epic. ⚔️ Whether you are a long-time fan or new to anime, this story will captivate you. The voice acting brings the characters to life! 🎧 A visual treat that pushes the boundaries of art.";
        } 
        // SCI-FI
        elseif (stripos($genre, 'Sci-Fi') !== false) {
            $body = "👽 Step into the future with **$movie**. The visual effects are groundbreaking and the concept is mind-bending. 🌌 It explores themes that will make you think. A true sci-fi spectacle that rivals the best in the genre. 🤖";
        }
        // DEFAULT
        else {
            $body = "🌟 The story is gripping and full of surprises. The character development is deep, making you connect with them instantly. 🎭 The visuals are stunning, and the background score elevates every scene. 🎵 A must-watch for every cinema lover!";
        }

        // C. BRANDING & OUTRO
        $outro = "
The cinematography is stunning, capturing every detail with perfection. 🎥 The background score elevates the experience to a whole new level. 🎵 Critics and audiences alike are praising the performances. It’s not just a movie; it’s an experience.

✅ **Why You Must Watch:**
- Incredible Storyline 📖
- Top-tier Acting 🎭
- Mind-blowing Visuals ✨
- Best in Class Audio 🎧

🚀 **Exclusive Premiere:**
You are watching the official high-quality version of **$movie**, uploaded directly via **Adept Studio**. 🎥

📲 **Download & Stream Now:**
Only on **Adept Cinema App**! Don't miss out on this blockbuster. 🏆
        ";

        // COMBINE
        $generated_desc = $intro . "\n\n" . $body . "\n\n" . $outro;
    }
}

// ==========================================
// 3. SAVE LOGIC (DIRECT UPDATE)
// ==========================================
if (isset($_POST['save_now'])) {
    $id = $_POST['save_id'];
    $text = $_POST['final_text'];
    
    $pdo->prepare("UPDATE movies SET description = ? WHERE id = ?")->execute([$text, $id]);
    echo "<script>alert('✅ DESCRIPTION SAVED TO MOVIE!'); window.location='movies.php';</script>";
}

// FETCH ALL MOVIES FOR DROPDOWN
$movies = $pdo->query("SELECT id, title FROM movies ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-black text-white p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8 border-b border-gray-800 pb-4">
        <h1 class="text-2xl font-bold text-blue-500"><i class="fas fa-robot"></i> SMART AI WRITER</h1>
        <a href="dashboard.php" class="bg-gray-800 px-4 py-2 rounded text-sm">Back</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- LEFT: SELECT MOVIE -->
        <div class="bg-[#111] p-6 rounded-xl border border-blue-900">
            <h3 class="text-lg font-bold text-white mb-4">1. Select Movie</h3>
            <p class="text-xs text-gray-400 mb-4">AI will automatically detect the Genre (Action, Anime, etc.) from the database.</p>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="text-xs text-gray-400 font-bold">CHOOSE UPLOADED MOVIE</label>
                    <select name="movie_id" class="w-full bg-black border border-gray-700 p-3 rounded text-white focus:border-blue-500 outline-none">
                        <?php foreach($movies as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= $selected_movie_id == $m['id'] ? 'selected' : '' ?>>
                                🎬 <?= $m['title'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="generate" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 py-3 rounded-lg font-bold shadow-lg hover:scale-105 transition">
                    ✨ GENERATE DESCRIPTION
                </button>
            </form>
        </div>

        <!-- RIGHT: RESULT & SAVE -->
        <div class="bg-[#111] p-6 rounded-xl border border-green-900 relative">
            <h3 class="text-lg font-bold text-green-500 mb-4">2. Result</h3>
            
            <form method="POST">
                <input type="hidden" name="save_id" value="<?= $selected_movie_id ?>">
                
                <textarea name="final_text" class="w-full h-80 bg-black border border-gray-700 p-4 rounded text-gray-300 text-sm leading-relaxed mb-4" placeholder="Generated text will appear here..."><?= $generated_desc ?></textarea>
                
                <?php if($generated_desc): ?>
                <div class="flex gap-2">
                    <button type="submit" name="save_now" class="flex-1 bg-green-600 text-white py-3 rounded font-bold hover:bg-green-500 shadow-lg">
                        <i class="fas fa-save"></i> SAVE TO MOVIE
                    </button>
                    
                    <button type="button" onclick="copyText()" class="bg-gray-700 text-white px-4 py-3 rounded font-bold hover:bg-gray-600">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>

    </div>

    <script>
        function copyText() {
            var copyText = document.querySelector("textarea");
            copyText.select();
            document.execCommand("copy");
            alert("✅ Copied!");
        }
    </script>

</body>
</html>