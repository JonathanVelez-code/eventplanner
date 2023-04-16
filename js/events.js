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
            console.log(JSON.parse(xhr.responseText));
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };


    xhr.send(body);
}
