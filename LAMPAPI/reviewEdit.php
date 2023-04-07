<?php

$inData = getRequestInfo();

//Get fields from clients JSON POST
$userID = $inData['userID'];
$eventID = $inData['eventID'];
$description = $inData['description'];

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');

//test if there any connection errors
if ($conn->connect_error) {
    returnWithError(-10, $conn->connect_error);
} else {
    // Find username by using userID
    $stmt = $conn->prepare('SELECT name FROM Users WHERE userID=?');
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $name = $row['name'];

    // Find the name of the user from Ratings table using the name column
    $stmt = $conn->prepare('SELECT name FROM Ratings WHERE name=?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        returnWithError(0, 'Review not found');
    } else {
        // Update the description for the given event and user
        $stmt = $conn->prepare(
            'UPDATE Ratings SET description = ? WHERE eventID = ? AND name = ?'
        );
        $stmt->bind_param('sis', $description, $eventID, $name);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();
}

//Get JSON from client, return as object
function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

//Send JASON to client
//PARAM: $obj - A JSON object
function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($errID, $errSTR)
{
    $retValue = '{"id":"' . $errID . '","error":"' . $errSTR . '"}';
    sendResultInfoAsJson($retValue);
}

?>
