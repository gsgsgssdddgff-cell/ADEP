<?php
// common/config.php

$host = '127.0.0.1';
$db   = 'adept_cinema_db';
$user = 'root';

// 1. CONNECTION LOGIC (AWebServer uses 'root', KSWEB uses '')
try {
    // Pehle 'root' password se try karte hain (Best for AWebServer)
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    
    // Agar 'root' password galat hai, to Empty (Khali) password try karte hain
    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    } catch (PDOException $e2) {
        
        // 2. ERROR HANDLING
        
        // Agar Database hi nahi bana (Error 1049)
        if ($e2->getCode() == 1049) {
            die("
            <body style='background:black;color:white;text-align:center;padding-top:50px;'>
                <h1 style='color:red;'>DATABASE MISSING</h1>
                <p>Database <b>'adept_cinema_db'</b> nahi mila.</p>
                <a href='../install.php' style='background:green;padding:10px 20px;text-decoration:none;color:white;font-weight:bold;border-radius:5px;'>INSTALL DATABASE</a>
            </body>");
        }
        
        // Agar Server Band hai (Error 2002)
        if ($e2->getCode() == 2002) {
            die("
            <body style='background:black;color:white;text-align:center;padding-top:50px;'>
                <h1 style='color:red;'>MySQL IS OFF</h1>
                <p>Please open <b>AWebServer / KSWEB</b> app and start MySQL.</p>
            </body>");
        }

        // Koi aur Error
        die("<h3>Database Error: " . $e2->getMessage() . "</h3>");
    }
}
?>