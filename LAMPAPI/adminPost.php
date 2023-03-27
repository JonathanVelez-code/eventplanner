<?php

$inData = getRequestInfo();

//Get fields from clients JSON POST
$eventApproval = $inData['eventApproval'];

//Connect to mySQL
$conn = new mysqli('localhost', 'AdminUser', 'cop4710Data@', 'EventPlanner');
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
} else {
    //Search for event details
    $stmt = $conn->prepare(
        'SELECT eventID, name, location, category, access, date, description, email, phoneNum, hostRSO, university FROM Events WHERE approval=?'
    );
    $stmt->bind_param('i', $eventApproval);
    $stmt->execute();
    $result = $stmt->get_result();

    //Check if the name exists
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            //Return the JSON data
            returnWithInfo(
                $row['eventID'],
                $row['name'],
                $row['location'],
                $row['category'],
                $row['access'],
                $row['date'],
                $row['description'],
                $row['email'],
                $row['phoneNum'],
                $row['hostRSO'],
                $row['university']
            );
        }
    } else {
        returnWithError('No Records Found');
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
function returnWithInfo(
    $evetID,
    $eventName,
    $location,
    $category,
    $access,
    $date,
    $description,
    $email,
    $phoneNum,
    $hostRSO,
    $university
) {
    $retValue =
        '{"eventID":' .
        $evetID .
        ',"name":' .
        $eventName .
        ',"location":"' .
        $location .
        '","category":"' .
        $category .
        '","access":"' .
        $access .
        '", "date":"' .
        $date .
        '","description":"' .
        $description .
        '","email":"' .
        $email .
        '","phoneNum":"' .
        $phoneNum .
        '","hostRSO":"' .
        $hostRSO .
        '","university":"' .
        $university .
        '","error":""}';
    sendResultInfoAsJson($retValue);
}

?>
