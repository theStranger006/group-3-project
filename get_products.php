<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

require_once __DIR__ . "/database.php";

$sql = "
SELECT
    p.ProductID,
    p.SellerID,
    p.ProductName,
    p.Description,
    p.ImageURL,
    p.Location,
    p.Price,
    p.Quantity,
    p.ProductCondition,
    p.Status,
    p.DatePosted,

    c.CategoryName,

    u.FullName AS SellerName,
    u.ProfileImage AS SellerImage

FROM Products p

INNER JOIN Categories c
    ON p.CategoryID = c.CategoryID

INNER JOIN Users u
    ON p.SellerID = u.UserID

ORDER BY p.DatePosted DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit();
}

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode([
    "success" => true,
    "products" => $products
]);

mysqli_close($conn);

?>