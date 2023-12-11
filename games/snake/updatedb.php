<?php

session_start();
include '../../vendor/connect.php';

if ($_POST['score'] || $_POST['score'] == '0') {
	$score = $_POST['score'];
	$user_id = $_SESSION["user_info"]["id"];

	$sql = 'SELECT topScore, num_of_games FROM snake WHERE ID_snake = :user_id';
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':user_id', $_SESSION["user_info"]["id"]);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	$numGames = $res[0]['num_of_games'] + 1;
	if ($score > $res[0]['topScore']) {
		$sql = 'UPDATE snake SET topScore = ?, num_of_games = ? WHERE ID_snake = ?';
		$sth = $dbh->prepare($sql);
		$sth->execute(array($score, $numGames, $user_id));
		echo "New record!";
	} else {
		$sql = 'UPDATE snake SET num_of_games = '.$numGames.' WHERE ID_snake = '.$user_id;
		$dbh->exec($sql);
	}
	$dbh = null;
} 
else header('Location: snake.php');

?>