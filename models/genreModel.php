<?php

class GenreModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function save($name){
        $stmt = $this->connection->prepare("INSERT INTO genres (name) VALUES (?)");
        $stmt->bind_param("s", $name);

        if (!$stmt->execute()) {
            error_log("Ошибка сохранения нового жанра: " . $this->connection->error, 3, __DIR__ . "/../error.log");
            return false;
        }

        return $this->connection->insert_id;
    }
    public function get_name_by_id($id){
        $stmt = $this->connection->prepare("SELECT name FROM genres WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result && $result->num_rows > 0){
            return $result->fetch_row()[0];
        }
        return null;
    }

    public function get_all_genres_names(){
        $stmt = $this->connection->prepare("SELECT name FROM genres");
        $stmt->execute();
        $result = $stmt->get_result();

        if($result && $result->num_rows > 0){
            $rows = $result->fetch_all(MYSQLI_NUM);
            return array_map(fn($row) => $row[0], $rows);
        }
        return [];
    }

    public function get_id_by_name($name){
        $stmt = $this->connection->prepare("SELECT id FROM genres WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result && $result->num_rows > 0){
            return $result->fetch_row()[0];
        }
        return null;
    }
}
?>
