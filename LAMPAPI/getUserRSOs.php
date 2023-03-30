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
	
		// get all RSO id's user is part of
		$stmt = $conn->prepare("SELECT rsoID FROM Members WHERE userID=?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $club = $stmt->get_result();
		$stmt->close();

        //get RSO table
        $query = "SELECT * FROM RSO";
        $results = $conn->query($query);

        if (!$results) {
            die();
        }

        $jsonObj = array();
        
        // loops through every rso user is part of
        while ($rso = $club->fetch_assoc()) {

            //echo $rso['rsoID'];
       
            //Find RSOs with the same id
            $stmt = $conn->prepare("SELECT rsoID, name FROM RSO WHERE rsoID=?");
            $stmt->bind_param("i", $rso['rsoID']);
            $stmt->execute();
            $result = $stmt->get_result();

            // if match, output
            if ($row = $result->fetch_assoc()) {

                returnWithInfo($row['name'],$row['rsoID']);
                $jsonObj[] = $row;
            }
        }

        $jsonObj = array();

        //holds all info of each event
        $final_res = json_encode($jsonObj);

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
	function returnWithInfo( $name, $rsoID)
	{
		$retValue = '{"name":"' . $name . '","rsoID":"' . $rsoID .  '","error":""}';
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
