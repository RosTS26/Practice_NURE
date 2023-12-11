<?php

session_start();
require_once '../vendor/connect.php';
require_once 'connectAdminDB.php';

if ($_SESSION['user_info']['id'] == 1) {

	$user_id = $_POST['user_id'];
	$user_nickname = $_POST['user_nickname'];
	$days = $_POST['days'];
	$cause = $_POST['cause'];
	if (strlen($cause) < 1) $cause = "ban";

	if ($user_id) {
		$sql = 'SELECT * FROM user WHERE id = :user_id';
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':user_id', $user_id);
	}
	else if ($user_nickname) {
		$sql = 'SELECT * FROM user WHERE username = :username';
		$sth = $dbh->prepare($sql);
		$sth->bindValue(':username', $user_nickname);
	}

	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	if (count($res) > 0) {

		$currentDateTime = new DateTime();
		$banDateTime = new DateTime();
		$banDateTime->modify("$days days");
		$sqlBanDT = $banDateTime->format('Y-m-d H:i:s');
		$sqlcurrentDT = $currentDateTime->format('Y-m-d H:i:s');

		$sqlBan = 'SELECT * FROM users_ban WHERE user_id = '.$res[0]['id'];
		$sth = $admindbh->query($sqlBan);
		$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($checkBan) > 0) {

			foreach ($checkBan as $row) {
				$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

				if ($currentDateTime < $end_date) {
					echo 'User "'.$res[0]['username'].'" is already banned!';
					die();
				}
			}
		}

		$sql = "INSERT INTO users_ban (user_id, username, cause, start_date, end_date) VALUES (:user_id, :username, :cause, :start_date, :end_date)";
		$sth = $admindbh->prepare($sql);
		$sth->bindValue(':user_id', $res[0]['id']);
		$sth->bindValue(':username', $res[0]['username']);
		$sth->bindValue(':cause', $cause);
		$sth->bindValue(':start_date', $sqlcurrentDT);
		$sth->bindValue(':end_date', $sqlBanDT);
		$sth->execute();

		echo 'User "'.$res[0]['username'].'" banned for '.$days.' day(s)!';

	} else echo 'This user does not exist';
}

$admindbh = null;
$dbh = null;

?>