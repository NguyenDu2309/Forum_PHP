<?php
$password = "admin123"; // The password you want to hash
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>

