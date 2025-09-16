<?php
class AuthorModel {
    private $connection;


    public function __construct ($connection){
        $this->connection = $connection;
    }

    public function save($name){
        $sql = $this->connection->prepare("INSERT INTO authors (name) VALUES (?)");
        $sql->bind_param("s", $name);

        if (!$sql->execute()) {
            error_log("Ошибка сохранения нового автора: " . $this->connection->error, 3, __DIR__ . "/../error.log");
            return false;
        }
        return $this->connection->insert_id;
    }

    public function get_name_by_id($id){
        $sql = $this->connection->prepare("SELECT name FROM authors WHERE id = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        if($result && $result->num_rows > 0){
            return $result->fetch_row()[0];
        }
        return null;
    }

    public function get_all_authors_names(){
        $sql = $this->connection->prepare("SELECT name FROM authors");
        $sql->execute();
        $result = $sql->get_result();
        if($result && $result->num_rows > 0){
            $rows = $result->fetch_all(MYSQLI_NUM);
            return array_map(fn($row) => $row[0], $rows);
        }
        return [];
    }


    public function get_id_by_name($name){
        $sql = $this->connection->prepare("SELECT id FROM authors WHERE name = ?");
        $sql->bind_param("s", $name);
        $sql->execute();
        $result = $sql->get_result();
        if($result && $result->num_rows > 0){
            return $result->fetch_row()[0];
        }
        return null;
    }
        
}

?>