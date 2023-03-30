<?php

$inData = getRequestInfo();

//Get fields from clients JSON POST
$userID = $inData['userID'];
$name = $inData['name'];

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');

//test if there any connection errors
if ($conn->connect_error) {
    returnWithError(-10, $conn->connect_error);
} else {
    // Get the new RSO ID
    $stmt = $conn->prepare('SELECT rsoID FROM RSO WHERE name=?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $rsoid = $row['rsoID'];

    $stmt = $conn->prepare('DELETE FROM Members WHERE userID=? AND rsoID=?');
    $stmt->bind_param('ii', $userID, $rsoid);
    $stmt->execute();

    $stmt = $conn->prepare('SELECT MembersID FROM Members WHERE rsoID=?');
    $stmt->bind_param('i', $rsoid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 5) {
        $numChange = 1; // Initialize the $numChange variable
        $stmt = $conn->prepare('UPDATE RSO SET activation = ? WHERE rsoID= ?');
        $stmt->bind_param('ii', $numChange, $rsoid);
        $stmt->execute();
        echo 'Not active';
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
