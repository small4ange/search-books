select b.* 
from books b 
left join books_authors ba on b.id = ba.book_id
left join authors a on ba.author_id = a.id
left join books_genres bj on bj.book_id = b.id
left join genres g on bj.genre_id = g.id
where a.name = 'Лев Толстой' or g.name = 'Роман';