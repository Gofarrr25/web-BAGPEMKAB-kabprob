<?php
try {
    // Source: SQLite
    $sqliteDbPath = __DIR__ . '/../database/database.sqlite';
    $sqlite = new PDO("sqlite:" . $sqliteDbPath);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Destination: MySQL
    $mysql = new PDO("mysql:host=127.0.0.1;port=3306;dbname=bagpemkab", "root", "");
    $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mysql->exec("SET FOREIGN_KEY_CHECKS=0;");

    // Get all tables from SQLite
    $stmt = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        if ($table === 'pages') {
            echo "Skipping table: $table (due to max_allowed_packet limits for large content)\n";
            continue;
        }
        echo "Migrating table: $table\n";
        
        // Fetch all rows from SQLite
        $rows = $sqlite->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) === 0) {
            echo "  No data to migrate.\n";
            continue;
        }

        // Empty the MySQL table first (optional, but good if we just migrated and it's empty anyway)
        $mysql->exec("TRUNCATE TABLE `$table`");

        // Insert into MySQL
        $columns = array_keys($rows[0]);
        $columnsList = implode(', ', array_map(function($c) { return "`$c`"; }, $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        
        $insertStmt = $mysql->prepare("INSERT INTO `$table` ($columnsList) VALUES ($placeholders)");
        
        try {
            $count = 0;
            foreach ($rows as $row) {
                $values = array_values($row);
                $insertStmt->execute($values);
                $count++;
            }
            echo "  Migrated $count rows.\n";
        } catch (PDOException $e) {
            echo "  Error migrating table $table: " . $e->getMessage() . "\n";
            // Continue with the next table
        }
    }

    $mysql->exec("SET FOREIGN_KEY_CHECKS=1;");
    echo "\nData migration completed successfully.\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
