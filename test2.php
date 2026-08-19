<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bagpemkab', 'root', '', [PDO::ATTR_TIMEOUT => 1]);
    echo "OK. Connection successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
