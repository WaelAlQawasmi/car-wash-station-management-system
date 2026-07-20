<?php

try {
    // Connect to MySQL server (without selecting a DB, since schema.sql creates it)
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Connected to MySQL server.\n";

    // Drop and recreate the database to ensure clean setup
    $pdo->exec("DROP DATABASE IF EXISTS car_stashen");
    echo "Old database dropped.\n";

    $sql = file_get_contents(__DIR__ . '/schema.sql');
    if ($sql === false) {
        throw new Exception("Could not read schema.sql file");
    }

    // Execute schema queries
    // PDO::exec cannot run multiple statements with some drivers, but PDO with mysql usually allows it.
    // Let's use exec to execute the whole script.
    $pdo->exec($sql);
    echo "Database schema imported successfully.\n";

} catch (Exception $e) {
    echo "Error migrating database: " . $e->getMessage() . "\n";
    exit(1);
}
