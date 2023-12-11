<?php
	session_start();
	require_once '../../vendor/connect.php';
	require_once '../../admin/connectAdminDB.php';
	require_once '../../vendor/checkban.php';
	require_once '../../vendor/checkBlockChat.php';

	if (!$_SESSION['user_info']['id']) header('Location: ../../index.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Friends</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="friends.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body>
	<script type="text/javascript" src="scriptFriends.js" defer></script>
	<div class="blockPanelFriend">
		<div class="container">
			<div class="friends-menu">
				<p class="header_P">Friends</p>
				<div class="add-friend">
					<p>Add friend:</p>
					<input type="text" id="friend-name" autocomplete="off" placeholder="enter friend's name">
					<input type="button" class="btn" id="friend-add" value="Add">
				</div>
				<div class="btns-menu">
					<input type="radio" name="option" id="option1" checked="true">
					<label for="option1" class="btn" id="my-friends">My friends</label>
					<input type="radio" name="option" id="option2">
					<label for="option2" class="btn" id="sent-app">Sent app</label>
					<input type="radio" name="option" id="option3">
					<label for="option3" class="btn" id="incoming-app">Incoming app
					</label>
				</div>
			</div>
			<div class="friend-profile">
				<p class="header_P">Friend profile</p>
				<div class="fp-row">
					<div class="information">
						<div id="username">UserName: </div>
						<div id="ID">ID: </div>
					</div>
					<div class="functions-btns"></div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="friends_info">
				<ul class="friends_list">
					<!-- <?php
						$sql = "SELECT friends FROM user_friends WHERE user_id = ".$_SESSION['user_info']['id'];
						$res = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
						$res = json_decode($res['friends'], true);

						foreach($res as $item) {
							echo '<li class="user_item" id="'.$item["id"].'">'.$item["username"].'</li>';
						}
					?> -->
				</ul>
			</div>
			<div class="field_for_message">
				<!-- <div class="chat-container"></div>
				<div class="text-and-btn">
					<textarea id="text-to-send"></textarea>
					<input type="button" class="btn" value="Send" id="btnSendMsg">
				</div> -->
			</div>
		</div>
		<div class="res" id="msgInfo"></div>
	</div>
	<div id="test"></div>
</body>