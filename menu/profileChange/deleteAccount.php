<?php

session_start();
require_once '../../vendor/connect.php';

if ($_POST['password']) {

	$id = $_SESSION['user_info']['id'];
	$password = md5($_POST['password']);

	$sql = 'SELECT * FROM user WHERE id = :id AND password = :password';
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':id', $id);
	$sth->bindValue(':password', $password);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($res) > 0) {
		$delPass = $password.'remove';

		$sql = 'UPDATE user SET password = :password WHERE id =:id';
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':id', $id);
		$sth->bindValue(':password', $delPass);
		$sth->execute();
		
		unset($_SESSION['user_info']);
	} else {
		echo '<p style="color: #EC5651;">Password is not correct!</p>';
	}
} else header('Location: ../profile.php');

$dbh = null;

?>