<?php
// Database connection
if (!isset($pdo)) {
    $host = 'localhost';
    $dbname = 'bookstore';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("❌ Database Error: " . $e->getMessage());
    }
}
// Prevent multiple inclusions
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Prevent function redeclaration
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }
}

?>
