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

$orderID = intval($data["orderID"] ?? 0);

if ($orderID <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid Order ID"
    ]);
    exit();
}

// Update order status
$sql = "UPDATE Orders
        SET Status='Delivered'
        WHERE OrderID=?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $orderID);
mysqli_stmt_execute($stmt);

// Update payment status
$sql2 = "UPDATE Payments
         SET Status='Paid'
         WHERE OrderID=?";

$stmt2 = mysqli_prepare($conn, $sql2);
mysqli_stmt_bind_param($stmt2, "i", $orderID);
mysqli_stmt_execute($stmt2);

echo json_encode([
    "success" => true
]);

mysqli_close($conn);

?>