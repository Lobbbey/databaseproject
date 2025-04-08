<?php
    //Variables taken from js file
    $inData = getRequestInfo();
    $Name = $inData["Name"];
    $Email = $inData["Email"];
    $Password = $inData["Password"];
    $Usertype = $inData["UserType"];
    $University = $inData["University"];

    $conn = new mysqli("localhost", "APIUser", "Password", "EventManagement");
    if($conn->connect_error){
        returnWithError("error: Could not connect to database");
    }
    else{
        $uniStmt = $conn->prepare("SELECT University_ID FROM University WHERE Name = ?");
        $uniStmt->bind_param("s", $UniversityName);
        $uniStmt->execute();
        $uniResult = $uniStmt->get_result();
        
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
            $University_ID = $uniResult->fetch_assoc()["University_ID"];
            $stmt = $conn->prepare("INSERT INTO Users (Name, Email, Password, UserType, University_ID) Values (?,?,?,?,?)");
            $stmt->bind_param("ssssi", $Name, $Email, $Password, $Usertype, $University_ID);
            $stmt->execute();
            sendResultInfoAsJson('{"result":"Finished Successfully"}');
        }
        $stmt->close();
        $uniStmt->close();
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