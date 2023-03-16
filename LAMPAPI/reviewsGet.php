<?php

    $inData = getRequestInfo();

    //Get fields from clients JSON POST
	$eventName = $inData["name"];

    //Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner");
    if ($conn->connect_error)
    {
        returnWithError($conn->connect_error);
    }
    else
	{

        //Get the event ID
		$stmt = $conn->prepare("SELECT eventID FROM Events WHERE name=?");
		$stmt->bind_param("s", $eventName);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$eventid = $row['eventID'];

		//Get reviews of this event
		$query = "SELECT * FROM Ratings WHERE eventID = '$eventid'";
		$result = $conn->query($query);	

        if (!$result) {
            die();
        }

        $jsonObj = array();

        // output all reviews
        while ($row_events = $result->fetch_assoc()) {
            
            returnWithInfo($row_events['name'],$row_events['date'], $row_events['ratingScale'], $row_events['description']);

            $jsonObj[] = $row_events;
        }

        $final_res = json_encode($jsonObj);


		$stmt->close();
		$conn->close();
	}
	
	//Get JSON from client, return as object
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	//Send JSON to client
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}

	//Return JSON to user with an error message when a bad request occurs or unable to find a user.
	function returnWithError( $err )
	{
		$retValue = '{"id":0,"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	//Return JSON to user with the correct users info
	function returnWithInfo( $name, $date, $ratingScale, $description )
	{
		$retValue = '{"name":"' . $name . '","ratingScale":"' . $ratingScale . '","date":"' . $date . '","description":"' . $description . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>