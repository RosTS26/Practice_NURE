<?php
	session_start();
	require_once '../../admin/connectAdminDB.php';
	require_once '../../vendor/checkban.php';
	if (!isset($_SESSION['user_info'])) header('Location: ../../index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tetris</title>
	<link rel="stylesheet" type="text/css" href="tetrisCSS.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body>
	<script type="text/javascript" src="tetrisScriptJQ.js" defer></script>
	<div class="main"></div>
	<div class="menu">
		<input type="text" id="score" class="score" readonly>
		<input type="button" value="Start" id="btnStart" class="btn">
		<form action="../../menu.php">
			<input type="submit" value="Menu" name="btnMenu" class="btn">
		</form>
		<input id="info" name="accordion_head" type="checkbox">
		<label for="info">Info</label>
		<div class="info_body">
			<h2>Управление</h2>
			<p>Вправо - стрелка вправо</p>
			<p>Влево - стрелка влево</p>
			<p>Повернуть фигуру - стрелка вверх</p>
			<p>Ускорить фигуру - стрелка вниз</p>
		</div>
	</div>
	<div id="nextFig">
		<p>Next figura</p>
		<img src="" id="img">
	</div>
	<div id="msgInfo">Press "Start"!</div>
</body>
</html>