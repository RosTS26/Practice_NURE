<?php
	session_start();
	require_once 'admin/connectAdminDB.php';
	require_once 'vendor/checkban.php';
	if (!isset($_SESSION['user_info'])) header('Location: index.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>Games</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
	<div class="menuLog">
		<p>Games</p>
		<form action="games/snake/snake.php">
			<input type="submit" value="Snake" name="btnSnake" class="btn">
		</form>
		<form action="games/tetris/tetris.php">
			<input type="submit" value="Tetris" name="btnTetris" class="btn">
		</form>
		<form action="games/roulette/roulette.php">
			<input type="submit" value="Roulette" name="btnRoulette" class="btn">
		</form>
	</div>
</body>
</html>