<?php

$inData = getRequestInfo();
$userid = $inData['userID'];

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    //Get user's university
    $stmt = $conn->prepare('SELECT university FROM Users WHERE userID=?');
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $university = $row['university'];
    $stmt->close();

    // get all RSO's user is part of
    $stmt = $conn->prepare('SELECT rsoID FROM Members WHERE userID=?');
    $stmt->bind_param('i', $userid);
    $stmt->execute();
    $club = $stmt->get_result();
    $stmt->close();

    // get events table
    $query = 'SELECT * FROM Events';
    $results = $conn->query($query);

    // if
    if (!$results) {
        die();
    }

    $jsonObj = [];

    while ($row_events = $results->fetch_assoc()) {
        //output a row here

        // output if event is public or same as user's uni
        if (
            $row_events['access'] == 'public' ||
            $row_events['university'] == $university
        ) {
            $jsonObj[] = [
                'name' => $row_events['name'],
                'date' => $row_events['date'],
                'location' => $row_events['location'],
                'description' => $row_events['description'],
                'category' => $row_events['category'],
                'access' => $row_events['access'],
            ];
        }

        // output if user is member of event's club
        while ($rso = $club->fetch_assoc()) {
            if ($row_events['rsoID'] == $rso['rsoID']) {
                $jsonObj[] = [
                    'name' => $row_events['name'],
                    'date' => $row_events['date'],
                    'location' => $row_events['location'],
                    'description' => $row_events['description'],
                    'category' => $row_events['category'],
                    'access' => $row_events['access'],
                ];
            }
        }
    }

    // holds all info of each event
    $final_res = json_encode($jsonObj);

    $conn->close();
    sendResultInfoAsJson($final_res);
}

//Get JSON from client, return as object
//PARAM: none
function getRequestInfo()
{
    return json_decode(file_get_contents('php://input'), true);
}

//Send JSON to client
//PARAM: $obj - A JSON object
function sendResultInfoAsJson($obj)
{
    header('Content-type: application/json');
    echo $obj;
}

//Return JSON to user with an error message
//PARAM: $errID - the ID of the specific error
//       $errSTR - a message describing the error, mostly for debugging
function returnWithError($errID, $errSTR)
{
    $retValue =
        '{"userID":"' .
        $errID .
        '","firstName":"","lastName":"","error":"' .
        $errSTR .
        '"}';
    sendResultInfoAsJson($retValue);
}

?>
