<?php

header("Content-Type: application/json");

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");


if($_SERVER["REQUEST_METHOD"] === "OPTIONS"){
    http_response_code(200);
    exit();
}


require_once "database.php";


$data = json_decode(file_get_contents("php://input"), true);


if(!$data){

    echo json_encode([
        "success"=>false,
        "message"=>"No data received"
    ]);

    exit();

}


// Receiving data from React

$productID = $data["ProductID"];
$sellerID = $data["SellerID"];

$categoryID = $data["CategoryID"];
$productName = $data["ProductName"];
$description = $data["Description"];
$imageURL = $data["ImageURL"];
$location = $data["Location"];
$price = $data["Price"];
$quantity = $data["Quantity"];
$productCondition = $data["ProductCondition"];
$status = $data["Status"];



$sql = "UPDATE Products SET

        CategoryID=?,
        ProductName=?,
        Description=?,
        ImageURL=?,
        Location=?,
        Price=?,
        Quantity=?,
        ProductCondition=?,
        Status=?

        WHERE ProductID=?
        AND SellerID=?";



$stmt = mysqli_prepare($conn,$sql);



mysqli_stmt_bind_param(
    $stmt,
    "issssdissii",
    $categoryID,
    $productName,
    $description,
    $imageURL,
    $location,
    $price,
    $quantity,
    $productCondition,
    $status,
    $productID,
    $sellerID
);



if(mysqli_stmt_execute($stmt)){


    if(mysqli_stmt_affected_rows($stmt) > 0){

        echo json_encode([
            "success"=>true,
            "message"=>"Product updated successfully"
        ]);

    }
    else{

        echo json_encode([
            "success"=>false,
            "message"=>"Product not found or seller does not own this product"
        ]);

    }


}
else{

    echo json_encode([
        "success"=>false,
        "message"=>"Database error"
    ]);

}



mysqli_stmt_close($stmt);
mysqli_close($conn);

?>