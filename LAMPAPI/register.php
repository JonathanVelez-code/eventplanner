<?php

	$inData = getRequestInfo();
	
	//Get regester fields from clients JSON POST
	$login = $inData["login"];
	$password = $inData["password"];
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];

	//Connect to mySQL
	$conn = new mysqli("localhost", "Beast", "COP4331", "CONTACT_MANAGER"); 

	if($login == "" || $password == "" || $firstName == "" || $lastName=="")
	{
	  returnWithError( -1, "Null Value." );
	  die();
	}

	if( $conn->connect_error )
	{
		returnWithError( -10, $conn->connect_error );
	}
	else
	{
		//Find users with the regestering clients login
		$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login=?");
		$stmt->bind_param("s", $login);
		$stmt->execute();
		$result = $stmt->get_result();

		//Check if login is taken
		if( $row = $result->fetch_assoc()  ) 
		{
			//A user with a matching login was found
			returnWithError( 0, "User already exists");
		}
		else 
		{	
			//Add the new user to the database
			$stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
			$stmt->execute();

			//Get the new users ID
			$stmt = $conn->prepare("SELECT ID FROM Users WHERE Login=? AND Password =?");
			$stmt->bind_param("ss", $login, $password);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$id = $row['ID'];

			//Return the new users info
			returnWithInfo($firstName, $lastName, $id);
		}

		$stmt->close();
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
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	//Return JSON to user with the users info
	//PARAM: $firstName, $lastName, $id from database
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
	//Return JSON to user with an error message
	//PARAM: $errID - the ID of the specific error
	//       $errSTR - a message describing the error, mostly for debugging
	function returnWithError($errID ,$errSTR )
	{
		$retValue = '{"id":"' . $errID . '","firstName":"","lastName":"","error":"' . $errSTR . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>