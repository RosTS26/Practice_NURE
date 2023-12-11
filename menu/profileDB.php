<?php

session_start();
require_once '../vendor/connect.php';
require_once '../admin/connectAdminDB.php';

class AdminDB {
	private $dbh;
	private $admindbh;
	private $id;
	private $username;

	public function __construct($dbh, $admindbh, $id, $username) {
		$this->dbh = $dbh;
		$this->admindbh = $admindbh;
		$this->id = $id;
		$this->username = $username;
	}

	// Проверка на наличие пользователя
	private function checkUser() {

		$sql = 'SELECT * FROM users_chat WHERE user_id = ?';
		$sth = $this->admindbh->prepare($sql);
		$sth->execute(array($this->id));
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	// Изменение имени
	public function changeUsername($newName) {
		$sql = 'SELECT * FROM user WHERE username = :newName';
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':newName', $newName);
		$sth->execute();
		$res = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)) {
			echo '<p style="color: #EC5651;">This name is taken!</p>';
		} else {
			$sql = 'UPDATE user SET username = ? WHERE id = ?';
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($newName, $this->id));

			$sql = 'UPDATE users_chat SET username = ? WHERE user_id = ?';
			$sth = $this->admindbh->prepare($sql);
			$sth->execute(array($newName, $this->id));

			$sql = 'UPDATE users_ban SET username = ? WHERE user_id = ?';
			$sth = $this->admindbh->prepare($sql);
			$sth->execute(array($newName, $this->id));

			echo '<p style="color: #50C878;">Your new name: '.$newName.'!</p>';
			$_SESSION['user_info']['username'] = $newName;
		}
	}

	// Изменение пароля
	public function changePassword($password, $newPassword, $repeatPassword) {

		$sql = 'SELECT * FROM user WHERE id = ? AND password = ?';
		$sth = $this->dbh->prepare($sql);
		$sth->execute(array($this->id, $password));
		$res = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)) {
			if (strlen($_POST['newPassword']) < 6) {
				echo '<p style="color: #EC5651;">New password is too short!</p>';
			} else if ($newPassword != $repeatPassword) {
				echo '<p style="color: #EC5651;">Password mismatch!</p>';
			} else {

				$sql = 'UPDATE user SET password = ? WHERE id = ?';
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($newPassword, $this->id));
				echo '<p style="color: #50C878;">Your password has been changed!</p>';
			}
		} else {
			echo '<p style="color: #EC5651;">Old password is not correct!</p>';
		}
	}

	// Удаление аккаунта
	public function deleteAccount($password) {
		$sql = 'SELECT * FROM user WHERE id = ? AND password = ?';
		$sth = $this->dbh->prepare($sql);
		$sth->execute(array($this->id, $password));
		$res = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)) {
			$delPass = $password.'remove';

			$sql = 'UPDATE user SET password = ? WHERE id = ?';
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($delPass, $this->id));
			
			$_SESSION['message'] = 'Account delete!';
			unset($_SESSION['user_info']);

		} else {
			echo '<p style="color: #EC5651;">Password is not correct!</p>';
		}
	}

	// Загрузка чата с админом
	public function loadChat() {

		$res = $this->checkUser();
		
		if (count($res)) {
			$json_chat = json_decode($res[0]['json_chat'], true);
			$newMsg = json_decode($res[0]['newMsg'], true);
			
			foreach ($json_chat as $item) {
				if ($item['id'] == $this->id) {
					echo '<div class="message sent-message">';
				} else {
					echo '<div class="message received-message">';
				}
				echo '<p class="message-text">'.$item['msg'].'</p>';
				echo '<span class="message-time">'.$item['time'].'</span></div>';
			}

			// Проверяем столбец с новыми сообщениями
			$flag = false;
			foreach ($newMsg as $key => $item) {
				// Если в нем есть сообщения от друга, удаляем их
				if ($item['id'] != $this->id) {
					unset($newMsg[$key]);
					$flag = true;
				}
			}

			// Если были новые сообщения от друга, очищаем столбец с новыми сообщениями
			if ($flag) {
				$newMsg = json_encode($newMsg);

				$sql = 'UPDATE users_chat SET newMsg = ? WHERE user_id = ?';
				$sth = $this->admindbh->prepare($sql);
				$sth->execute(array($newMsg, $this->id));
			}
		}
	}

	// Проверка на получение новых сообщений
	public function getNewMsg() {

		$res = $this->checkUser();

		if (count($res)) {

			$newMsg = json_decode($res[0]['newMsg'], true);

			if (count($newMsg)) {

				$flag = false;
				foreach ($newMsg as $key => $item) {
					if ($item['id'] != $this->id) {
						echo '<div class="message received-message">';
						echo '<p class="message-text">'.$item["msg"].'</p>';
						echo '<span class="message-time">'.$item["time"].'</span></div>';
						unset($newMsg[$key]);
						$flag = true;
					}
				}

				if ($flag) {
					$newMsg = json_encode($newMsg);

					$sql = 'UPDATE users_chat SET newMsg = ? WHERE user_id = ?';
					$sth = $this->admindbh->prepare($sql);
					$sth->execute(array($newMsg, $this->id));
				}
			}
		}
	}

	// Отправка сообщения
	public function sentMsg($sendText) {

		$res = $this->checkUser();

		// Формируем сообщение
		$time = date('H:i');
		$data = array(
			'id' => $this->id,
			'name' => $this->username,
			'msg' => $sendText,
			'time' => $time
		);

		if (count($res) && isset($sendText)) {

			$json_chat = json_decode($res[0]['json_chat'], true);
			array_push($json_chat, $data);
			$json_chat = json_encode($json_chat);

			$newMsg = json_decode($res[0]['newMsg'], true);
			array_push($newMsg, $data);
			$newMsg = json_encode($newMsg);
			
			$sql = 'UPDATE users_chat SET json_chat = ?, newMsg = ? WHERE user_id = ?';
			$sth = $this->admindbh->prepare($sql);
			$sth->execute(array($json_chat, $newMsg, $this->id));

		} else {
			$json_chat = array();
			array_push($json_chat, $data);
			$json_chat = json_encode($json_chat);

			$sql = 'INSERT INTO users_chat (user_id, username, json_chat, newMsg) VALUES (?, ?, ?, ?)';
			$sth = $this->admindbh->prepare($sql);
			$sth->execute(array($this->id, $_SESSION['user_info']['username'], $json_chat, $json_chat));
		}

		echo '<div class="message sent-message">';
		echo '<p class="message-text">'.$sendText.'</p>';
		echo '<span class="message-time">'.$time.'</span></div>';
	}
}

$id = $_SESSION['user_info']['id'];
$username = $_SESSION['user_info']['username'];
$obj = new AdminDB($dbh, $admindbh, $id, $username);

if (isset($_POST['loadChat'])) {
	$obj->loadChat();
}
else if (isset($_POST['getNewMsg'])) {
	$obj->getNewMsg();
}
else if (isset($_POST['sentMsg'])) {
	$obj->sentMsg($_POST['sendText']);
}
else if (isset($_POST['newName'])) {
	$obj->changeUsername($_POST['newName']);
} 
else if (isset($_POST['newPassword'])) {
	$obj->changePassword(md5($_POST['password']), md5($_POST['newPassword']), md5($_POST['repeatPassword']));
} 
else if (isset($_POST['deleteAccount'])) {
	$obj->deleteAccount(md5($_POST['password']));
} 
else header('Location: profile.php');

$dbh = null;
$admindbh = null;

?>