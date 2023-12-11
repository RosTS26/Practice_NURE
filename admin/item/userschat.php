<?php

session_start();
require_once '../vendor/connect.php';
require_once 'connectAdminDB.php';

if (isset($_POST['sendText']) && isset($_POST['user_id'])) {

	$sendText = $_POST['sendText'];
	$id = $_POST['user_id'];
	$time = date('H:i');

	$data = array(
		'id' => $_SESSION['user_info']['id'],
		'name' => $_SESSION['user_info']['username'],
		'msg' => $sendText,
		'time' => $time
	);

	$sql = 'SELECT * FROM users_chat WHERE user_id = :id';
	$sth = $admindbh->prepare($sql);
	$sth->bindValue(':id', $id);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($res) > 0) {

		$json_chat = json_decode($res[0]['json_chat'], true);
		array_push($json_chat, $data);
		$json_chat = json_encode($json_chat);

		$newMsg = json_decode($res[0]['newMsg'], true);
		array_push($newMsg, $data);
		$newMsg = json_encode($newMsg);
		
		$sql = 'UPDATE users_chat SET json_chat = ?, newMsg = ? WHERE user_id = ?';
		$sth = $admindbh->prepare($sql);
		$sth->execute(array($json_chat, $newMsg, $id));
	} else {

		$json_chat = array();
		array_push($json_chat, $data);
		$json_chat = json_encode($json_chat);

		$sql = 'INSERT INTO users_chat (user_id, username, json_chat, newMsg) VALUES (?, ?, ?, ?)';
		$sth = $admindbh->prepare($sql);
		$sth->execute(array($id, $_SESSION['user_info']['username'], $json_chat, $json_chat));
	}

	echo '<div class="message sent-message">';
	echo '<p class="message-text">'.$sendText.'</p>';
	echo '<span class="message-time">'.$time.'</span></div>';

} else header('Location: ../index.php');

$admindbh = null;
$dbh = null;

?>