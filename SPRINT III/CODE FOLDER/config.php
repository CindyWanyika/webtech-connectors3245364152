<?php
$host = "localhost";
$user = "zeinab.hamidou";
$password = "@M@dou2001";
$database = "webtech_2025A_zeinab_hamidou";

$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error){
  die("Connection failed: " .$conn->connect_error);
}
?> 