<?php
require 'db.php'; // Gather dbConnection from db.php

header("Access-Control-Allow-Origin: * "); // Allow requests from this domain
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Allow GET, POST, and OPTIONS requests
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers like Content-Type

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight request
    http_response_code(200);
    exit();
}

$connectionObject = dbConnection(); // Call the connect db function

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($name && $password) {
        $stmt = $connectionObject->prepare("INSERT INTO userdetails (username, password, lastlogin ) VALUES (?,?,now())");
        $stmt->bind_param("ss", $name, $password);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Registration successful."]);
        } else {
            echo json_encode(["success" => false, "message" => "Registration failed."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Username and password are required."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only POST requests are allowed."]);
}

$connectionObject->close();
?>
