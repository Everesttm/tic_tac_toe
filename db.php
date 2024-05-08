<?php

$database_host = 'abccc.com';
$database_user = 'lklk';
$database_password = 'mkmk';
$database_name = 'kmkm';

$conn = new mysqli($database_host, $database_user, $database_password, $database_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully";
}
