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
    //Find if the rso exists or not
    $stmt = $conn->prepare('SELECT rsoID FROM RSO WHERE name=?');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();

    //Check if name exists
    if ($row = $result->fetch_assoc()) {
        $rsoID = $row['rsoID'];
        $stmt = $conn->prepare(
            'INSERT INTO Members (userID, rsoID, role) VALUES (?,?, 0)'
        );
        $stmt->bind_param('ii', $userID, $rsoID);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT MembersID FROM Members WHERE rsoID=?");
        $stmt->bind_param('i', $rsoID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >= 5) {
            //$numChange = 0; //initialize the $numChange variable
            $stmt = $conn->prepare(
                "UPDATE RSO SET activation = 1 WHERE rsoID= ?"
            );
            //$stmt->bind_param('ii', $numChange, $rsoID);
            $stmt->bind_param('i', $rsoID);
            $stmt->execute();
            returnWithInfo($userID, $name);
            //print 'active';
        }
    } else {
        //RSO name does not exist
        returnWithError(0, 'RSO name does not exist');
    }

    $stmt->close();
    $conn->close();
}

function returnWithInfo($userid, $name)
	{
		$retValue = '{"userID":' . $userid . ',"name":"' . $name . '","error":""}';
		sendResultInfoAsJson( $retValue );
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
