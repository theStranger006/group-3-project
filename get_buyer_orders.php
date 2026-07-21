<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/database.php";

if (!isset($_GET["buyerID"])) {
    echo json_encode([
        "success" => false,
        "message" => "Buyer ID is required."
    ]);
    exit();
}

$buyerID = intval($_GET["buyerID"]);

$sql = "
SELECT

    o.OrderID,
    o.BuyerID,
    o.OrderDate,
    o.TotalAmount,
    o.Status AS OrderStatus,

    oi.OrderItemID,
    oi.ProductID,
    oi.Quantity,
    oi.Price,

    p.ProductName,
    p.ImageURL,
    p.SellerID,

    u.FullName AS SellerName,

    pay.PaymentMethod,
    pay.Status AS PaymentStatus

FROM Orders o

INNER JOIN OrderItems oi
    ON o.OrderID = oi.OrderID

INNER JOIN Products p
    ON oi.ProductID = p.ProductID

INNER JOIN Users u
    ON p.SellerID = u.UserID

LEFT JOIN Payments pay
    ON o.OrderID = pay.OrderID

WHERE o.BuyerID = ?

ORDER BY o.OrderDate DESC
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $buyerID);

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

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>