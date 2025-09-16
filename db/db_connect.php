<?php
require_once "../config.php";
class Database {
   private $connection;

   public function __construct() {
        $this->connection = new mysqli(HOST, USER, PASSWORD, DB_NAME);
        if ($this->connection->connect_error) {
            error_log("Ошибка подключения к БД: " . $this->connection->connect_error, 3, "../error.log");
            exit;
        }
    }

    public function getConnection() {
        return $this->connection;
    }

}
 
?>