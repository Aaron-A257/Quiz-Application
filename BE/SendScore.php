<?php
header("Access-Control-Allow-Origin: http://localhost:5500"); // Allow requests from this domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow GET, POST, and OPTIONS requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow specific headers like Content-Type

require 'db.php'; // Gather dbConnection from db.php

$connectionObject = dbConnection(); // Call the connect db function

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['username'] ?? null;
    $score = $_POST['score'] ?? null;
    
    if ($name && $score) {
        $stmt = $connectionObject->prepare("UPDATE userdetails SET score = ?, has_taken_quiz = TRUE WHERE username = ?");
        $stmt->bind_param("is", $score, $name);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Score submitted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to submit score."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Username and score are required."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only POST requests are allowed."]);
}


$connectionObject->close();
?>