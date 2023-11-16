function pollData(){
    requestData();
    window.setInterval(requestData, 10000);
}

function process(intext){
    let dataObject = JSON.parse(intext)[0];
    let players = document.getElementById("zusage");
    players.firstChild.nodeValue = dataObject.playing;
}

let request = new XMLHttpRequest();

function requestData() { // fordert die Daten asynchron an
    "use strict";
    let id = document.getElementById("gameID").value;
    request.open("GET", "Exam21API.php?gameID="+id);
    request.onreadystatechange = processData;
    request.send(null);
}

function processData() {
    "use strict";
    if (request.readyState === 4) { // Uebertragung = DONE
        if (request.status === 200) { // HTTP-Status = OK
            if (request.responseText != null)
               process(request.responseText);
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } // else; // Uebertragung laeuft noch
}

