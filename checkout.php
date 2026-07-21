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

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No data received."
    ]);
    exit();
}

$buyerID = intval($data["buyerID"] ?? 0);
$productID = intval($data["productID"] ?? 0);
$quantity = intval($data["quantity"] ?? 1);
$paymentMethod = trim($data["paymentMethod"] ?? "Cash");

if ($buyerID == 0 || $productID == 0 || $quantity <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid checkout data."
    ]);
    exit();
}

mysqli_begin_transaction($conn);

try {

    // Get product details
    $sql = "SELECT Price, Quantity, Status
            FROM Products
            WHERE ProductID=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $productID);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        throw new Exception("Product not found.");
    }

    if ($product["Quantity"] < $quantity) {
        throw new Exception("Not enough stock.");
    }

    $price = $product["Price"];
    $total = $price * $quantity;

    // Create Order
    $sql = "INSERT INTO Orders
            (BuyerID, TotalAmount, Status)
            VALUES (?, ?, 'Pending')";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "id", $buyerID, $total);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    $orderID = mysqli_insert_id($conn);

    // Create Order Item
    $sql = "INSERT INTO OrderItems
            (OrderID, ProductID, Quantity, Price)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iiid",
        $orderID,
        $productID,
        $quantity,
        $price
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    // Create Payment
    $sql = "INSERT INTO Payments
            (OrderID, PaymentMethod, Amount, Status)
            VALUES (?, ?, ?, 'Paid')";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "isd",
        $orderID,
        $paymentMethod,
        $total
    );

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    // Update Stock
    $newQuantity = $product["Quantity"] - $quantity;

    if ($newQuantity == 0) {

        $sql = "UPDATE Products
                SET Quantity=0,
                    Status='Sold'
                WHERE ProductID=?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $productID);

    } else {

        $sql = "UPDATE Products
                SET Quantity=?
                WHERE ProductID=?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $newQuantity,
            $productID
        );

    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt));
    }

    mysqli_commit($conn);

    echo json_encode([
        "success" => true,
        "message" => "Checkout completed successfully.",
        "orderID" => $orderID
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

mysqli_close($conn);

?>