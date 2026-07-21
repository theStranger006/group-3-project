<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:3000");

require_once "database.php";

$userID = intval($_GET["userID"] ?? 0);

if ($userID <= 0) {
    echo json_encode([
        "success" => false,
        "count" => 0
    ]);
    exit;
}

$sql = "
SELECT COUNT(*) AS unreadCount
FROM Messages
WHERE ReceiverID = ?
AND IsRead = 0
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $userID);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

echo json_encode([
    "success" => true,
    "count" => intval($row["unreadCount"])
]);

mysqli_close($conn);

?>