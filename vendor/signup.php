<?php

session_start();
require_once 'connect.php';

$username = $_POST['username'];
$password = md5($_POST['password']);
$password_confirm = md5($_POST['password_confirm']);

$sql = "SELECT * FROM user WHERE username = :username";
$sth = $dbh->prepare($sql);
$sth->bindValue(':username', $username);
$sth->execute();
$res = $sth->fetchAll(PDO::FETCH_ASSOC);

if (count($res)) {
	$_SESSION['msgError'] = 'Пользователь с таким именем уже существует!';
	header('Location: ../regist.php');
	die();
} else {
	if (strlen($username) < 3) {
		$_SESSION['msgError'] = 'Имя слишком короткое!';
		header('Location: ../regist.php');
		die();
	}

	else if (strlen($_POST['password']) < 6) {
		$_SESSION['msgError'] = 'Пароль слишком короткий!';
		header('Location: ../regist.php');
		die();
	}

	else if ($password === $password_confirm) {
		// БД пользователя
		$sql = "INSERT INTO user (`id`, `username`, `password`) VALUES (NULL, ?, ?)";

		$sth = $dbh->prepare($sql);
		$sth->execute(array($username, $password));

		// Получаем id созданного пользователя
		$user_id = ($dbh->lastInsertId());

		// Создания списка друзей
		$json = json_encode(array());
		$dbh->query("INSERT INTO `user_friends`(`user_id`, `friends`, `sent_app`, `incoming_app`) VALUES ('$user_id','$json','$json','$json')");

		// Создание и привязка БД для игр
		$dbh->query("INSERT INTO snake (`ID_snake`, `topScore`, `num_of_games`) VALUES ('$user_id', 0, 0)");
		$dbh->query("INSERT INTO tetris (`ID_tetris`, `topScore`, `num_of_games`) VALUES ('$user_id', 0, 0)");
		$dbh->query("INSERT INTO roulette (`ID_roulette`, `num_of_games`) VALUES ('$user_id', 0)");

		$_SESSION['message'] = 'Регистрация прошла успешно!';
		header('Location: ../index.php');
	} else {
		$_SESSION['msgError'] = 'Пароли не совпадают!';
		header('Location: ../regist.php');
	}
}
$dbh = null;

?>