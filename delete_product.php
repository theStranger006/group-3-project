<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

require_once "database.php";

// Read JSON
$data = json_decode(file_get_contents("php://input"), true);

$productID = intval($data["productID"] ?? 0);

if ($productID == 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid Product ID."
    ]);
    exit();
}

// Delete product
$sql = "DELETE FROM Products WHERE ProductID = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $productID);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Product deleted successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_stmt_error($stmt)
    ]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>