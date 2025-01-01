<?php
function dbConnection(){
    $connectionObject =  mysqli_connect("localhost", "root", "");

    // Check connection
    if (!$connectionObject) {
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_query($connectionObject, "
        CREATE DATABASE IF NOT EXISTS user
    ");

    mysqli_query($connectionObject, "
        USE user
     ");

     mysqli_query($connectionObject, "
        CREATE TABLE IF NOT EXISTS userdetails (
        username VARCHAR(30) NOT NULL PRIMARY KEY,
        password VARCHAR(30) NOT NULL,
        score INT(6) DEFAULT 0,
        has_taken_quiz BOOLEAN DEFAULT FALSE,
        lastlogin TIMESTAMP)
     ");
    
    
        
    return $connectionObject;
}
?>