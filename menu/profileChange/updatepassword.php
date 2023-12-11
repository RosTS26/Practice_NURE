<?php

session_start();
require_once '../../vendor/connect.php';

if ($_POST['password'] && $_POST['newPassword'] && $_POST['repeatPassword']) {

	$id = $_SESSION['user_info']['id'];
	$password = md5($_POST['password']);
	$newPassword = md5($_POST['newPassword']);
	$repeatPassword = md5($_POST['repeatPassword']);

	$sql = 'SELECT * FROM user WHERE id = :id AND password = :password';
	$sth = $dbh->prepare($sql);
	$sth->bindValue(':id', $id);
	$sth->bindValue(':password', $password);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($res) > 0) {
		if (strlen($_POST['newPassword']) < 6) {
			echo '<p style="color: #EC5651;">New password is too short!</p>';
		} else if ($newPassword != $repeatPassword) {
			echo '<p style="color: #EC5651;">Password mismatch!</p>';
		} else {

			$sql = 'UPDATE user SET password = :newPassword WHERE id = :id';
			$sth = $dbh->prepare($sql);
			$sth->bindValue(':id', $id);
			$sth->bindValue(':newPassword', $newPassword);
			$sth->execute();
			echo '<p style="color: #50C878;">Your password has been changed!</p>';
		}
	} else {
		echo '<p style="color: #EC5651;">Old password is not correct!</p>';
	}

	$dbh = null;
} else header('Location: ../profile.php');

?>