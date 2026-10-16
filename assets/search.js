let input = document.querySelector("#search-album");
let results = document.querySelector("#search-results");

if (input !== null) {
    input.addEventListener("input", () => {
        searchAlbums();
    });
}

async function searchAlbums() {
    const response = await fetch(`/search?album=${input.value}`);
    const data = await response.json();
    console.log(data);

    let text = "";
    for (const album of data) {
        text += `<p>${album.title} - ${album.artist.stageName}</p>`;
    }
    results.innerHTML = text;
}
