<?php
	session_start();
	require_once '../../admin/connectAdminDB.php';
	require_once '../../vendor/checkban.php';
	if (!isset($_SESSION['user_info'])) header('Location: ../../index.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Snake</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../css/styles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body>
	<script src="snake.js" defer></script>
	<canvas id="game" width="600px" height="600px"></canvas>
	<input type="button" value="Start" id="btnStart" class="btn">
	<form action="../../menu.php">
		<input type="submit" value="Menu" name="btnMenu" class="btn">
	</form>
</body>
</html>