<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/database.php";

// Read JSON from React
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No data received."
    ]);
    exit();
}

// Get login credentials
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if ($email == "" || $password == "") {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required."
    ]);
    exit();
}

// Find user
$sql = "SELECT * FROM Users WHERE Email = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo json_encode([
        "success" => false,
        "message" => "User not found."
    ]);
    exit();
}

$user = mysqli_fetch_assoc($result);

// Verify password
if (!password_verify($password, $user["Password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Incorrect password."
    ]);
    exit();
}

// NEW: Check if account is suspended
if (isset($user["Status"]) && $user["Status"] === "Suspended") {
    echo json_encode([
        "success" => false,
        "message" => "Your account has been suspended. Please contact the administrator."
    ]);
    exit();
}


// Login successful
echo json_encode([
    "success" => true,
    "message" => "Login successful.",
    "user" => [
        "id" => $user["UserID"],
        "name" => $user["FullName"],
        "email" => $user["Email"],
        "role" => strtolower($user["Role"]),
        "avatar" => !empty($user["ProfileImage"])
            ? "http://localhost/Soko/" . $user["ProfileImage"]
            : "",
        "profileImage" => $user["ProfileImage"],
        "phone" => $user["Phone"]
    ]
]);
mysqli_close($conn);

?>