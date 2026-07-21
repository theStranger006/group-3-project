<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

require_once "database.php";

if (!isset($_GET["sellerID"])) {
    echo json_encode([
        "success" => false,
        "message" => "Seller ID is required."
    ]);
    exit();
}

$sellerID = intval($_GET["sellerID"]);

$sql = "
SELECT
    o.OrderID,
    o.OrderDate,
    o.Status,
    o.TotalAmount,

    oi.ProductID,
    oi.Quantity,
    oi.Price,

    p.ProductName,

    u.UserID AS BuyerID,
    u.FullName AS BuyerName,

    pay.PaymentMethod

FROM Orders o

INNER JOIN OrderItems oi
ON o.OrderID = oi.OrderID

INNER JOIN Products p
ON oi.ProductID = p.ProductID

INNER JOIN Users u
ON o.BuyerID = u.UserID

INNER JOIN Payments pay
ON pay.OrderID = o.OrderID

WHERE p.SellerID = ?

ORDER BY o.OrderDate DESC
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $sellerID);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

echo json_encode([
    "success" => true,
    "orders" => $orders
]);

mysqli_close($conn);

?>