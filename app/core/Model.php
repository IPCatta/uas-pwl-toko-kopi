<?php
class Model
{
    protected static ?mysqli $db = null;

    public function __construct() {
        if (self::$db === null) self::$db = $this->connect();
    }

    private function connect(): mysqli {
        $config = require CONFIG_PATH . '/database.php';
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['database'], $config['port'] ?? 3306);
        if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);
        $conn->set_charset('utf8mb4');
        return $conn;
    }

    protected function getDb(): mysqli {
        return self::$db;
    }
}
