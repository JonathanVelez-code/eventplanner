<?php

$inData = getRequestInfo();

//Get regester fields from clients JSON POST
$name = $inData['name'];
$location = $inData['location'];
$category = $inData['category'];
$access = $inData['access'];
$dateTime = $inData['dateTime'];
$description = $inData['description'];
$email = $inData['email'];
$phoneNum = $inData['phoneNum'];
$hostRSO = $inData['hostRSO'];
$picturesPath = $inData['picturesPath'];
$approval = $inData['approval'];
$university = $inData['university'];
$rsoID = $inData['rsoID'];

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');

//check if entered data is empty.
if (
    $name == '' ||
    $location == '' ||
    $category == '' ||
    $access == '' ||
    $dateTime == '' ||
    $description == '' ||
    $email == '' ||
    $phoneNum == '' ||
    $hostRSO == '' ||
    $approval == '' ||
    $university == '' ||
    $rsoID == ''
) {
    returnWithError(-1, 'Null Value.');
    die();
}

//test if there any connection errors
if ($conn->connect_error) {
    returnWithError(-10, $conn->connect_error);
} else {
    //Add the new user to the database
    $stmt = $conn->prepare(
        'INSERT INTO Events (name,location,category,access,date,description,email,phoneNum,hostRSO,picturesPath,approval,university,rsoID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $stmt->bind_param(
        'ssssssssssisi',
        $name,
        $location,
        $category,
        $access,
        $dateTime,
        $description,
        $email,
        $phoneNum,
        $hostRSO,
        $picturesPath,
        $approval,
        $university,
        $rsoID
    );
    $stmt->execute();

    //Get the new eventid from the database
    $stmt = $conn->prepare(
        'SELECT eventID FROM Events WHERE name=? AND hostRSO =?'
    );
    $stmt->bind_param('ss', $name, $hostRSO);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $eventid = $row['eventID'];

    //Return the new users info
    returnWithInfo(
        $eventid,
        $name,
        $location,
        $category,
        $access,
        $dateTime,
        $description,
        $email,
        $phoneNum,
        $hostRSO,
        $picturesPath,
        $approval,
        $university,
        $rsoID
    );

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

//Return JSON to user with the users info
function returnWithInfo(
    $eventid,
    $name,
    $location,
    $category,
    $access,
    $dateTime,
    $description,
    $email,
    $phoneNum,
    $hostRSO,
    $picturesPath,
    $approval,
    $university,
    $rsoID
) {
    $retValue =
        '{"event id":' .
        $eventid .
        ',"Name":"' .
        $name .
        '","location":"' .
        $location .
        '","category":"' .
        $category .
        '","access":"' .
        $access .
        '","dateTime":"' .
        $dateTime .
        '","description":"' .
        $description .
        '","email":"' .
        $email .
        '","phoneNum":"' .
        $phoneNum .
        '","hostRSO":"' .
        $hostRSO .
        '","picturesPath":"' .
        $picturesPath .
        '","approvel":"' .
        $approval .
        '","university":"' .
        $university .
        '","rsoID":"' .
        $rsoID .
        '","error":""}';
    sendResultInfoAsJson($retValue);
}

//Return JSON to user with an error message
//PARAM: $errID - the ID of the specific error
//       $errSTR - a message describing the error, mostly for debugging
function returnWithError($errID, $errSTR)
{
    $retValue = '{"id":"' . $errID . '","error":"' . $errSTR . '"}';
    sendResultInfoAsJson($retValue);
}

?>
