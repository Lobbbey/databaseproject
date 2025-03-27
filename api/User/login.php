<?php

    $inData = json_decode(file_get_contents('php://input'), true);
    $Email = $inData["Email"];
    $password = $inData["Password"];

    $conn = new mysqli("localhost", "root", "test", "EventManagement");
    if($conn->connect_error){
        returnWithError("error: Could not connect to database");
    }
    else{
        $stmt = $conn->prepare("SELECT UID, Name, UserType, University_ID FROM Users WHERE Email=? AND Password=?");
        $stmt->bind_param("ss", $Email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()){
            returnWithInfo($row['Name'], $row['UserType'], $row['UID'], $row['University_ID']);
        }
        else{
            returnWithError("error: No records Found");
        }

        $stmt->close();
        $conn->close();
    }


    function returnWithError($err){
        $retVal = '{"result":"' . $err . '"}';
        sendResultInfoAsJson($retVal);
    }

    function sendResultInfoAsJson($obj){
        header('Content-type: application/json');
        echo $obj;
    }

    //Returns JSON object
	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	//Returns JSON error message
	function returnWithError( $err )
	{
		$retValue = '{"id":0, "result":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	//Returns JSON id, first name, last name, success message
	function returnWithInfo( $Name,$uType, $UID, $uniID){
        $retValue = json_encode([
            "Name" => $Name,
            "UID" => $UID,
            "uniID" => $uniID,
            "UserType" => $uType,
            "result" => "Finished Successfully"
        ]);
        sendResultInfoAsJson($retValue);
    }
?>