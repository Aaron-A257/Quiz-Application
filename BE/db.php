<?php
function dbConnection(){
    $servername = "localhost";
    $username = "root"; // Default username
    $password = ""; // Default password (empty)
    $dbname = "user"; // Your database name

    $connectionObject = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($connectionObject->connect_error) {
        die("Connection failed: " . $connectionObject->connect_error);
    }
    return $connectionObject;
    $connectionObject->close();
}
?>