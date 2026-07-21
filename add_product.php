<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/database.php";

// Read form data
$sellerID = $_POST["sellerID"] ?? "";
$categoryID = $_POST["categoryID"] ?? "";
$productName = trim($_POST["productName"] ?? "");
$description = trim($_POST["description"] ?? "");
$location = trim($_POST["location"] ?? "");
$price = $_POST["price"] ?? "";
$quantity = $_POST["quantity"] ?? "";
$productCondition = $_POST["productCondition"] ?? "Used";

// Validate required fields
if (
    empty($sellerID) ||
    empty($categoryID) ||
    empty($productName) ||
    empty($price) ||
    empty($quantity)
) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill in all required fields."
    ]);
    exit();
}

// Default image path
$imageURL = "";

// Handle uploaded image
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {

    $uploadDir = "../uploads/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

    if (!in_array($extension, $allowed)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid image type."
        ]);
        exit();
    }

    $fileName = uniqid("product_") . "." . $extension;

    $destination = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
        $imageURL = "uploads/" . $fileName;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to upload image."
        ]);
        exit();
    }
}

// Insert product
$sql = "INSERT INTO Products
(
    SellerID,
    CategoryID,
    ProductName,
    Description,
    ImageURL,
    Location,
    Price,
    Quantity,
    ProductCondition
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "iissssdss",
    $sellerID,
    $categoryID,
    $productName,
    $description,
    $imageURL,
    $location,
    $price,
    $quantity,
    $productCondition
);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "message" => "Product added successfully.",
        "productID" => mysqli_insert_id($conn),
        "imageURL" => $imageURL
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