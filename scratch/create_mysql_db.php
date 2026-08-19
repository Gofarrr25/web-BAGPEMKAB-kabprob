<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "MySQL is running.\n";

    // Try to create the database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `bagpemkab`");
    echo "Database 'bagpemkab' created or already exists.\n";
} catch (PDOException $e) {
    echo "MySQL connection failed: " . $e->getMessage();
}
