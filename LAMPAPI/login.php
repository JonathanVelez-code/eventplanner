<?php

$inData = getRequestInfo();

//Get fields from clients JSON POST
$userid = 0;
$firstName = '';
$lastName = '';
$email = '';
$univerity = '';
$name = '';

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    //Search for the clients username and password then select data from database
    $stmt = $conn->prepare(
        'SELECT userID,firstName,lastName,email,university FROM Users WHERE username=? AND password=?'
    );
    $stmt->bind_param('ss', $inData['username'], $inData['password']);
    $stmt->execute();
    $result = $stmt->get_result();

    //Check if the correct username & password matches a registered user
    if ($row = $result->fetch_assoc()) {
        //Return the JSON data
        returnWithInfo(
            $row['firstName'],
            $row['lastName'],
            $row['userID'],
            $row['email'],
            $row['university']
        );
    } else {
        $stmt = $conn->prepare(
            'SELECT superAdminID,name FROM SuperAdmin WHERE username=? AND password=?'
        );
        $stmt->bind_param('ss', $inData['username'], $inData['password']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            returnAdminInfo($row['superAdminID'], $row['name']);
        } else {
            returnWithError('No Records Found');
        }
    }

    $stmt->close();
    $conn->close();
}

//Get JSON from client, return as object
function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

//Send JSON to client
function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

//Return JSON to user with an error message when a bad request occurs or unable to find a user.
function returnWithError($err)
{
    $retValue = '{"id":0,"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

//Return JSON to user with the correct users info
function returnWithInfo($firstName, $lastName, $userID, $email, $univerity)
{
    $retValue =
        '{"id":' .
        $userID .
        ',"firstName":"' .
        $firstName .
        '","lastName":"' .
        $lastName .
        '","email":"' .
        $email .
        '", "univerity":"' .
        $univerity .
        '","error":""}';
    sendResultInfoAsJson($retValue);
}

function returnAdminInfo($userID, $name)
{
    $retValue = '{"id":' . $userID . ',"Name":"' . $name . '","error":""}';
    sendResultInfoAsJson($retValue);
}

?>
