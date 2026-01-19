<?php
session_start();
require_once 'config.php';
$user = $_SESSION['user_id'] ?? null;

// MAINTENANCE CHECK
$m_mode = $pdo->query("SELECT maintenance_mode FROM settings WHERE id=1")->fetchColumn();
if ($m_mode == 1 && !isset($_SESSION['admin_logged_in'])) {
    die("<h1>🚧 MAINTENANCE MODE</h1><p>We are upgrading servers. Back soon!</p>");
}

if(!$user) { die("Please Login"); }

// 1. ADD TO WATCHLIST
if(isset($_POST['add_watchlist'])) {
    $mid = $_POST['movie_id'];
    $check = $pdo->query("SELECT id FROM watchlist WHERE username='$user' AND movie_id=$mid")->rowCount();
    if($check == 0) {
        $pdo->prepare("INSERT INTO watchlist (username, movie_id) VALUES (?, ?)")->execute([$user, $mid]);
        echo "Added";
    } else {
        $pdo->prepare("DELETE FROM watchlist WHERE username=? AND movie_id=?")->execute([$user, $mid]);
        echo "Removed";
    }
}

// 2. SUBMIT REVIEW
if(isset($_POST['submit_review'])) {
    $pdo->prepare("INSERT INTO reviews (username, movie_id, rating, comment) VALUES (?, ?, ?, ?)")
        ->execute([$user, $_POST['movie_id'], $_POST['rating'], $_POST['comment']]);
    header("Location: ../movie_details.php?id=".$_POST['movie_id']);
}

// 3. SUBMIT REPORT
if(isset($_POST['report_broken'])) {
    $pdo->prepare("INSERT INTO reports (movie_id, issue) VALUES (?, 'Broken Link/Video Error')")->execute([$_POST['movie_id']]);
    echo "<script>alert('Report Sent! Admin will fix it.'); window.history.back();</script>";
}

// 4. SUBMIT REQUEST
if(isset($_POST['request_movie'])) {
    $pdo->prepare("INSERT INTO requests (username, message) VALUES (?, ?)")->execute([$user, $_POST['req_text']]);
    echo "<script>alert('Request Sent! We will upload soon.'); window.location='../index.php';</script>";
}
?>