<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

require_once "database.php";

// Check seller ID
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
    p.ProductID,
    p.SellerID,
    p.ProductName,
    p.Description,
    p.Price,
    p.Quantity,
    p.ProductCondition,
    p.Status,
    p.DatePosted,
    p.Location,
    p.ImageURL,
    c.CategoryName,
    u.FullName AS SellerName,
     u.ProfileImage AS SellerImage
    

FROM Products p
INNER JOIN Categories c
    ON p.CategoryID = c.CategoryID
INNER JOIN Users u
    ON p.SellerID = u.UserID
WHERE p.SellerID = ?
ORDER BY p.DatePosted DESC
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $sellerID);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

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