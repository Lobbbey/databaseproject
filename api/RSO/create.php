<?php
    $inData = json_decode(file_get_contents('php://input'), true);
    $Name = $inData["Name"];
    $University_ID = $inData["University_ID"];
    $Description = $inData["Description"];

    $conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
    if($conn->conection_error){
        returnWithError("error: Could not connect to database");
    }
    else{
        $stmt = $conn->prepare("INSERT INTO RSO (Name, University_ID, Description) Values (?,?,?)");
        $stmt->bind_param("sis", $Name, $University_ID, $Description);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            sendResultInfoAsJson('{"result":"RSO created successfully."}');
        } 
        else {
            returnWithError("Failed to create RSO.");
        }

        $stmt->close();
        $conn->close();
    }

    // Return JSON result
    function sendResultInfoAsJson($obj) {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err) {
        $retValue = '{"result":"' . $err . '"}';
        sendResultInfoAsJson($retValue);
    }
        
?>