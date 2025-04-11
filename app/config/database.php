<?php


class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $db_name = "webbantruyentranh";
    public $conn;
    public function getConnection() {
         $this->conn = null;
         try{
            $this->conn = new PDO("mysql:host=" . $this->servername . ";dbname=" . $this->db_name, $this->username, $this->password);   
            $this->conn->exec("set names utf8");
         }catch(PDOException $e){
            echo "Connection failed: " . $e->getMessage();
         }
         return $this->conn;
    }

// Create connection
}
?>