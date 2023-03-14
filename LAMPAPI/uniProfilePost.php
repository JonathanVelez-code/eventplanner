<?php

	$inData = getRequestInfo();
	
	//Get regester fields from clients JSON POST
	$name = $inData["name"];
	$numStudents = $inData["numStudents"];
	$location = $inData["location"];
	$description = $inData["description"];
	$picturesPath = $inData["picturesPath"];


	//Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner"); 

	//check if entered data is empty.
	if($name == "" || $numStudents == "" || $location == "" || $description =="" || $picturesPath == "")
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
		//Find university with same name
		$stmt = $conn->prepare("SELECT universityID FROM Universities WHERE name=?");
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$result = $stmt->get_result();

		//Check if username is taken
		if( $row = $result->fetch_assoc()  ) 
		{
			//A uni with a matching name was found
			returnWithError( 0, "University already exists");
		}
		else 
		{	
			//Add the new university to the database
			$stmt = $conn->prepare("INSERT INTO Universities (name, numStudents ,location,description,picturesPath) VALUES (?,?,?,?,?)");
			$stmt->bind_param("sisss", $name, $numStudents, $location, $description, $picturesPath);
			$stmt->execute();

			//Get the new universities ID
			$stmt = $conn->prepare("SELECT universityID FROM Universities WHERE name=?");
			$stmt->bind_param("s", $name);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$uniID = $row['universityID'];

			//Return the new universities info
			returnWithInfo($uniID, $name, $numStudents, $location, $description, $picturesPath);
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
	function returnWithInfo($uniID, $name, $numStudents, $location, $description, $picturesPath)
	{
		$retValue = '{"uniID":' . $uniID . ',"name":"' . $name . '","numStudents":"' . $numStudents . '","location":"' . $location . '","description":"' . $description . '","picturesPath":"' . $picturesPath . '","error":""}';
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
