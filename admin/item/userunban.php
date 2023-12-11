<?php

session_start();
require_once '../vendor/connect.php';
require_once 'connectAdminDB.php';

if ($_SESSION['user_info']['id'] == 1) {

	if (isset($_POST['user_id'])) {
		$sql = 'SELECT * FROM user WHERE id = :user_id';
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':user_id', $_POST['user_id']);
	}
	else if (isset($_POST['user_nickname'])) {
		$sql = 'SELECT * FROM user WHERE username = :username';
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':username', $_POST['user_nickname']);
	}

	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	if (count($res) > 0) {

		$currentDateTime = new DateTime();
		$sqlcurrentDT = $currentDateTime->format('Y-m-d H:i:s');

		$sqlBan = 'SELECT * FROM users_ban WHERE user_id = '.$res[0]['id'];
		$sth = $admindbh->query($sqlBan);
		$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($checkBan) > 0) {

			foreach ($checkBan as $row) {
				$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

				if ($currentDateTime < $end_date) {
					$sql = 'UPDATE users_ban SET end_date = :end_date WHERE user_id = :id';
					$sth = $admindbh->prepare($sql);
					$sth->bindValue(':end_date', $row['start_date']);
					$sth->bindValue(':id', $row['user_id']);
					$sth->execute();
					echo 'User "'.$res[0]['username'].'" unbanned!';
					die();
				}
			}
		}
		echo 'User "'.$res[0]['username'].'" is not banned!';
	} else {
		echo 'This user does not exist!';
	}
} else header('Location: ../index.php');

$admindbh = null;
$dbh = null;

?>