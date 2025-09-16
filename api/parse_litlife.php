<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../db/db_connect.php";
require_once "../models/bookModel.php";
require_once "../models/authorModel.php";
require_once "../models/genreModel.php";

$db = new Database();
$conn = $db->getConnection();
$bookModel = new BookModel($conn);
$authorModel = new AuthorModel($conn);
$genreModel = new GenreModel($conn);

$conn->query("DELETE FROM books_authors");
$conn->query("DELETE FROM books_genres");
$conn->query("DELETE FROM books");
$conn->query("DELETE FROM authors");
$conn->query("DELETE FROM genres");


$page = rand(1, 10);
$url = "https://litlife.club/books?order=rating_week_desc&page=$page";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$html = curl_exec($ch);
if(curl_errno($ch)){
    error_log("CURL Error: ".curl_error($ch), 3, __DIR__."/../error.log");
}
curl_close($ch);


libxml_use_internal_errors(true);
$dom = new DOMDocument();
if (!$dom->loadHTML($html)) {
    error_log("DOM Load Error", 3,"../error.log");
}
libxml_clear_errors();
$xpath = new DOMXPath($dom);


$booksDiv = $xpath->query("//div[contains(@class,'card')]/div[contains(@class,'card-body')]");
if(!$booksDiv){
    
    error_log("XPath query returned empty result", 3, "../error.log");
}


$counter = 0;
foreach ($booksDiv as $bookDiv) {
    if ($counter >= 20) {
        break;
    }
    // изображение
    $imgNode = $xpath->query(".//div/a/img", $bookDiv); 
    $image = $imgNode->length ? $imgNode[0]->getAttribute("data-src") ?: $imgNode[0]->getAttribute("src") : "";

    // название книги
    $titleNode = $xpath->query(".//h3/span/a", $bookDiv);
    $title = $titleNode->length ? trim($titleNode[0]->textContent) : "";

    // описание
    $descNode = $xpath->query(".//div[contains(@class,'mt-3')]", $bookDiv);
    $description = $descNode->length ? trim($descNode[0]->textContent) : "";

    // авторы
    $authorNode = $xpath->query(".//a[contains(@class,'author')]", $bookDiv);
    $author_ids = [];
    foreach($authorNode as $a) {
        $authorName = trim($a->textContent);
        $author_id = $authorModel->get_id_by_name($authorName);
        
        if (!$author_id) {
            $author_id = $authorModel->save($authorName);
            if (!$author_id) {
                error_log("Ошибка сохранения автора: $authorName", 3, "../error.log");
            }
        }
        $author_ids[] = $author_id;
    }
    // жанры
    $genreNode = $xpath->query(".//div[.//span[contains(text(),'Жанры')]]/span[contains(@class,'font-normal')]/a", $bookDiv);
    $genre_ids = [];
    foreach($genreNode as $g) {
        $genreName = trim($g->textContent);
        if ($genreName === "") {
            continue;
        }
        $genre_id = $genreModel->get_id_by_name($genreName);

        if (!$genre_id) {
            $genre_id = $genreModel->save($genreName);
        }
        $genre_ids[] = $genre_id;
    }

    //сохраняем книгу
    if (!empty($title)) {
        $bookModel->save($title, $description, $image, $author_ids, $genre_ids);
    }

    $counter++;
}