<?php

session_start();
include '../../vendor/connect.php';

$sql = 'SELECT deposit FROM roulette WHERE ID_roulette = :user_id';
$sth = $dbh->prepare($sql);
$sth->bindValue(':user_id', $_SESSION["user_info"]["id"]);
$sth->execute();
$res = $sth->fetchAll(PDO::FETCH_ASSOC);
echo $res[0]['deposit'];

?>