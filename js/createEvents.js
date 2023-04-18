

function createlistEvents() {
    // Pull the data receievd from the user to send to the API.
    let eventName = document.getElementById("EventName").value;
    let date = document.getElementById("Date").value;
    let time = document.getElementById("Time").value;
    let location = document.getElementById("Location").value;
    let category = document.getElementById("Category").value;
    let email = document.getElementById("Email").value;
    let phoneNumber = document.getElementById("PhoneNumber").value;
    let description = document.getElementById("Description").value;
    let accessibility = document.getElementById("Accessibility").value;
    let dateTime = date + " " + time;
    let university = "University of Central Florida";
    let hostRSO = "Club2";

    const data = {
        name: eventName,
        location: location,
        category: category,
        access: accessibility,
        dateTime: dateTime,
        description: description,
        email: email,
        phoneNum: phoneNumber,
        hostRSO: hostRSO,
        picturesPath: 1, //by default
        approval: 1, //by default
        university: university,
        rsoID: 99, //by default
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "http://eventsplanneruni.com/LAMPAPI/eventsPost.php");
    xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            console.log(response);

            window.location.href = "event.html";
        }
    };

    xhr.send(JSON.stringify(data));

}