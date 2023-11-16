function pollNews() {
    "use strict";
    requestData();
    window.setInterval(requestData, 8000);
}

function createDOM(row){
    "use strict";
    let newsEntry = document.createElement("section");
    let title = document.createElement("h2");
    let textNodeTitle = document.createTextNode(row.title);
    title.appendChild(textNodeTitle);
    let time = document.createElement("p");
    time.className = "timestamp";
    let textNodeTime = document.createTextNode(row.timestamp);
    time.appendChild(textNodeTime);
    let text = document.createElement("p");
    let textNodeText = document.createTextNode(row.text);
    text.appendChild(textNodeText);
    newsEntry.appendChild(title);
    newsEntry.appendChild(time);
    newsEntry.appendChild(text);
    return newsEntry;
}

function processNews(data) {
    "use strict";
    let newsList = JSON.parse(data);
    let newsContainer = document.getElementById("news_container");

    if(newsList.length === 0){
        console.error("No news data found.");
        return;
    }

    while (newsContainer.firstChild) {
        newsContainer.removeChild(newsContainer.lastChild);
    }

    for(let i = 0; i < newsList.length; i++){
        let row = newsList[i];
        if(newsContainer && row.timestamp && row.title && row.text){
            let articel = createDOM(row);
            newsContainer.appendChild(articel);
        }
    }
}

let request = new XMLHttpRequest();
function requestData() {
    "use strict";
    request.open("GET", "News.php?JSON=1");
    request.onreadystatechange = processData;
    request.send(null);
}

function processData() {
    "use strict";
    if (request.readyState == 4) {
        if (request.status == 200) {
            if (request.responseText != null)
                processNews(request.responseText);
            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } else; }