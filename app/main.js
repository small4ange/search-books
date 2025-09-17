document.getElementById("searchForm").addEventListener("change", async function(e) {
    e.preventDefault();
    await doSearch(this);
    renderBooks(data);
});

searchForm.addEventListener("submit", async function(e) {
    e.preventDefault();
    await doSearch(this);
});

async function doSearch(form) {
    let formData = new FormData(form);
    let response = await fetch("../api/form_search_books.php", {
        method: "POST",
        body: formData
    });
    let data = await response.json();
    renderBooks(data);
}

function renderBooks(data){
    let container = document.getElementById("booksContainer");
    container.innerHTML = "";

    data.forEach(book => {
        
        let card = document.createElement("div");
        card.classList.add("book-card");
        card.innerHTML = `
        <img src="${book.image}" width="120">
        <div class="book-info">
        <h3>${book.name}</h3>
        <p><b>Авторы:</b> ${book.authors}</p>
        <p><b>Жанры:</b> ${book.genres}</p>
        <p class="description"><b>Описание:</b> ${book.description}</p>
        </div>
        `;
        container.appendChild(card);
    });
}

document.addEventListener("DOMContentLoaded", async () => {
    let response = await fetch("../api/form_search_books.php", {
        method:"POST",
        body: {}
    });
    let data = await response.json();
    renderBooks(data);
});