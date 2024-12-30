<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500"); // Allow requests from this domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Allow GET, POST, and OPTIONS requests
header("Access-Control-Allow-Headers: Content-Type"); // Allow specific headers like Content-Type

$servername = "localhost";
$username = "root"; // Default username
$password = ""; // Default password (empty)
$dbname = "testdb"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($name && $password) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $name, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["success" => true, "message" => "Login successful."]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid username or password."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Username and password are required."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only POST requests are allowed."]);
}

$conn->close();
?>
