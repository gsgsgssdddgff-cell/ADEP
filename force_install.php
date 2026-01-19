<?php
// 1. FORCE SHOW ERRORS (Taaki Blank Screen na aaye)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🛠 AWEBSERVER EMERGENCY SETUP</h1>";

// 2. AWEBSERVER SETTINGS (Hardcoded)
$host = '127.0.0.1';
$user = 'root';
$pass = 'root'; // AWebServer ka password hamesha 'root' hota hai

try {
    // 3. CONNECT TO MYSQL
    echo "Connecting to MySQL... ";
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span style='color:green'><b>SUCCESS!</b></span><br>";

    // 4. CREATE DATABASE
    echo "Creating Database... ";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS adept_cinema_db");
    $pdo->exec("USE adept_cinema_db");
    echo "<span style='color:green'><b>DONE.</b></span><br>";

    // 5. CREATE TABLES
    echo "Creating Tables... <br>";

    // Admin Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )");

    // Movies Table (With ALL Columns)
    $pdo->exec("CREATE TABLE IF NOT EXISTS movies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        rating DECIMAL(3,1),
        release_year INT,
        watch_link VARCHAR(255),
        poster_url VARCHAR(255),
        video_file VARCHAR(255),
        screenshots TEXT
    )");

    // Categories Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL
    )");

    // Banned IPs
    $pdo->exec("CREATE TABLE IF NOT EXISTS banned_ips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL UNIQUE,
        reason VARCHAR(255),
        banned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE,
        password VARCHAR(255)
    )");

    echo "✅ All Tables Created.<br>";

    // 6. ADD ADMIN
    $check = $pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
    if ($check == 0) {
        $u = "admin";
        $p = "Vega@2026#Pro"; // Aapka Naya Password (Hardcoded)
        $sql = "INSERT INTO admin (username, password) VALUES ('$u', '$p')";
        $pdo->exec($sql);
        echo "✅ Admin Account Created (User: admin / Pass: Vega@2026#Pro)<br>";
    }

    // 7. ADD CATEGORIES
    $checkCat = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($checkCat == 0) {
        $pdo->exec("INSERT INTO categories (name) VALUES ('Action'), ('Anime'), ('Horror'), ('Sci-Fi')");
        echo "✅ Categories Added.<br>";
    }

    echo "<h1 style='color:green'>🎉 SETUP COMPLETE SUCCESSFULLY!</h1>";
    echo "<a href='index.php' style='font-size:20px; font-weight:bold;'>👉 CLICK HERE TO OPEN APP</a>";

} catch (PDOException $e) {
    echo "<h1 style='color:red'>ERROR AA GAYA!</h1>";
    echo "<h3>Detail: " . $e->getMessage() . "</h3>";
    echo "<p>Agar error 'Access denied' hai, to shayad password galat hai.</p>";
}
?>