<?php
header('Content-Type: text/plain');

// 1. Проверка PHP
echo "PHP Version: ".phpversion()."\n";
echo "Extensions: ".implode(", ", get_loaded_extensions())."\n\n";

// 2. Проверка БД
try {
    $db = new PDO(
        'mysql:host=localhost;dbname=mvolikfg_2;charset=utf8mb4',
        'mvolikfg_2',
        'Mvolik683',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "✅ DB Connection: Successful\n";
    echo "Tables: ".implode(", ", $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN))."\n";
} catch (PDOException $e) {
    die("❌ DB Error: ".$e->getMessage());
}

// 3. Проверка файловой системы
echo "\nFile System:\n";
echo "Path: ".realpath(__DIR__)."\n";
echo "Writeable: ".(is_writable(__DIR__) ? 'Yes' : 'No')."\n";