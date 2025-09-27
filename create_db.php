<?php
// Create MySQL database for Laravel e-commerce project

$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'ecommerce';

try {
    // Create connection without specifying database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    
    echo "Database '$database' created successfully!\n";
    
    // Test connection to the new database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    echo "Connection to database '$database' successful!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nMake sure XAMPP MySQL is running!\n";
    echo "You can start it from XAMPP Control Panel.\n";
}
?>