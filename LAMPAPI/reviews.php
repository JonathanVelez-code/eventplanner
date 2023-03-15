<?php

	$inData = getRequestInfo();
	
	//Get regester fields from clients JSON POST
    $name = $inData['name'];
    $description = $inData['description'];
    $dateTime = $inData['dateTime'];
    $ratingScale = $inData['ratingScale'];
	$eventname = $inData['eventname'];
    

	//Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner"); 

	//check if entered data is empty.
	if($name == "" || $description == "" || $dateTime == ""  || $ratingScale == "")
	{
	  returnWithError( -1, "Null Value." );
	  die();
	}

	//test if there any connection errors
	if( $conn->connect_error )
	{
		returnWithError( -10, $conn->connect_error );
	}
	else
	{	
		//Get the eventID from the database
		$stmt = $conn->prepare("SELECT eventID FROM Events WHERE name=?");
		$stmt->bind_param("s", $eventname);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$eventid = $row['eventID'];
		$stmt->close(); //no close will crash.

		
		//Add the new user to the database
		$stmt = $conn->prepare("INSERT INTO Ratings (name,description,date,ratingScale,eventID) VALUES (?,?,?,?,?)");
		$stmt->bind_param("sssii", $name, $description, $dateTime, $ratingScale, $eventid);
		$stmt->execute();
	
		
		//Get the new commentID from the database
		$stmt = $conn->prepare("SELECT commentID FROM Ratings WHERE name=?");
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$commentid = $row['commentID'];
		

		//Return the new users info
		returnWithInfo($commentid, $name, $description, $date, $ratingScale, $eventid);
		

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
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	//Return JSON to user with the users info
	function returnWithInfo( $commentid, $name, $description, $date, $ratingScale, $eventid)
	{
		$retValue = '{"comment id":' . $commentid . ',"Name":"' . $name . '","description":"' . $description . '","date":"' . $date . '","ratingScale":"' . $ratingScale . '","EventID":"' . $eventid . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
	//Return JSON to user with an error message
	//PARAM: $errID - the ID of the specific error
	//       $errSTR - a message describing the error, mostly for debugging
	function returnWithError($errID ,$errSTR )
	{
		$retValue = '{"id":"' . $errID . '","error":"' . $errSTR . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
