<?php
	session_start();
	include '../vendor/connect.php';
	require_once '../admin/connectAdminDB.php';
	require_once '../vendor/checkban.php';
	if (!isset($_SESSION['user_info'])) {
		header('Location: index.php');
		die();
	}

	function sortScore($res) {
	// Сортировка по очкам
		for ($i = 0; $i < count($res) - 1; $i++) {
			$max = $i;
			for ($j = $i+1; $j < count($res); $j++) {
				if ($res[$j]['topScore'] > $res[$max]['topScore']) {
					$max = $j;
				}
			}
			if ($max != $i) {
				$c = $res[$i];
				$res[$i] = $res[$max];
				$res[$max] = $c;
			}
		}
		return $res;
	} 
?>
<!DOCTYPE html>
<html>
<head>
	<title>Rating</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="stylesRating.css">
</head>
<body>
	<div class="menuLog">
		<p>Rating</p>
		<div class="res">
			<?php
				$sql = "SELECT user.id, user.username, snake.topScore, snake.num_of_games FROM user JOIN snake ON user.id = ID_snake";
				$res = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

				$res = sortScore($res);
				
				echo '<table border="1">';
				echo '<caption><b>Top 10 snake players</b></caption>';
				echo '<tr>
					<th>Nickname</th>
					<th>Score</th>
					<th>Games played</th>
				</tr>';
				for ($i = 0; $i < 10; $i++) {
					if ($i == count($res)) break;
					echo '<tr>
						<td>'.$res[$i]['username'].'</td>
						<td>'.$res[$i]['topScore'].'</td>
						<td>'.$res[$i]['num_of_games'].'</td>
					</tr>';
				}
				echo '</table>';

				$sql = "SELECT user.id, user.username, tetris.topScore, tetris.num_of_games FROM user JOIN tetris ON user.id = ID_tetris";
				$res = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

				$res = sortScore($res);
				
				echo '<table border="1">';
				echo '<caption><b>Top 10 tetris players</b></caption>';
				echo '<tr>
					<th>Nickname</th>
					<th>Score</th>
					<th>Games played</th>
				</tr>';
				for ($i = 0; $i < 10; $i++) {
					if ($i == count($res)) break;
					echo '<tr>
						<td>'.$res[$i]['username'].'</td>
						<td>'.$res[$i]['topScore'].'</td>
						<td>'.$res[$i]['num_of_games'].'</td>
					</tr>';
				}
				echo '</table>';
			?>
		</div>
		<form action="../menu.php">
			<input type="submit" value="Back" name="btnBack" class="btn" id="aftBtn">
		</form>
	</div>
</body>
</html>