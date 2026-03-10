<?php
class Database
{
    private $host = "localhost";
    private $db_name = "gamekey_store";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";
    public $conn;

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES utf8mb4");
        }
        catch (PDOException $exception) {
            error_log("Database Connection Error: " . $exception->getMessage());
            die(json_encode(["error" => "Database connection failed"]));
        }
        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn = null;
    }
}

?>
