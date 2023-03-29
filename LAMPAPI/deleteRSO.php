<?php

$inData = getRequestInfo();

//Get fields from clients JSON POST
$rsoID = $inData['rsoID'];
$adminID = $inData['adminID'];
$name = $inData['name'];

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');

//test if there any connection errors
if ($conn->connect_error) {
    returnWithError(-10, $conn->connect_error);
} else {
    $stmt = $conn->prepare('DELETE FROM RSO WHERE rsoID=?');
    $stmt->bind_param('i', $rsoID);
    $stmt->execute();

    $stmt = $conn->prepare('SELECT rsoID FROM RSO WHERE adminID=?');
    $stmt->bind_param('i', $adminID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 5) {
        $numChange = 1; //initialize the $numChange variable
        $stmt = $conn->prepare('UPDATE RSO SET activation = ? WHERE name= ?');
        $stmt->bind_param('is', $numChange, $name);
        $stmt->execute();
        print 'Not active';
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

?>
