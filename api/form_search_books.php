<?php
require_once "../db/db_connect.php";
require_once "../models/bookModel.php";

error_reporting(E_ALL);
ini_set("display_errors", 0); 
ini_set("log_errors", 1);
ini_set("error_log", "../error.log");

$db = new Database();
$bookModel = new BookModel($db->getConnection());

header("Content-Type: application/json");

$bookName = $_POST['bookName'] ?? '';
$authors = $_POST['authors'] ?? [];
$genres = $_POST['genres'] ?? [];

$books = $bookModel->searchBooks($bookName, $authors, $genres);

echo json_encode($books, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
 
?>