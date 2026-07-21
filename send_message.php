<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

require_once "database.php";

$data = json_decode(file_get_contents("php://input"), true);

$senderID = intval($data["senderID"] ?? 0);
$receiverID = intval($data["receiverID"] ?? 0);
$productID = intval($data["productID"] ?? 0);
$message = trim($data["message"] ?? "");



if ($senderID == 0 || $receiverID == 0 || $message == "") {

    echo json_encode([
        "success" => false,
        "senderID" => $senderID,
        "receiverID" => $receiverID,
        "productID" => $productID,
        "messageText" => $message
    ]);

    exit();
}








$sql = "INSERT INTO Messages
(SenderID, ReceiverID, ProductID, Message)
VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "iiis",
    $senderID,
    $receiverID,
    $productID,
    $message
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Message sent."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
}

mysqli_close($conn);

?>