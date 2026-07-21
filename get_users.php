<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "database.php";

// Get all users
$sql = "SELECT
            UserID,
            FullName,
            Email,
            Phone,
            Role,
            Status,
            CreatedAt,
            ProfileImage
        FROM Users
        ORDER BY UserID DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit;
}

$users = [];

while ($row = mysqli_fetch_assoc($result)) {

    $users[] = [
        "id" => (string)$row["UserID"],
        "name" => $row["FullName"],
        "email" => $row["Email"],
        "phone" => $row["Phone"],
        "role" => strtolower($row["Role"]),
        "status" => $row["Status"],
        "joinedDate" => $row["CreatedAt"],

        "avatar" => !empty($row["ProfileImage"])
            ? "http://localhost/Soko/" . $row["ProfileImage"]
            : "https://ui-avatars.com/api/?name=" . urlencode($row["FullName"]),

        "profileImage" => $row["ProfileImage"]
    ];
}

echo json_encode([
    "success" => true,
    "users" => $users
]);

mysqli_close($conn);

?>