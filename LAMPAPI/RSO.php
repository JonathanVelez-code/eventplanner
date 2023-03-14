<?php

	$inData = getRequestInfo();
	
	//Get regester fields from clients JSON POST
	$adminEmail = $inData["adminEmail"];
	$name = $inData["name"];
	$emails = $inData["emails"];
    $description = $inData["description"];
    $admin = "admin";

	//Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner"); 

	//check if entered data is empty.
	if($adminEmail == "" || $name == "" || $emails == "")
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
		//Find users with the same admin email
		$stmt = $conn->prepare("SELECT userID FROM Users WHERE email=?");
		$stmt->bind_param("s", $adminEmail);
		$stmt->execute();
		$result = $stmt->get_result();


		//Check if email is taken
		if( $row = $result->fetch_assoc()  ) 
		{
            $adminID = $row['userID'];

			//Add the new rso to the database
			$stmt = $conn->prepare("INSERT INTO RSO (adminID, name, description) VALUES (?,?,?)");
			$stmt->bind_param("sss", $adminID, $name, $description);
			$stmt->execute();

			//Get the new RSO ID
			$stmt = $conn->prepare("SELECT rsoID FROM RSO WHERE name=?");
			$stmt->bind_param("s", $name);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$rsoid = $row['rsoID'];


            //Add the admin to the members
			$stmt = $conn->prepare("INSERT INTO Members (userID, rsoID, role) VALUES (?,?, 1)");
			$stmt->bind_param("ss", $adminID, $rsoid);
			$stmt->execute();

			//Return the new RSO info
			returnWithInfo($rsoid, $adminID, $name, $description);
		}
		else 
		{	
            // if email does not exist
            returnWithError( 0, "User does not exist");
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
