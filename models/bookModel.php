<?php
require_once __DIR__ . '/authorModel.php';
require_once __DIR__ . '/genreModel.php';

class BookModel {
    private $connection; 

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function save($name = null, $description = null, $image = null, $author_ids = [], $genre_ids = []) {
        $sql = $this->connection->prepare("INSERT INTO books (name, description, picture) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $name, $description, $image);

        if (!$sql->execute()) {
            error_log("Ошибка сохранения книги: " . $this->connection->error, 3,"../error.log");
            exit;
        }

        $book_id = $this->connection->insert_id;

         if (!empty($author_ids)) {
            $author_stmt = $this->connection->prepare("INSERT INTO books_authors (book_id, author_id) VALUES (?, ?)");
            foreach ($author_ids as $author_id) {
                $author_stmt->bind_param("ii", $book_id, $author_id);
                $author_stmt->execute();
            }
        }

        if (!empty($genre_ids)) {
            $genre_stmt = $this->connection->prepare("INSERT INTO books_genres (book_id, genre_id) VALUES (?, ?)");
            foreach ($genre_ids as $genre_id) {
                $genre_stmt->bind_param("ii", $book_id, $genre_id);
                $genre_stmt->execute();
            }
        }

        return $book_id;
    }

    public function searchBooks ($bookName, $authors, $genres){
        $sql = "SELECT b.id, b.name, b.description, b.picture AS image, GROUP_CONCAT(DISTINCT a.name) AS authors, GROUP_CONCAT(DISTINCT g.name) AS genres
                FROM books b 
                LEFT JOIN books_authors ba ON b.id = ba.book_id
                LEFT JOIN authors a ON ba.author_id = a.id
                LEFT JOIN books_genres bj ON bj.book_id = b.id
                LEFT JOIN genres g ON bj.genre_id = g.id
                WHERE 1=1
        ";
        $bind_params = [];
        $types="";
        if(!empty($bookName)){
            $sql.=" AND b.name LIKE ?";
            $bind_params[] = "%".$bookName."%";
            $types.="s";
        }
        if(!empty($genres)) {
            $sql.= " AND g.name IN (". implode(',', array_fill(0, count($genres), '?')) . ")";
            $bind_params = array_merge($bind_params, $genres);
            $types.=implode('',array_fill(0,count($genres),"s"));
        }
        if(!empty($authors)){
            $sql.= " AND a.name IN (". implode(',', array_fill(0, count($authors), '?')) . ")";
            $bind_params = array_merge($bind_params, $authors);
            $types.=implode('',array_fill(0,count($authors),"s"));
        }
        $sql.=" GROUP BY b.id";
        $res=$this->connection->prepare($sql);
        if($bind_params){
            $res->bind_param($types, ...$bind_params);
        }
        $res->execute();
        $result = $res->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

}
?>

