<?php
/**
 * Base Model — kedai-kopi
 *
 * Menyediakan koneksi database mysqli yang dibagikan ke semua model.
 * Semua query WAJIB memakai prepared statements (prepare + bind_param).
 *
 * Koneksi dibuat satu kali (singleton) dari config/database.php,
 * lalu dibagikan ke seluruh instance Model.
 */
class Model
{
    /**
     * Koneksi mysqli yang dibagikan.
     *
     * @var mysqli|null
     */
    protected static ?mysqli $db = null;

    /**
     * Constructor — inisialisasi koneksi DB jika belum ada.
     */
    public function __construct()
    {
        if (self::$db === null) {
            self::$db = $this->connect();
        }
    }

    /**
     * Buat koneksi ke database MySQL.
     * Mengambil konfigurasi dari config/database.php.
     *
     * @return mysqli
     */
    private function connect(): mysqli
    {
        $config = require CONFIG_PATH . '/database.php';

        $conn = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database'],
            $config['port'] ?? 3306
        );

        if ($conn->connect_error) {
            die('Koneksi database gagal: ' . $conn->connect_error);
        }

        // Set charset utf8mb4
        $conn->set_charset('utf8mb4');

        return $conn;
    }

    /**
     * Getter untuk koneksi DB (agar child class bisa akses).
     *
     * @return mysqli
     */
    protected function getDb(): mysqli
    {
        return self::$db;
    }
}
