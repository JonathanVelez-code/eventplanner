<?php

	$inData = getRequestInfo();
	
	//Get regester fields from clients JSON POST
	$username = $inData["userName"];
	$password = $inData["password"];
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$university = $inData["university"];


	//Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner"); 

	if($username == "" || $password == "" || $firstName == "" || $lastName=="" || $email = "" || $university = "")
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
		$stmt = $conn->prepare("SELECT userID FROM Users WHERE userName=?");
		$stmt->bind_param("s", $username);
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
			$stmt = $conn->prepare("INSERT into Users (firstName,lastName,userName,password, email, university) VALUES (?,?,?,?,?,?)");
			$stmt->bind_param("ssssss", $firstName, $lastName, $username, $password, $email, $university);
			$stmt->execute();

			//Get the new users ID
			$stmt = $conn->prepare("SELECT userID FROM Users WHERE userName=? AND password =?");
			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$userid = $row['userID'];

			//Return the new users info
			returnWithInfo($firstName, $lastName, $userid);
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
	function returnWithInfo( $firstName, $lastName, $userid )
	{
		$retValue = '{"userID":' . $userid . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
	//Return JSON to user with an error message
	//PARAM: $errID - the ID of the specific error
	//       $errSTR - a message describing the error, mostly for debugging
	function returnWithError($errID ,$errSTR )
	{
		$retValue = '{"userID":"' . $errID . '","firstName":"","lastName":"","error":"' . $errSTR . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>