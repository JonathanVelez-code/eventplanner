<?php

$inData = getRequestInfo();

//Get fields from clients JSON POST
$numChange = $inData['numChange'];
$eventID = $inData['eventID'];

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    $stmt = $conn->prepare('UPDATE Events SET approval = ? WHERE eventID= ?');
    $stmt->bind_param('ii', $numChange, $eventID);
    $stmt->execute();
    print 'Approval UPDATED ';

    $stmt->close();
    $conn->close();
}

//Get JSON from client, return as object
function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

//Return JSON to user with an error message when a bad request occurs or unable to find a user.
function returnWithError($err)
{
    $retValue = '{"id":0,"error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

?>
