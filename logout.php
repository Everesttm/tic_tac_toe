<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: ttt_login.php");
exit;
