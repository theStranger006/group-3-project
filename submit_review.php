<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "database.php";

$data = json_decode(file_get_contents("php://input"), true);

$productID = intval($data["productID"] ?? 0);
$buyerID   = intval($data["buyerID"] ?? 0);
$rating    = intval($data["rating"] ?? 0);
$comment   = trim($data["comment"] ?? "");

if (
    $productID <= 0 ||
    $buyerID <= 0 ||
    $rating < 1 ||
    $rating > 5
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid review data."
    ]);
    exit;
}

/*
Prevent duplicate reviews
*/
$check = mysqli_prepare(
    $conn,
    "SELECT ReviewID
     FROM Reviews
     WHERE ProductID = ?
     AND BuyerID = ?"
);

mysqli_stmt_bind_param($check, "ii", $productID, $buyerID);
mysqli_stmt_execute($check);
$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0) {

    echo json_encode([
        "success" => false,
        "message" => "You have already reviewed this product."
    ]);

    exit;
}

// Get the seller ID from the product
$getSeller = mysqli_prepare(
    $conn,
    "SELECT SellerID
     FROM Products
     WHERE ProductID = ?"
);

mysqli_stmt_bind_param($getSeller, "i", $productID);
mysqli_stmt_execute($getSeller);

$sellerResult = mysqli_stmt_get_result($getSeller);

if (mysqli_num_rows($sellerResult) == 0) {

    echo json_encode([
        "success" => false,
        "message" => "Product not found."
    ]);

    exit;
}

$sellerRow = mysqli_fetch_assoc($sellerResult);
$sellerID = $sellerRow["SellerID"];

// Save the review
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO Reviews
    (ProductID, BuyerID, SellerID, Rating, Comment)
    VALUES (?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "iiiis",
    $productID,
    $buyerID,
    $sellerID,
    $rating,
    $comment
);




if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Review submitted."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
}

mysqli_close($conn);

?>