console.log("ajax-test.js loaded");

async function testAjax() {
    const response = await fetch('/ajax/test');
    const data = await response.json();
    console.log(data);
}

testAjax();

let btn = document.querySelector("#btn-ajax-2");
if(btn!== null){
    btn.addEventListener("click", ()=>{
        testAjax2();
    })
}

async function testAjax2() {
    const response = await fetch('/ajax/test2');
    const data = await response.json();
    console.log(data);
}

testAjax2();

let total = 0;
async function countGenre() {
    const response = await fetch('/ajax/count_genre');
    const data = await response.json();
    const newtotal = data.total_genres;

    if (newtotal !== total && total!==0) {
        alert('Genre ajouté ou supprimé !');
    }
    total = newtotal;
    console.log("Total genres:", total);
}

countGenre();

setInterval(countGenre, 5000);

let btnPokemon = document.querySelector("#btn-pokemon");
if (btnPokemon !== null) {
    btnPokemon.addEventListener("click", () => {
        checkPokemon();
    });
}

async function checkPokemon() {
    const response = await fetch('/ajax/genre_pokemon');
    const data = await response.json();

    if (data.exists) {
        alert('Un genre "pokemon" détecté');
    }
}