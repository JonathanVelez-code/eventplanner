<?php

	$inData = getRequestInfo();
	
	//Get regester fields from clients JSON POST
	$userID = $inData["userID"];
	$name = $inData["name"];

	//Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner"); 

	//check if entered data is empty.
	if($userID == "" || $name == "")
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
		echo $userID;
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
	function returnWithInfo($rsoid, $adminID, $name, $description)
	{
		$retValue = '{"rsoID":' . $rsoid . ',"name":"' . $name . '","adminID":"' . $adminID .'","description":"' . $description . '","error":""}';
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
