<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bagpemkab;charset=utf8mb4', 'root', '', [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in bagpemkab:\n";
    foreach($tables as $table) {
        echo "- " . $table . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
