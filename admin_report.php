<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit();
}

require_once __DIR__ . "/database.php";

// Category stats
$catSql = "
    SELECT c.CategoryName, COUNT(p.ProductID) AS count, COALESCE(SUM(p.Price), 0) AS totalVal
    FROM Categories c
    LEFT JOIN Products p ON c.CategoryID = p.CategoryID
    GROUP BY c.CategoryID, c.CategoryName
    ORDER BY count DESC
";
$catResult = mysqli_query($conn, $catSql);
$categoryStats = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $categoryStats[] = $row;
}

// Transactions
$txSql = "
    SELECT 
        o.OrderID,
        p.ProductName,
        buyer.FullName AS BuyerName,
        seller.FullName AS SellerName,
        o.Status,
        o.TotalAmount,
        o.OrderDate,
        pay.PaymentMethod
    FROM Orders o
    JOIN Products p ON o.OrderID = (
        SELECT oi.OrderID FROM OrderItems oi WHERE oi.OrderID = o.OrderID LIMIT 1
    )
    JOIN OrderItems oi ON o.OrderID = oi.OrderID
    JOIN Users buyer ON o.BuyerID = buyer.UserID
    JOIN Users seller ON p.SellerID = seller.UserID
    LEFT JOIN Payments pay ON o.OrderID = pay.OrderID
    WHERE oi.ProductID = p.ProductID
    GROUP BY o.OrderID
    ORDER BY o.OrderDate DESC
";
$txResult = mysqli_query($conn, $txSql);
$transactions = [];
while ($row = mysqli_fetch_assoc($txResult)) {
    $transactions[] = [
        "id" => (string)$row['OrderID'],
        "productTitle" => $row['ProductName'],
        "buyerName" => $row['BuyerName'],
        "sellerName" => $row['SellerName'],
        "status" => strtolower($row['Status']),
        "amount" => (float)$row['TotalAmount'],
        "date" => $row['OrderDate'],
        "paymentMethod" => $row['PaymentMethod'] ?? 'N/A'
    ];
}

echo json_encode([
    "success" => true,
    "categoryStats" => $categoryStats,
    "transactions" => $transactions
]);

mysqli_close($conn);
?>