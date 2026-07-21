<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

require_once "database.php";

$sellerID = intval($_GET["sellerID"] ?? 0);

if ($sellerID <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid seller ID."
    ]);
    exit();
}

$sql = "
SELECT
    AVG(Rating) AS AverageRating,
    COUNT(*) AS TotalReviews
FROM Reviews
WHERE SellerID = ?
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
$row = mysqli_fetch_assoc($result);

$rating = $row["AverageRating"];
$reviews = $row["TotalReviews"];

echo json_encode([
    "success" => true,
    "rating" => $rating ? round((float)$rating, 1) : 0,
    "reviews" => (int)$reviews
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>