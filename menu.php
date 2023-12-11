<?php
	session_start();
	require_once 'admin/connectAdminDB.php';
	require_once 'vendor/checkban.php';

	if (!isset($_SESSION['user_info'])) {
		header('Location: index.php');
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Menu</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
	<div class="menuLog">
		<p>Menu</p>
		<form action="games.php">
			<input type="submit" value="Game" name="btnGame" class="btn">
		</form>
		<form action="menu/profile.php">
			<input type="submit" value="Profile" name="btnProfile" class="btn">
		</form>
		<form action="menu/friends/friends.php">
			<input type="submit" value="Friends" name="btnFriends" class="btn">
		</form>
		<form action="menu/rating.php">
			<input type="submit" value="Rating" name="btnRating" class="btn">
		</form>
		<form action="vendor/logout.php">
			<input type="submit" value="Exit" name="btnExit" class="btn">
		</form>
	</div>
	<?php
		if ($_SESSION['user_info']['id'] == 1) {
			echo '<div class=admin><form action="admin/adminpanel.php">';
			echo '<input type="submit" value="Admin panel"name="btnAdmin" class="btn"></form></div>';
		}
	?>
</body>
</html>