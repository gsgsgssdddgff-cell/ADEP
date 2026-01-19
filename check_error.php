<?php
// Ye 3 lines error ko screen par dikhayengi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 DIAGNOSTIC TOOL</h1>";

// 1. Check Config
echo "Checking common/config.php... ";
if (file_exists('common/config.php')) {
    include 'common/config.php';
    echo "<span style='color:green'>FOUND & LOADED ✅</span><br>";
} else {
    die("<span style='color:red'>MISSING! ❌ (common/config.php nahi mila)</span>");
}

// 2. Check Database
echo "Checking Database Connection... ";
if (isset($pdo)) {
    echo "<span style='color:green'>CONNECTED ✅</span><br>";
} else {
    echo "<span style='color:red'>FAILED ❌ (Database connect nahi hua)</span><br>";
}

// 3. Check Index
echo "Checking index.php syntax... <br>";
include 'index.php';
?>