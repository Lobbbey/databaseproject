<?php
    //Variables taken from js file
    $inData = getRequestInfo();
    $Name = $inData["Name"];
    $Email = $inData["Email"];
    $Password = $inData["Password"];
    $Usertype = $inData["Usertype"];

    $conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
    if($conn->connect_error){
        returnWithError("error: Could not connect to database");
    }
    else{
        //Returns if username already exists in database
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Email=?");
        $stmt->bind_param("s", $Email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()){
            returnWithError("error: User Already Exists");
        }
        else{
            //Insert user into database
            $stmt = $conn->prepare("INSERT INTO Users (Name, Email, Password, UserType) Values (?,?,?,?)");
            $stmt->bind_param("ssss", $Name, $Email, $Password, $Usertype);
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