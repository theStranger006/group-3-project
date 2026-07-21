

<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

// Change this to your actual database connection file
require_once __DIR__ . "/database.php";   // Example: database.php, connect.php, etc.

// Read JSON sent from React
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No data received."
    ]);
    exit();
}

$name = trim($data["name"] ?? "");
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";
$role = $data["role"] ?? "";
$location = $data["location"] ?? "";
$bio = trim($data["bio"] ?? "");

// Convert buyer -> Buyer, seller -> Seller
$role = ucfirst(strtolower($role));
error_log("Role received: " . $role);
// Validate required fields
if ($name == "" || $email == "" || $password == "") {
    echo json_encode([
        "success" => false,
        "message" => "Please fill in all required fields."
    ]);
    exit();
}

// Check if email already exists
$sql = "SELECT * FROM users WHERE Email = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "s", $email);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {

    echo json_encode([
        "success" => false,
        "message" => "Email already exists."
    ]);

    exit();
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$sql = "INSERT INTO users
(FullName, Email, Password, Role)
VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die(json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $name,
    $email,
    $hashedPassword,
    $role
);

if (!mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => false,
        "mysql_errno" => mysqli_stmt_errno($stmt),
        "mysql_error" => mysqli_stmt_error($stmt)
    ]);

    exit();
}

$id = mysqli_insert_id($conn);

echo json_encode([
    "success" => true,
    "message" => "Registration successful."
]);










mysqli_close($conn);

?>