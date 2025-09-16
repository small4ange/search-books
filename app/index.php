<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SearchBooks</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 0); 
        ini_set("log_errors", 1);
        ini_set("error_log", "../error.log");

        require_once "../db/db_connect.php";
        require_once "../models/genreModel.php";
        require_once "../models/bookModel.php";
        require_once "../models/authorModel.php";

        $db = new Database();
        $connection = $db->getConnection();
        $genreModel = new GenreModel($connection);
        $authorModel = new AuthorModel($connection);
        $bookModel = new BookModel($connection);

    ?>
    <div class="container">
        <form id="searchForm">
            <input type="text" name="bookName" placeholder="Название книги" class="form-text-input">
            <details class="genres">
                <summary>Жанры</summary>
                <div class="checkbox-container"> 
                    <?php
                        $genres = $genreModel->get_all_genres_names();
                        foreach ($genres as $genre) {
                            echo '<input type="checkbox" name="genres[]" value="'.htmlspecialchars($genre).'">';
                            echo '<label>'.$genre.'</label>';
                        }
                    ?>   
                </div>
            </details>
            
            <details class="authors">
                <summary>Авторы</summary>
                <div class="checkbox-container"> 
                    <?php
                        $authors = $authorModel->get_all_authors_names();
                        foreach ($authors as $author) {
                            echo '<input type="checkbox" name="authors[]" value="'.htmlspecialchars($author).'">';
                            echo '<label>'.$author.'</label>';
                        }
                    ?>   
                </div>
            </details>
            
            <button type="submit" class="btn-success">Поиск</button>
        </form>
        <div class="books-container" id="booksContainer"></div>
    </div>
    <script src="./main.js"></script>
</body>
</html>