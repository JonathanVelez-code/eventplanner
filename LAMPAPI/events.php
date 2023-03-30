<?php

	$inData = getRequestInfo();
	$userid = $inData["userID"];

    //Connect to mySQL
	$conn = new mysqli("localhost", "AdminUser", "cop4710Data@", "EventPlanner");
    if ($conn->connect_error)
    {
        returnWithError($conn->connect_error);
    }
    else
	{

		//Get users university
		$stmt = $conn->prepare("SELECT university FROM Users WHERE userID=?");
		$stmt->bind_param("i", $userid);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$university = $row['university'];
		$stmt->close();
	

		// get all RSO's user is part of
		$stmt = $conn->prepare("SELECT rsoID FROM Members WHERE userID=?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $club = $stmt->get_result();
		$stmt->close();



        // get events table
        $query = "SELECT * FROM Events";
        $results = $conn->query($query);


		// if 
        if (!$results) {
            die();
        }

        $jsonObj = array();
        echo "<table>";

        while ($row_events = $results->fetch_assoc()) {
            //output a row here

			// output if event is public or same as users uni
			if($row_events['access'] == "public" || $row_events['university'] == $university)
            	returnWithInfo($row_events['name'],$row_events['date'], $row_events['location'], $row_events['description'], $row_events['category'], $row_events['access']);

			// output if user is member of events club
			while ($rso = $club->fetch_assoc()) {

				if($row_events['rsoID'] == $rso['rsoID'])
				 returnWithInfo($row_events['name'],$row_events['date'], $row_events['location'], $row_events['description'], $row_events['category'], $row_events['access']);
			}

            $jsonObj[] = $row_events;
        }

        // holds all info of each event
        $final_res = json_encode($jsonObj);

        echo "</table>";

		//$stmt->close();
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
	function returnWithInfo( $name, $date, $location, $description, $category, $access )
	{
		$retValue = '{"name":"' . $name . '","location":"' . $location . '","access":"' . $access . '","date":"' . $date . '","description":"' . $description . '","category":"' . $category . '","error":""}';
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
