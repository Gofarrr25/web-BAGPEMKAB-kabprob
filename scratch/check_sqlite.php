<?php
try {
    $dbPath = __DIR__ . '/../database/database.sqlite';
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $summary = [];
    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        if ($count > 0) {
            $summary[$table] = $count;
        }
    }

    echo "Tables with data:\n";
    foreach ($summary as $table => $count) {
        echo "- $table: $count rows\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
