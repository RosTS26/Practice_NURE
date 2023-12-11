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
	<title>Roulette</title>
	<link rel="stylesheet" type="text/css" href="rouletteCSS.css">
	<!--Подключаем библиотеку-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body>
	<script src="rouletteScript.js" defer></script>
	<div class="indexBox">
		<canvas id="game" width="870px" height="630px"></canvas>
		<input type="button" value="Spin" id="btnSpin">
	</div>
	<div class="msgInfo">Load...</div>
</body>
</html>