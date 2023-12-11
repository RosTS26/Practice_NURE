<?php

session_start();
require_once 'connect.php';
require_once '../admin/connectAdminDB.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$sql = "SELECT * FROM user WHERE username = :username AND password = :password";
$sth = $dbh->prepare($sql);
$sth->bindValue(':username', $username);
$sth->bindValue(':password', $password);
$sth->execute();
$res = $sth->fetchAll(PDO::FETCH_ASSOC);

if (count($res) > 0) {

	$sqlBan = 'SELECT * FROM users_ban WHERE user_id = '.$res[0]['id'];
	$sth = $admindbh->query($sqlBan);
	$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($checkBan) > 0) {
		$currentDateTime = new DateTime();

		foreach ($checkBan as $row) {
			// Преобразование даты и времени из базы данных в объект DateTime
			$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

			if ($currentDateTime < $end_date) {
				$cause = $row['cause'];
				$_SESSION['msgError'] = 'Ваш аккаун заблокирован до<br>'.$end_date->format('Y-m-d H:i:s').'<br>Причина: '.$cause;
				header('Location: ../index.php');
				die();
			}
		}
	}

	// Инициализация сессии
	$_SESSION['user_info'] = [
		"id" => $res[0]['id'],
		"username" => $res[0]['username']
	];

	// online
	$sql = 'UPDATE user SET online = 1 WHERE id = '.$_SESSION['user_info']['id'];
	$dbh->query($sql);

	header('Location: ../menu.php');

} else {
	$_SESSION['msgError'] = 'Неверный логин или пароль!';
	header('Location: ../index.php');
}

?>