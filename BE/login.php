<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

require 'db.php'; // Gather dbConnection from db.php

$connectionObject = dbConnection(); // Call the connect db function


function login_user($connectionObject) {
    $name = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($name && $password) {
        $stmt = $connectionObject->prepare("SELECT has_taken_quiz FROM userdetails WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $name, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt = $connectionObject->prepare("UPDATE userdetails SET lastlogin = now() WHERE username = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            
            return json_encode([
                "success" => true,
                "message" => "Login successful.",
                "has_taken_quiz" => (bool)$row['has_taken_quiz']]);
        } else {
            return json_encode(["success" => false, "message" => "Invalid username or password."]);
        }

        $stmt->close();
    } else {
        return json_encode(["success" => false, "message" => "Username and password are required."]);
    }
}

function is_user_logged_in($connectionObject) {
    $name = $_GET['username'] ?? null;
    
    if ($name != null) {
        $stmt = $connectionObject->prepare("SELECT has_taken_quiz FROM userdetails WHERE username = ? AND TIMEDIFF(now(), lastlogin) <= '00:05:00'");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_encode([
                "status" => true,
                "has_taken_quiz" => (bool)$row['has_taken_quiz']
            ]);
        }
    } 
    return json_encode(["status" => false]);
}

$response = json_encode(["status" => false]);
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = is_user_logged_in($connectionObject);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = login_user($connectionObject);
}

echo $response;

$connectionObject->close();
?>