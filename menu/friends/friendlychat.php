<?php

session_start();
require_once '../../vendor/connect.php';
require_once '../../admin/connectAdminDB.php';
require_once '../../vendor/checkBlockChat.php';

class FriendlyChat {
	private $dbh;
	private $id;
	private $username;
	private $checkBlock;
	private $blockInfo;

	public function __construct ($dbh, $id, $username, $checkBlock, $blockInfo) {
		$this->dbh = $dbh;
		$this->id = $id;
		$this->username = $username;
		$this->checkBlock = $checkBlock;
		$this->blockInfo = $blockInfo;
	}

	public function getBlockInfo() {
		echo $this->blockInfo;
	}

	// === Метод для возвращения переписки с другом ===
	public function getChat($friend_id) {

		// Проверка на блокировку чата
		if ($this->checkBlock == true) die('1');
		// if ($this->checkBlock == true) die('<div class="chat-info blockChat">'.$this->blockInfo.'</div>');

		$sql = 'SELECT * FROM friendly_chat WHERE user_id = ? AND friend_id = ?';
		$sth = $this->dbh->prepare($sql);
		$sth->execute(array($this->id, $friend_id));
		$res = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)) {

			// Отправляем сохраненный чат в виде JSON
			$json_chat = json_decode($res[0]['chat'], true);
			echo json_encode($json_chat);

			// $newMsg = json_decode($res[0]['newMsg'], true);

			// if (count($json_chat) || count($newMsg)) {
			// 	foreach ($json_chat as $item) {
			// 		if ($item['id'] == $this->id) {
			// 			echo '<div class="message sent-message">';
			// 		} else {
			// 			echo '<div class="message received-message">';
			// 		}
			// 		echo '<p class="message-text">'.$item['msg'].'</p>';
			// 		echo '<span class="message-time">'.$item['time'].'</span></div>';
			// 	}

			// 	if (count($newMsg)) {

			// 		$json_chat = array_merge($json_chat, $newMsg);

			// 		echo '<div class="newMsgInfo">New message</div>';
			// 		foreach ($newMsg as $item) {
			// 			echo '<div class="message received-message">';
			// 			echo '<p class="message-text">'.$item['msg'].'</p>';
			// 			echo '<span class="message-time">'.$item['time'].'</span></div>';
			// 		}

			// 		$json_chat = json_encode($json_chat);
			// 		$newMsg = json_encode(array());

			// 		$sql = 'UPDATE friendly_chat SET chat = ?, newMsg = ? WHERE user_id = ? AND friend_id = ?';
			// 		$sth = $this->dbh->prepare($sql);
			// 		$sth->execute(array($json_chat, $newMsg, $this->id, $friend_id));
			// 	}

			// } else die ('<div class="chat-info">Send a message to a friend to start a chat!</div>');

		} else die('2');
	}

	// === Отправка сообщения ===
	public function sendMsg($friend_id, $sendMsg) {

		if ($this->checkBlock == true) die('1');

		$sql = 'SELECT * FROM friendly_chat WHERE user_id = ? AND friend_id = ?';
		$sth = $this->dbh->prepare($sql);

		$sth->execute(array($this->id, $friend_id));
		$myDB = $sth->fetchAll(PDO::FETCH_ASSOC);

		$sth->execute(array($friend_id, $this->id));
		$friendDB = $sth->fetchAll(PDO::FETCH_ASSOC);

		// Проверка на существование двух чатов
		if (count($myDB) && count($friendDB)) {

			// Формируем сообщение
			$time = date('H:i');
			$data = array(
				'id' => $this->id,
				'name' => $this->username,
				'msg' => $sendMsg,
				'time' => $time
			);

			// Переводим полученые результы (чаты) в массив
			$myChat = json_decode($myDB[0]['chat'], true);
			$friendNewMsg = json_decode($friendDB[0]['newMsg'], true);

			// Записываем новое сообщение
			array_push($myChat, $data);
			array_push($friendNewMsg, $data);

			// Декодируем массив в JSON и сохраняем его в БД
			$myChat = json_encode($myChat);
			$friendNewMsg = json_encode($friendNewMsg);

			$sql = 'UPDATE friendly_chat SET chat = ? WHERE user_id = ? AND friend_id = ?';
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($myChat, $this->id, $friend_id));

			$sql = 'UPDATE friendly_chat SET newMsg = ? WHERE user_id = ? AND friend_id = ?';
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($friendNewMsg, $friend_id, $this->id));

			echo json_encode(array($data));

			// echo '<div class="message sent-message">';
			// echo '<p class="message-text">'.$sendMsg.'</p>';
			// echo '<span class="message-time">'.$time.'</span></div>';

		} else die('2');
	}

	// === Возвращающие новых сообщений ===
	public function getNewMsg($friend_id) {

		if ($this->checkBlock == true) die('1');

		$sql = 'SELECT * FROM friendly_chat WHERE user_id = ? AND friend_id = ?';
		$myDB = $this->dbh->prepare($sql);
		$myDB->execute(array($this->id, $friend_id));

		$myDB = $myDB->fetchAll(PDO::FETCH_ASSOC);
		if(count($myDB) == 0) die('2');

		$newMsg = json_decode($myDB[0]['newMsg'], true);
		$chat = json_decode($myDB[0]['chat'], true);
		
		if (count($newMsg)) {

			// foreach ($newMsg as $item) {
			// 	echo '<div class="message received-message">';
			// 	echo '<p class="message-text">'.$item['msg'].'</p>';
			// 	echo '<span class="message-time">'.$item['time'].'</span></div>';
			// }

			$chat = json_encode(array_merge($chat, $newMsg));
			$updateNewMsg = json_encode(array());
			$sql = 'UPDATE friendly_chat SET chat = ?, newMsg = ? WHERE user_id = ? AND friend_id = ?';
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($chat, $updateNewMsg, $this->id, $friend_id));

			echo json_encode($newMsg);

		} else die();
	}
}

$id = $_SESSION['user_info']['id'];
$username = $_SESSION['user_info']['username'];
$checkBlock = $_SESSION['user_info']['checkBlock'];
$blockInfo = $_SESSION['user_info']['blockInfo'];

$obj = new FriendlyChat($dbh, $id, $username, $checkBlock, $blockInfo);

if (isset($_POST['getChat'])) {
	$obj->getChat($_POST['friend_id']);
}
else if (isset($_POST['sendMsg'])) {
	$obj->sendMsg($_POST['friend_id'], $_POST['sendMsg']);
}
else if (isset($_POST['getNewMsg'])) {
	$obj->getNewMsg($_POST['friend_id']);
}
else if (isset($_POST['getBlockInfo'])) {
	$obj->getBlockInfo();
}
else header('Location: friends.php');

?>