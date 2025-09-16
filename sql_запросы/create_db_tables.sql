create database search_books_bd;

use search_books_bd;

create table books (
	id INT auto_increment primary key ,
    picture varchar(500) unique,
    name varchar(100),
    description varchar(1000)
);

create table authors (
	id INT auto_increment primary key ,
    name varchar(100)
);

create table genres (
	id INT auto_increment primary key ,
    name varchar(100)
);

create table books_authors (
    author_id int,
    book_id int,
	primary key (book_id, author_id),
    foreign key (author_id) references authors(id) on delete cascade,
    foreign key (book_id) references books(id) on delete cascade
);

create table books_genres (
    genre_id int,
    book_id int,
    primary key (book_id, genre_id),
    foreign key (genre_id) references genres(id) on delete cascade,
    foreign key (book_id) references books(id) on delete cascade
);