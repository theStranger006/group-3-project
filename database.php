<?php

$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "soko";

$conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);

if (!$conn) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed: " . mysqli_connect_error()
    ]));
}