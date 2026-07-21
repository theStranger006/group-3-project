<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

require_once "database.php";

// Read JSON from React
$data = json_decode(file_get_contents("php://input"), true);

$senderID = intval($data["senderID"] ?? 0);
$receiverID = intval($data["receiverID"] ?? 0);

if ($senderID == 0 || $receiverID == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Missing user IDs."
    ]);
    exit();
}

$sql = "SELECT
            MessageID,
            SenderID,
            ReceiverID,
            ProductID,
            Message,
            DateSent,
            IsRead
        FROM Messages
        WHERE
        (SenderID = ? AND ReceiverID = ?)
        OR
        (SenderID = ? AND ReceiverID = ?)
        ORDER BY DateSent ASC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "iiii",
    $senderID,
    $receiverID,
    $receiverID,
    $senderID
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$messages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

echo json_encode([
    "success" => true,
    "messages" => $messages
]);

mysqli_close($conn);

?>