<?php

session_start();
require_once 'connectAdminDB.php';

class AdminDB {
	private $dbh;
	private $id;

	public function __construct($dbh, $id) {
		$this->dbh = $dbh;
		$this->id = $id;
	}

	// Проверка на наличие существующего пользователя
	private function checkUser($user_id, $user_name) {
		require_once '../vendor/connect.php';

		if ($user_id != '') {
			$sql = 'SELECT * FROM user WHERE id = :user_id';
			$sth = $dbh->prepare($sql);
			$sth->bindValue(':user_id', $user_id);
		} else {
			$sql = 'SELECT * FROM user WHERE username = :user_name';
			$sth = $dbh->prepare($sql);
			$sth->bindValue(':user_name', $user_name);
		}
		$sth->execute();

		$dbh = null;
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	// Проверка на наличие чата пользователя
	private function checkUserChat($user_id) {

		$sql = 'SELECT * FROM users_chat WHERE user_id = ?';
		$sth = $this->dbh->prepare($sql);
		$sth->execute(array($user_id));
		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	// Загрузка чата с пользователем
	public function loadChat($user_id) {

		$res = $this->checkUserChat($user_id);
		
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

			$flag = false;
			foreach ($newMsg as $key => $item) {
				if ($item['id'] != $this->id) {
					unset($newMsg[$key]);
					$flag = true;
				}
			}

			if ($flag) {
				$newMsg = json_encode($newMsg);

				$sql = 'UPDATE users_chat SET newMsg = ? WHERE user_id = ?';
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($newMsg, $user_id));
			}
		}
	}

	// Проверка на получение новых сообщений
	public function getNewMsg($user_id) {

		$res = $this->checkUserChat($user_id);

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
					$sth = $this->dbh->prepare($sql);
					$sth->execute(array($newMsg, $user_id));
				}
			}
		}
	}

	// Отправка сообщения
	public function sentMsg($user_id, $sendText) {

		$res = $this->checkUserChat($user_id);

		if (count($res) && isset($sendText)) {

			// Формируем сообщение
			$time = date('H:i');
			$data = array(
				'id' => $this->id,
				'name' => 'admin',
				'msg' => $sendText,
				'time' => $time
			);

			$json_chat = json_decode($res[0]['json_chat'], true);
			array_push($json_chat, $data);
			$json_chat = json_encode($json_chat);

			$newMsg = json_decode($res[0]['newMsg'], true);
			array_push($newMsg, $data);
			$newMsg = json_encode($newMsg);
			
			$sql = 'UPDATE users_chat SET json_chat = ?, newMsg = ? WHERE user_id = ?';
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($json_chat, $newMsg, $user_id));

			echo '<div class="message sent-message">';
			echo '<p class="message-text">'.$sendText.'</p>';
			echo '<span class="message-time">'.$time.'</span></div>';
		}
	}

	// ==================================================================

	// Блокировка пользователя
	public function userBan($user_id, $user_name, $cause, $days) {

		if ($cause == '') $cause = 'ban';
		$res = $this->checkUser($user_id, $user_name);
		
		if (count($res)) {

			$currentDateTime = new DateTime();

			$sqlBan = 'SELECT * FROM users_ban WHERE user_id = '.$res[0]['id'];
			$sth = $this->dbh->query($sqlBan);
			$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

			if (count($checkBan) > 0) {

				foreach ($checkBan as $row) {
					$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

					if ($currentDateTime < $end_date) {
						die('User "'.$res[0]['username'].'" is already banned!');
					}
				}
			}

			$banDateTime = new DateTime();
			$banDateTime->modify("$days days");
			$start_date = $currentDateTime->format('Y-m-d H:i:s');
			$end_date = $banDateTime->format('Y-m-d H:i:s');

			$sql = "INSERT INTO users_ban (user_id, username, cause, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($res[0]['id'], $res[0]['username'], $cause, $start_date, $end_date));

			echo 'User "'.$res[0]['username'].'" banned for '.$days.' day(s)!';

		} else echo 'This user does not exist';
	}

	// Разблокировка пользователя
	public function userUnban($user_id, $user_name) {

		$res = $this->checkUser($user_id, $user_name);
		
		if (count($res)) {

			$currentDateTime = new DateTime();
			//$sqlcurrentDT = $currentDateTime->format('Y-m-d H:i:s');

			$sqlBan = 'SELECT * FROM users_ban WHERE user_id = '.$res[0]['id'];
			$sth = $this->dbh->query($sqlBan);
			$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

			if (count($checkBan) > 0) {

				foreach ($checkBan as $row) {
					$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

					if ($currentDateTime < $end_date) {
						$sql = 'UPDATE users_ban SET end_date = ? WHERE start_date = ?';
						$sth = $this->dbh->prepare($sql);
						$sth->execute(array($row['start_date'], $row['start_date']));
						die('User "'.$res[0]['username'].'" unbanned!');
					}
				}
			}
			die ('User "'.$res[0]['username'].'" is not banned!');
		} else die ('This user does not exist!');
	}

	// Блокировка чата юзера
	public function userBlockChat($user_id, $user_name, $cause, $days) {

		if ($cause == '') $cause = 'block';
		$res = $this->checkUser($user_id, $user_name);
		
		if (count($res)) {

			$currentDateTime = new DateTime();

			$sqlBan = 'SELECT * FROM users_block_chat WHERE user_id = '.$res[0]['id'];
			$sth = $this->dbh->query($sqlBan);
			$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

			if (count($checkBan) > 0) {

				foreach ($checkBan as $row) {
					$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

					if ($currentDateTime < $end_date) {
						die('User "'.$res[0]['username'].'" is already blocked chat!');
					}
				}
			}

			$banDateTime = new DateTime();
			$banDateTime->modify("$days days");
			$start_date = $currentDateTime->format('Y-m-d H:i:s');
			$end_date = $banDateTime->format('Y-m-d H:i:s');

			$sql = "INSERT INTO users_block_chat (user_id, username, cause, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($res[0]['id'], $res[0]['username'], $cause, $start_date, $end_date));

			echo 'User "'.$res[0]['username'].'" blocked chat for '.$days.' day(s)!';

		} else echo 'This user does not exist';
	}

	// Разблокировка чата юзера
	public function userUnblockChat($user_id, $user_name) {

		$res = $this->checkUser($user_id, $user_name);
		
		if (count($res)) {

			$currentDateTime = new DateTime();
			//$sqlcurrentDT = $currentDateTime->format('Y-m-d H:i:s');

			$sqlBan = 'SELECT * FROM users_block_chat WHERE user_id = '.$res[0]['id'];
			$sth = $this->dbh->query($sqlBan);
			$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

			if (count($checkBan) > 0) {

				foreach ($checkBan as $row) {
					$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

					if ($currentDateTime < $end_date) {
						$sql = 'UPDATE users_block_chat SET end_date = ? WHERE start_date = ?';
						$sth = $this->dbh->prepare($sql);
						$sth->execute(array($row['start_date'], $row['start_date']));
						die('User "'.$res[0]['username'].'" unblocked chat!');
					}
				}
			}
			die ('User "'.$res[0]['username'].'" is not blocked chat!');
		} else die ('This user does not exist!');
	}
}

$id = $_SESSION['user_info']['id'];
$obj = new AdminDB($admindbh, $id);

if ($id == 1) {
	if (isset($_POST['loadChat'])) {
		$obj->loadChat($_POST['user_id']);
	}
	else if (isset($_POST['getNewMsg'])) {
		$obj->getNewMsg($_POST['user_id']);
	}
	else if (isset($_POST['sentMsg'])) {
		$obj->sentMsg($_POST['user_id'], $_POST['sendText']);
	}
	else if (isset($_POST['ban'])) {
		if ($_POST['user_id'] != 1 && $_POST['username'] != 'admin') {
			$obj->userBan($_POST['user_id'], $_POST['username'], $_POST['cause'], $_POST['days']);
		} else die ('This admin account!');
	}
	else if (isset($_POST['unban'])) {
		$obj->userUnban($_POST['user_id'], $_POST['username']);
	}
	else if (isset($_POST['block'])) {
		if ($_POST['user_id'] != 1 && $_POST['username'] != 'admin') {
			$obj->userBlockChat($_POST['user_id'], $_POST['username'], $_POST['cause'], $_POST['days']);
		} else die ('This admin account!');
	}
	else if (isset($_POST['unblock'])) {
		$obj->userUnblockChat($_POST['user_id'], $_POST['username']);
	}
} else header('Location: adminpanel.php');

?>