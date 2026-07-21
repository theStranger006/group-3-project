<?php

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once "database.php";

if (!isset($_POST["userID"])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing user ID."
    ]);
    exit();
}

$userID = intval($_POST["userID"]);

if (!isset($_FILES["image"])) {
    echo json_encode([
        "success" => false,
        "message" => "No image uploaded."
    ]);
    exit();
}

$uploadDir = "../uploads/profiles/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$imageName = uniqid("profile_") . "_" . basename($_FILES["image"]["name"]);

$targetFile = $uploadDir . $imageName;

if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
    echo json_encode([
        "success" => false,
        "message" => "Unable to save image."
    ]);
    exit();
}

$imagePath = "uploads/profiles/" . $imageName;

$sql = "UPDATE Users
        SET ProfileImage=?
        WHERE UserID=?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "si", $imagePath, $userID);

if (mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => true,
        "image" => "http://localhost/Soko/" . $imagePath
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => mysqli_error($conn)
    ]);

}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>