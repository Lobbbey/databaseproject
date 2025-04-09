<?php
    // Variables taken from js file
    $inData = getRequestInfo();
    $Name = $inData["Name"];
    $Email = $inData["Email"];
    $Password = $inData["Password"];
    $Usertype = $inData["UserType"];
    $University = $inData["University"];

    $conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
    if ($conn->connect_error) {
        returnWithError("error: Could not connect to database");
    } else {
        // Translate the University name into its University ID
        $uniStmt = $conn->prepare("SELECT University_ID FROM University WHERE Name = ?");
        $uniStmt->bind_param("s", $University);
        $uniStmt->execute();
        $uniResult = $uniStmt->get_result();
        
        if ($uniResult->num_rows === 0) {
            returnWithError("error: University not found");
            $uniStmt->close();
            $conn->close();
            exit();
        }

        $University_ID = $uniResult->fetch_assoc()["University_ID"];
        $uniStmt->close();

        // Check if the user already exists
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Email=?");
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc()) {
            returnWithError("error: User Already Exists");
        } else {
            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO Users (Name, Email, Password, UserType, University_ID) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $Name, $Email, $Password, $Usertype, $University_ID);
            $stmt->execute();

            // Fetch the newly created user's UID
            $UID = $stmt->insert_id;

            // Return the required data
            $response = [
                "UID" => $UID,
                "Name" => $Name,
                "UserType" => $Usertype,
                "University_ID" => $University_ID
            ];
            sendResultInfoAsJson(json_encode($response));
        }

        $stmt->close();
        $conn->close();
    }

    // Gets input from file in key-value pairs
    function getRequestInfo() {
        return json_decode(file_get_contents('php://input'), true);
    }

    // Returns JSON as error message
    function returnWithError($err) {
        $retvalue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson($retvalue);
    }
    
    // Returns JSON Object
    function sendResultInfoAsJson($obj) {
        header('Content-type: application/json');
        echo $obj;
    }
?>