<?php

session_start();
require_once 'connect.php';
// offline
$sql = 'UPDATE user SET online = 0 WHERE id = '.$_SESSION['user_info']['id'];
$dbh->query($sql);
unset($_SESSION['user_info']);
header('Location: ../index.php');

?>