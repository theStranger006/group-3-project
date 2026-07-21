<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");

require_once "database.php";

$response = [];

/* Users */
$sql = "SELECT COUNT(*) AS TotalUsers FROM Users";
$result = mysqli_query($conn, $sql);
$response["totalUsers"] = mysqli_fetch_assoc($result)["TotalUsers"];

/* Products */
$sql = "SELECT COUNT(*) AS TotalProducts FROM Products";
$result = mysqli_query($conn, $sql);
$response["totalProducts"] = mysqli_fetch_assoc($result)["TotalProducts"];

/* Available Products - FIXED: Active instead of Available */
$sql = "SELECT COUNT(*) AS AvailableProducts
        FROM Products
        WHERE Status = 'Active'";
$result = mysqli_query($conn, $sql);
$response["availableProducts"] = mysqli_fetch_assoc($result)["AvailableProducts"];

/* Orders */
$sql = "SELECT COUNT(*) AS TotalOrders
        FROM Orders";
$result = mysqli_query($conn, $sql);
$response["totalOrders"] = mysqli_fetch_assoc($result)["TotalOrders"];

/* Completed Orders */
$sql = "SELECT COUNT(*) AS CompletedOrders
        FROM Orders
        WHERE Status = 'Completed'";
$result = mysqli_query($conn, $sql);
$response["completedOrders"] = mysqli_fetch_assoc($result)["CompletedOrders"];

/* Sales */
$sql = "SELECT IFNULL(SUM(TotalAmount), 0) AS TotalSales
        FROM Orders
        WHERE Status = 'Completed'";
$result = mysqli_query($conn, $sql);
$response["totalSales"] = mysqli_fetch_assoc($result)["TotalSales"];

/* Pending Payments */
$sql = "SELECT COUNT(*) AS PendingPayments
        FROM Payments
        WHERE Status = 'Pending'";
$result = mysqli_query($conn, $sql);
$response["pendingPayments"] = mysqli_fetch_assoc($result)["PendingPayments"];

echo json_encode([
    "success" => true,
    "stats" => $response
]);

mysqli_close($conn);