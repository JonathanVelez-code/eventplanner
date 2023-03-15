<?php

    $inData = getRequestInfo();

    //Get fields from clients JSON POST
	$uniName = $inData["name"];

    //Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner");
    if ($conn->connect_error)
    {
        returnWithError($conn->connect_error);
    }
    else
	{
		//Search for university
		$stmt = $conn->prepare("SELECT name,numStudents,location, description, picturesPath FROM Universities WHERE name=?");
		$stmt->bind_param("s", $uniName);
		$stmt->execute();
		$result = $stmt->get_result();	

		//Check if the name exists
		if( $row = $result->fetch_assoc()  )
		{
			//Return the JSON data
			returnWithInfo( $row['name'], $row['numStudents'], $row['location'], $row['description'], $row['picturesPath'] );
		}
		else
		{
			returnWithError("No Records Found");
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
	function returnWithInfo( $name, $numStudents, $location, $description, $picturesPath )
	{
		$retValue = '{"name":' . $name . ',"numStudents":"' . $numStudents . '","location":"' . $location . '","description":"' . $description . '", "picturesPath":"' . $picturesPath . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>