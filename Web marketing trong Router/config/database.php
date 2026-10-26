<?php
// File: config/database.php
// Cấu hình kết nối database

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mặc định XAMPP là rỗng
define('DB_NAME', 'shopping_mall');

class Database
{
    private $connection;
    private static $instance = null;

    private function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->connection->connect_error) {
                throw new Exception("Kết nối thất bại: " . $this->connection->connect_error);
            }

            $this->connection->set_charset("utf8mb4");

        } catch (Exception $e) {
            die("Lỗi database: " . $e->getMessage());
        }
    }

    // Singleton pattern
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    // Method thực thi query
    public function query($sql, $params = [], $types = "")
    {
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("Lỗi prepare: " . $this->connection->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }

    // Lấy 1 dòng
    public function fetchOne($sql, $params = [], $types = "")
    {
        $stmt = $this->query($sql, $params, $types);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Lấy nhiều dòng
    public function fetchAll($sql, $params = [], $types = "")
    {
        $stmt = $this->query($sql, $params, $types);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Insert và trả về ID
    public function insert($sql, $params = [], $types = "")
    {
        $stmt = $this->query($sql, $params, $types);
        return $this->connection->insert_id;
    }
}
?>