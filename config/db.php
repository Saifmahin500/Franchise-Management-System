<?php
$host = "localhost";
$dbname = "franchise_db";   // তোমার database নাম
$username = "root";         // XAMPP default user
$password = "";             // XAMPP default password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
