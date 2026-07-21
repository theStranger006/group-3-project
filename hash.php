<?php

// Generates a hashed version of the password "admin123"

$password = "admin123";

echo password_hash($password, PASSWORD_DEFAULT);

?>