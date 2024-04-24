<?php

$database_host = 'cs.neiu.edu';
$database_user = 'SP24CS4121_haynazarov2';
$database_password = 'haynazarov2618548';
$database_name = 'SP24CS4121_haynazarov2';

$conn = new mysqli($database_host, $database_user, $database_password, $database_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully";
}