<?php
//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    $query = 'SELECT * FROM Universities';
    $results = $conn->query($query);

    // when empty send error
    if (!$results) {
        returnWithError(-1, 'No results found.');
        die();
    }
    $jsonObj = [];
    echo '<table>';

    while ($row_events = $results->fetch_assoc()) {
        //output a row here
        returnWithInfo($row_events['name']);

        $jsonObj[] = $row_events;
    }
    // holds all info of each event
    $final_res = json_encode($jsonObj);

    echo '</table>';

    $conn->close();
}

//Get JSON from client, return as object
//PARAM: none
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
//PARAM: $firstName, $lastName, $id from database
function returnWithInfo($name)
{
    $retValue = '{"name":' . $name . ',"error":""}';
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
