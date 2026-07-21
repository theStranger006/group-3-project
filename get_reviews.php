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
        "message" => "Invalid seller ID.",
        "reviews" => []
    ]);
    exit;
}

$sql = "
SELECT
    Reviews.ReviewID,
    Reviews.Rating,
    Reviews.Comment,
    Reviews.ReviewDate,
    Users.FullName,
    Users.ProfileImage
FROM Reviews
INNER JOIN Users
    ON Reviews.BuyerID = Users.UserID
WHERE Reviews.SellerID = ?
ORDER BY Reviews.ReviewDate DESC
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $sellerID);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$reviews = [];

while ($row = mysqli_fetch_assoc($result)) {

    $avatar = "";

    if (!empty($row["ProfileImage"])) {
        $avatar = "http://localhost/Soko/API/" . $row["ProfileImage"];
    }

    $reviews[] = [
        "reviewID" => (int)$row["ReviewID"],
        "buyerName" => $row["FullName"],
        "rating" => (int)$row["Rating"],
        "comment" => $row["Comment"],
        "date" => $row["ReviewDate"],
        "avatar" => $avatar
    ];
}

echo json_encode([
    "success" => true,
    "reviews" => $reviews
]);

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>