<?php
class DB {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $name = "pos_system";
    public $conn;

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->name);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}
?>
