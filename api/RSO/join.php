<?php

    $inData = json_decode(file_get_contents('php://input'), true);
    $UID = $inData["UID"];
    $RSOID = $inData["RSOID"];

    $conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");

    if ($conn->connect_error) {
        returnWithError("Database connection failed.");
    } else {
        $stmt = $conn->prepare("INSERT IGNORE INTO RSO_Members (UID, RSOID) VALUES (?, ?)");
        $stmt->bind_param("ii", $UID, $RSOID);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            sendResultInfoAsJson('{"result":"Successfully joined RSO."}');
        } else {
            returnWithError("Already a member or RSO does not exist.");
        }

        $stmt->close();
        $conn->close();
    }

    function sendResultInfoAsJson($obj) {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError($err) {
        $retValue = '{"result":"' . $err . '"}';
        sendResultInfoAsJson($retValue);
    }

?>