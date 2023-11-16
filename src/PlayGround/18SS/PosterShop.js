function start(){
    document.getElementById("posterSelect").options[0].selected = true;
    show(0);

}
function nextPoster(){
    "use strict"
    let index;
    let posterSelect = document.getElementById("posterSelect");
    let options = posterSelect.options;
    index = posterSelect.selectedIndex;
    index = index + 1;
    if(index >= options.length){
        index = 0;
    }
    options[index].selected = true;
    let headings = document.getElementsByTagName('h1');
    for(let i = 0; i < headings.length; i++){
        headings[i].textContent = "Finde das passende Poster für dein Wohnzimmer";
    }
    show(index);
}

function show(i){
    "use strict";
    let posterSelect = document.getElementById("posterSelect");
    let image = posterSelect.options[i].text;
    document.getElementById("poster").src = "Images/" + image;

}