<?php

session_start();
include '../../vendor/connect.php';

if ($_POST['sum']) {
	$sum = $_POST['sum'];
	$user_id = $_SESSION["user_info"]["id"];

	$sql = 'SELECT deposit, num_of_games FROM roulette WHERE ID_roulette = :user_id';
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':user_id', $_SESSION["user_info"]["id"]);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	$numGames = $res[0]['num_of_games'] + 1;
	$deposit = $res[0]['deposit'] + $sum;
	$sql = 'UPDATE roulette SET deposit ='.$deposit.', num_of_games ='.$numGames.' WHERE ID_roulette ='.$user_id;
	$dbh->exec($sql);
	echo $deposit;

	$dbh = null;
} 
else header('Location: roulette.php');

?>