<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

// STATISTICS
$total_movies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_pay = 0;
$pending_movies = 0;
try { $pending_pay = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status = 'PENDING'")->fetchColumn(); } catch (Exception $e) {}
try { $pending_movies = $pdo->query("SELECT COUNT(*) FROM movies WHERE status = 'pending'")->fetchColumn(); } catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#050505] text-white p-6 font-sans">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8 border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-3xl font-black text-red-600 tracking-tighter">ADEPT <span class="text-white">ADMIN</span></h1>
            <p class="text-xs text-green-500 font-mono mt-1"><i class="fas fa-circle text-[8px] animate-pulse"></i> SYSTEM ONLINE</p>
        </div>
        <a href="../index.php" class="bg-gray-800 px-4 py-2 rounded text-sm hover:bg-gray-700">Open App</a>
    </div>

    <!-- NOTIFICATION ALERTS -->
    <div class="space-y-2 mb-6">
        <?php if($pending_pay > 0): ?>
            <a href="approve_payment.php" class="flex items-center gap-3 bg-yellow-600/90 text-black font-bold p-4 rounded-xl shadow-lg shadow-yellow-900/40 animate-pulse">
                <i class="fas fa-bell text-2xl"></i>
                <span>Action: <?= $pending_pay ?> New Payments need verification!</span>
            </a>
        <?php endif; ?>
        <?php if($pending_movies > 0): ?>
            <a href="creator_approval.php" class="flex items-center gap-3 bg-blue-600/90 text-white font-bold p-4 rounded-xl shadow-lg animate-pulse">
                <i class="fas fa-film text-2xl"></i>
                <span>Action: <?= $pending_movies ?> Creator Uploads Pending!</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- MAIN GRID -->
    <div class="grid gap-4">

        <!-- 🚀 AI TOOLS -->
        <a href="ai_generator.php" class="block w-full bg-gradient-to-r from-blue-700 to-purple-800 p-6 rounded-2xl flex items-center justify-between border border-blue-500/50 shadow-2xl hover:scale-[1.01] transition">
            <div class="text-left">
                <h3 class="font-bold text-xl text-white">AI Description Generator</h3>
                <p class="text-xs text-blue-200 opacity-80 mt-1">Auto-Write Professional Plots</p>
            </div>
            <i class="fas fa-magic text-3xl text-white"></i>
        </a>
        
        <!-- 💾 BACKUP SYSTEM -->
        <a href="save_menu.php" class="block w-full bg-[#111] p-6 rounded-2xl flex items-center justify-between border border-green-600/50 hover:border-green-500 transition group">
            <div class="text-left">
                <h3 class="font-bold text-xl text-green-500 group-hover:text-green-400">Backup Center</h3>
                <p class="text-xs text-gray-500 group-hover:text-gray-300 mt-1">Scan, Save & Restore Movies (ZIP)</p>
            </div>
            <i class="fas fa-database text-3xl text-green-600"></i>
        </a>

        <!-- 🔒 UPLOAD (SINGLE) -->
        <button onclick="secureUpload()" class="w-full bg-gradient-to-r from-red-600 to-red-800 p-5 rounded-xl border border-red-500 flex items-center justify-between shadow-lg hover:scale-[1.01] transition">
            <div class="text-left">
                <h3 class="font-bold text-lg text-white">UPLOAD MOVIE (SINGLE)</h3>
                <p class="text-xs text-red-200 opacity-75">Gallery / ZArchiver / Link</p>
            </div>
            <i class="fas fa-cloud-upload-alt text-2xl text-white"></i>
        </button>

        <!-- 🎥 UPLOAD SERIES (BULK) -->
        <a href="upload_series.php" class="block w-full bg-[#1a1120] border border-purple-600 p-5 rounded-2xl flex items-center justify-between shadow-xl hover:border-purple-400 transition group">
            <div class="text-left">
                <h3 class="font-bold text-xl text-purple-500 group-hover:text-purple-400">UPLOAD SERIES / BULK</h3>
                <p class="text-xs text-gray-400 mt-1">Multi-Episode • 256GB Support</p>
            </div>
            <i class="fas fa-layer-group text-3xl text-purple-600 group-hover:scale-110 transition"></i>
        </a>

        <!-- 👇 START: NEW CONTROL BUTTONS (ADDED HERE) 👇 -->
        <div class="grid grid-cols-2 gap-4">
            <!-- USER ROLES MANAGER -->
            <a href="manage_users.php" class="block bg-gray-900 border border-purple-500/50 p-5 rounded-xl text-center hover:bg-gray-800 transition group">
                <i class="fas fa-users-cog text-3xl text-purple-500 mb-2 group-hover:scale-110 transition"></i>
                <h3 class="font-bold text-gray-200">Manage Roles</h3>
                <p class="text-[10px] text-gray-500">Admin/Owner Access</p>
            </a>

            <!-- BROADCAST MESSAGE -->
            <a href="send_broadcast.php" class="block bg-gray-900 border border-blue-500/50 p-5 rounded-xl text-center hover:bg-gray-800 transition group">
                <i class="fas fa-bullhorn text-3xl text-blue-500 mb-2 group-hover:scale-110 transition"></i>
                <h3 class="font-bold text-gray-200">Broadcast</h3>
                <p class="text-[10px] text-gray-500">Message All Users</p>
            </a>
        </div>
        <!-- 👆 END: NEW CONTROL BUTTONS 👆 -->

        <!-- CORE MODULES GRID -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Library -->
            <a href="movies.php" class="bg-[#111] p-5 rounded-xl border border-gray-800 hover:border-blue-600 transition text-center group">
                <i class="fas fa-film text-2xl text-blue-500 mb-2 group-hover:scale-110 transition"></i>
                <h3 class="font-bold text-gray-200">Library (<?= $total_movies ?>)</h3>
            </a>
            
            <!-- Users -->
            <a href="users.php" class="bg-[#111] p-5 rounded-xl border border-gray-800 hover:border-red-600 transition text-center group">
                <i class="fas fa-users text-2xl text-red-500 mb-2 group-hover:scale-110 transition"></i>
                <h3 class="font-bold text-gray-200">Users (<?= $total_users ?>)</h3>
            </a>

            <!-- Payments -->
            <a href="approve_payment.php" class="bg-[#111] p-5 rounded-xl border border-gray-800 hover:border-yellow-500 transition text-center group">
                <i class="fas fa-receipt text-2xl text-yellow-500 mb-2 group-hover:scale-110 transition"></i>
                <h3 class="font-bold text-gray-200">Payments</h3>
            </a>

            <!-- PC Sync Tool -->
            <a href="pc_sync.php" class="bg-[#111] p-5 rounded-xl border border-gray-800 hover:border-cyan-500 transition text-center group">
                <i class="fas fa-desktop text-2xl text-cyan-500 mb-2 group-hover:scale-110 transition"></i>
                <h3 class="font-bold text-gray-200">PC Sync</h3>
            </a>
        </div>

        <!-- EXTRA SETTINGS -->
        <a href="creator_approval.php" class="block w-full bg-[#111] p-4 rounded-xl flex items-center justify-between border border-gray-800 hover:border-green-500 transition">
             <span class="text-sm font-bold text-green-400">Creator Approvals</span>
             <i class="fas fa-check-circle text-green-500"></i>
        </a>

        <a href="settings.php" class="block w-full bg-[#111] p-4 rounded-xl text-center text-xs text-gray-500 hover:text-white border border-transparent hover:border-gray-800">
            <i class="fas fa-cog"></i> Payment Settings
        </a>

    </div>

    <script>
        function secureUpload() {
            let pass = prompt("🔐 Enter Master Password:");
            if(pass === "Vortex!Mango_92^Neon$River#Atlas") { window.location.href = "upload.php"; } 
            else { alert("❌ ACCESS DENIED!"); }
        }
    </script>
</body>
</html>