<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit; }
require_once '../common/config.php';

if (isset($_POST['send_msg'])) {
    $title = $_POST['title'];
    $msg = $_POST['message'];
    $sender = "Official Admin";

    $pdo->prepare("INSERT INTO admin_messages (title, message, sender_name) VALUES (?, ?, ?)")->execute([$title, $msg, $sender]);
    echo "<script>alert('✅ Message Sent to ALL Users!'); window.location='dashboard.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broadcast Message</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-6">
    
    <div class="max-w-xl mx-auto bg-[#111] p-6 rounded-xl border border-blue-600 shadow-xl">
        <h1 class="text-2xl font-bold text-blue-500 mb-6">BROADCAST CENTER</h1>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="text-xs text-gray-500 font-bold uppercase">Title / Subject</label>
                <input type="text" name="title" class="w-full bg-black border border-gray-700 p-3 rounded text-white" placeholder="e.g. Server Maintenance / New Offer">
            </div>

            <div>
                <label class="text-xs text-gray-500 font-bold uppercase">Message to Users</label>
                <textarea name="message" class="w-full bg-black border border-gray-700 p-3 rounded text-white h-32" placeholder="Write your message here... Users can only READ this."></textarea>
            </div>

            <button type="submit" name="send_msg" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 py-3 rounded-xl font-bold">
                SEND TO EVERYONE 🚀
            </button>
        </form>

        <a href="dashboard.php" class="block text-center mt-4 text-gray-500 text-sm">Cancel</a>
    </div>

</body>
</html>