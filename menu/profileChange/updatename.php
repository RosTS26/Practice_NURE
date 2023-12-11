<?php

session_start();
require_once '../../vendor/connect.php';

if ($_POST['newName']) {

	$id = $_SESSION['user_info']['id'];
	$newName = $_POST['newName'];

	$sql = 'SELECT * FROM user WHERE username = :newName';
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':newName', $newName);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($res) > 0) {
		echo '<p style="color: #EC5651;">This name is taken!</p>';
	} else {
		$sql = 'UPDATE user SET username = :newName WHERE id = :id';
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':id', $id);
		$sth->bindValue(':newName', $newName);
		$sth->execute();
		echo '<p style="color: #50C878;">Your new name: '.$newName.'!</p>';
		$_SESSION['user_info']['username'] = $newName;
	}

	$dbh = null;
} else header('Location: ../profile.php');

?>