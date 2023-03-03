<?php

	$inData = getRequestInfo();
	
	//Get regester fields from clients JSON POST
	$username = $inData["username"];
	$password = $inData["password"];
	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$email = $inData["email"];
	$university = $inData["university"];


	//Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner"); 

	//check if entered data is empty.
	if($username == "" || $password == "" || $firstName == "" || $lastName=="" || $email == "" || $university == ""	)
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
		//Find users with the same username
		$stmt = $conn->prepare("SELECT userID FROM Users WHERE username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();

		//Check if username is taken
		if( $row = $result->fetch_assoc()  ) 
		{
			//A user with a matching username was found
			returnWithError( 0, "User already exists");
		}
		else 
		{	
			//Add the new user to the database
			$stmt = $conn->prepare("INSERT INTO Users (firstName,lastName,username,password,email,university) VALUES (?,?,?,?,?,?)");
			$stmt->bind_param("ssssss", $firstName, $lastName, $username, $password, $email, $university);
			$stmt->execute();

			//Get the new users ID
			$stmt = $conn->prepare("SELECT userID FROM Users WHERE username=? AND password =?");
			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$userid = $row['userID'];

			//Return the new users info
			returnWithInfo($firstName, $lastName, $userid, $email, $university);
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
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	//Return JSON to user with the users info
	function returnWithInfo( $firstName, $lastName, $userid, $email, $university )
	{
		$retValue = '{"id":' . $userid . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","email":"' . $email . '","university":"' . $university . '","error":""}';
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
