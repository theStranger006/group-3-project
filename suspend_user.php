<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "database.php";

$data = json_decode(file_get_contents("php://input"), true);

$userID = $data["userID"] ?? null;
$status = $data["status"] ?? null;

if (!$userID || !$status) {
    echo json_encode([
        "success" => false,
        "message" => "Missing data."
    ]);
    exit;
}

$sql = "UPDATE Users
        SET Status = ?
        WHERE UserID = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "si", $status, $userID);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "User updated successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>