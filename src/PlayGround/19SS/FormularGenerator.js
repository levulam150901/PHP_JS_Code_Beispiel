window.onload = getInputData;
function sendData(){
    "use strict";

    let elem = event.target;
    let key = elem.name;
    let value = elem.value;
    let param = "key=" + key + "&value=" + encodeURI(value);

    let xml = new XMLHttpRequest();
    xml.open("GET", "FormularGenerator.php?"+param);
    xml.send();
}

function getInputData(){
    "use strict";
    let inputs = document.getElementsByTagName("input");
    for (let i = 0; i < inputs.length; i++){
        inputs[i].addEventListener("change", sendData);
    }
}