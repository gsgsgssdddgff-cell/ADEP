<?php
// install.php - AUTO BUILDER

$host = '127.0.0.1';
$user = 'root';
$pass = 'root'; // Try 'root' first

try {
    // 1. Connect without DB
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS adept_cinema_db");
    $pdo->exec("USE adept_cinema_db");

    // 3. Create Tables
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE,
        password VARCHAR(255),
        is_premium TINYINT DEFAULT 0,
        sub_plan VARCHAR(50) DEFAULT 'Free',
        ai_credits INT DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS movies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT,
        title VARCHAR(255),
        description TEXT,
        rating VARCHAR(10),
        release_year INT,
        watch_link VARCHAR(255),
        video_file VARCHAR(255),
        poster_url VARCHAR(255),
        screenshots TEXT,
        price INT DEFAULT 0,
        is_premium TINYINT DEFAULT 0,
        creator_name VARCHAR(100) DEFAULT 'Admin',
        creator_phone VARCHAR(50),
        creator_upi VARCHAR(100),
        creator_bank TEXT,
        status VARCHAR(20) DEFAULT 'approved'
    );

    CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50),
        utr VARCHAR(50),
        amount VARCHAR(20),
        plan VARCHAR(50),
        status VARCHAR(20) DEFAULT 'PENDING',
        movie_id INT DEFAULT 0,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS payment_settings (
        id INT PRIMARY KEY,
        upi_id VARCHAR(100),
        bank_name VARCHAR(100),
        acc_no VARCHAR(100),
        ifsc VARCHAR(100),
        contact_no VARCHAR(20)
    );
    ";
    $pdo->exec($sql);

    // 4. Insert Default Data
    // Admin
    $check = $pdo->query("SELECT COUNT(*) FROM users WHERE username='admin'")->fetchColumn();
    if ($check == 0) {
        // Admin Password: admin123 (Change later)
        $hash = password_hash("admin123", PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, password, is_premium) VALUES ('admin', '$hash', 1)");
    }

    // Categories
    $c_check = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($c_check == 0) {
        $genres = ["Action", "Adventure", "Anime", "Comedy", "Drama", "Horror", "Sci-Fi", "Thriller"];
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        foreach ($genres as $g) { $stmt->execute([$g]); }
    }

    // Payment Settings
    $p_check = $pdo->query("SELECT COUNT(*) FROM payment_settings")->fetchColumn();
    if ($p_check == 0) {
        $pdo->exec("INSERT INTO payment_settings (id, upi_id, contact_no) VALUES (1, '7822957378@fam', '7822957378')");
    }

    echo "<body style='background:black;color:green;text-align:center;padding-top:50px;font-family:sans-serif;'>";
    echo "<h1>✅ DATABASE CREATED SUCCESSFULLY!</h1>";
    echo "<h3>All Tables, Admin & Categories are Ready.</h3>";
    echo "<a href='index.php' style='background:white;color:black;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;'>OPEN APP NOW</a>";
    echo "</body>";

} catch (PDOException $e) {
    // Try empty password if root failed
    if (strpos($e->getMessage(), "Access denied") !== false) {
        echo "<h1>Trying empty password... Refresh page.</h1>";
        // (You can add retry logic here, but usually user just needs to know)
    }
    die("Setup Error: " . $e->getMessage());
}
?>