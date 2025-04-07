<?php

    $inData = json_decode(file_get_contents('php://input'), true);
    $Email = $inData["Email"];

    $conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
    if($conn->conection_error){
        returnWithError("error: Could not connect to database");
    }
    else{
        $stmt = $conn->prepare("UPDATE Users SET UserType = 'Admin' WHERE Email = ?");
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            sendResultInfoAsJson('{"result":"User promoted to Admin successfully."}');
        } else {
            returnWithError("No user found with that email or already Admin.");
        }

        $stmt->close();
        $conn->close();
    }


    // Functions for JSON output
    function sendResultInfoAsJson($obj) {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err) {
        $retValue = '{"result":"' . $err . '"}';
        sendResultInfoAsJson($retValue);
    }
?>