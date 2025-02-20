<?php
    //Variables taken from js file
    $inData = getRequestInfo();
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $userName = $inData["userName"];
    $password = $inData["password"];

    //FIX THIS WITH ACCURATE DATA
    $conn = new mysqli("localhost", "root", "test", "");
    if($conn->connect_error){
        returnWithError("error: Could not connect to database");
    }
    else{
        //Returns if username already exists in database
        $stmt = $conn->prepare("SLECET * FROM Users WHERE Username=?");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()){
            returnWithError("error: User Already Exists");
        }
        else{
            //Insert user into database
            $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Username, Password) Values (?,?,?,?)");
            $stmt->bind_param("ssss", $firstName, $lastName, $userName, $password);
            $stmt->execute();
            sendResultInfoAsJson('{"result":"Finished Successfully"}');
        }
        $stmt->close();
        $conn->close();
    }

    //Gets input from file in key-value pairs
    function getRequestInfo(){
        return json_decode(file_get_contents('php://input'), true);
    }

    //Returns JSON as error message
    function returnWithError($err){
        $retvalue = '{"result":"' . $err . '"}';
        sendResultInfoAsJson($retvalue);
    }
    
    //Returns JSON Object
    function sendResultInfoAsJson($obj){
        header('Content-type: application/json');
        echo $obj;
    }
?>