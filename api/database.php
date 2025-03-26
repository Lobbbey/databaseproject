<?php
    $host = "localhost";
    $dbName = "EventManagement";
    $userName = "root";
    $pasword = "test";
    $conn = new mysqli($host, $userName, $pasword, $dbName);

    if($conn->connection_error){
        die("Connection failed: " . $conn->connection_error);
    }
?>