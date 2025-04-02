<?php
namespace Core;

class Database {
    private $connection;

    public function __construct() {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST'),
            getenv('DB_DATABASE')
        );

        $this->connection = new \PDO(
            $dsn,
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }

    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}