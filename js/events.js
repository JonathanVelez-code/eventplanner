// Const Url and extention to reference differnet endpoints in the same JS file
const urlBase = 'http://eventsplanneruni.com/LAMPAPI/';
const extension = 'php';

//Init the userId
let userId;

function readCookie() {
    const cookie = document.cookie.split(';');
    for (let i = 0; i < cookie.length; i++) {
        const cookiePair = cookie[i].split('=');
        if (cookiePair[0].trim() === 'userid') {
            userId = cookiePair[1].trim();
            break;
        }
    }
}

//show all the events
function displayEvents() {

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "http://eventsplanneruni.com/LAMPAPI/events.php");
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8")

    var body = JSON.stringify({
        userID: userId,
    });

    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let events = JSON.parse(xhr.responseText);
            let num = events.length;
            // Loop through each div element and update its content
            for (let i = 0; i < events.length; i++) {
                let name = events[i].name;
                let divId = "name" + (i + 1);
                document.getElementById(divId).innerHTML = name;
            }
            for (let i = 0; i < events.length; i++) {
                let name = events[i].date;
                let divId = "date" + (i + 1);
                document.getElementById(divId).innerHTML = name;
            }
            for (let i = 0; i < events.length; i++) {
                let name = events[i].location;
                let divId = "location" + (i + 1);
                document.getElementById(divId).innerHTML = name;
            }
            for (let i = 0; i < events.length; i++) {
                let name = events[i].hostRSO;
                let divId = "hosted" + (i + 1);
                document.getElementById(divId).innerHTML = name;
            }
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };


    xhr.send(body);
}
