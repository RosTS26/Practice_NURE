<?php

session_start();
include '../../vendor/connect.php';

if ($_POST['score'] || $_POST['score'] == '0') {
	$score = $_POST['score'];
	$user_id = $_SESSION["user_info"]["id"];

	$sql = 'SELECT topScore, num_of_games FROM tetris WHERE ID_tetris = :user_id';
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':user_id', $_SESSION["user_info"]["id"]);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	$numGames = $res[0]['num_of_games'] + 1;
	if ($score > $res[0]['topScore']) {
		$sql = 'UPDATE tetris SET topScore ='.$score.', num_of_games ='.$numGames.' WHERE ID_tetris ='.$user_id;
		$dbh->exec($sql);
		echo "New record!";
	} else {
		$sql = 'UPDATE tetris SET num_of_games = '.$numGames.' WHERE ID_tetris = '.$user_id;
		$dbh->exec($sql);
	}
	$dbh = null;
} 
else header('Location: tetris.php');

?>