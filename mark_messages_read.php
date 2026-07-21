<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "database.php";

$data = json_decode(file_get_contents("php://input"), true);

$receiverID = intval($data["receiverID"] ?? 0);
$senderID   = intval($data["senderID"] ?? 0);

if ($receiverID <= 0 || $senderID <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid IDs"
    ]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE Messages
     SET IsRead = 1
     WHERE ReceiverID = ?
       AND SenderID = ?
       AND IsRead = 0"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $receiverID,
    $senderID
);

// if (mysqli_stmt_execute($stmt)) {

//     echo json_encode([
//         "success" => true
//     ]);

// } 
if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "rowsUpdated" => mysqli_stmt_affected_rows($stmt),
        "receiverID" => $receiverID,
        "senderID" => $senderID
    ]);

}






else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

}

mysqli_close($conn);

?>