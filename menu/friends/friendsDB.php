<?php

session_start();
require_once '../../vendor/connect.php';

class InteractionFriends {
	private $dbh;
	private $id;
	private $username;

	public function __construct ($dbh, $id, $username) {
		$this->dbh = $dbh;
		$this->id = $id;
		$this->username = $username;
	}

	// === Метод добавления новых друзей ===
	public function addFriend($friendName) {	
		// Проверка на существование пользователя
		$sql = "SELECT * FROM user WHERE username = :username";
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':username', $friendName);
		$sth->execute();
		$res = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)) {

			$friend_id = $res[0]['id']; // id юзера, которому отправлена заявка
			if ($friend_id == $this->id) die('Is that your username!');

			// Запрос на все таблицы "Друга"
			$res = $this->dbh->query("SELECT * FROM user_friends WHERE user_id = '$friend_id'")->fetch(PDO::FETCH_ASSOC);

			$checkFriends = json_decode($res['friends'], true);
			$checkSent = json_decode($res['sent_app'], true);
			// Таблица входящих заявок
			$incomApp = json_decode($res['incoming_app'], true);

			// Проверка всех колонок юзера
			foreach ($checkFriends as $item) {
				if ($item['id'] == $this->id) {
					die('This user is already your friend!');
				}
			}

			foreach ($checkSent as $item) {
				if ($item['id'] == $this->id) {
					die('This user has already sent you a friend request!');
				}
			}

			foreach ($incomApp as $item) {
				if ($item['id'] == $this->id) {
					die('Your friend request is pending!');
				}
			}

			$sentApp = $this->dbh->query("SELECT sent_app FROM user_friends WHERE user_id = '$this->id'");

			// Информация отправленной заявки в формате JSON
			$json_sentApp = array(
				'id' => $this->id,
				'username' => $this->username,
			);

			$json_incomApp = array(
				'id' => $friend_id,
				'username' => $friendName,
			);

			// Преобразование результатов в из JSON в массив
			$sentApp = $sentApp->fetch(PDO::FETCH_ASSOC);
			$sentApp = json_decode($sentApp['sent_app'], true);
			array_push($sentApp, $json_incomApp);
			array_push($incomApp, $json_sentApp);
			$sentApp = json_encode($sentApp);
			$incomApp = json_encode($incomApp);

			// Обновляем БД заявок в друзья
			$sql = "UPDATE user_friends SET sent_app = :sentApp WHERE user_id = :id";
			$sth = $this->dbh->prepare($sql);
			$sth->bindValue(':sentApp', $sentApp);
			$sth->bindValue(':id', $this->id);
			$sth->execute();

			$sql = "UPDATE user_friends SET incoming_app = :incomApp WHERE user_id = :id";
			$sth = $this->dbh->prepare($sql);
			$sth->bindValue(':incomApp', $incomApp);
			$sth->bindValue(':id', $friend_id);
			$sth->execute();

			echo 'Application successfully sent!';

		} else {
			echo 'User with this name not found';
		}
	}

	// === Метод для возвращения списка друзей ===
	public function getFriends() {
		$sql = "SELECT friends FROM user_friends WHERE user_id = '$this->id'";
		$res = $this->dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
		$friends = json_decode($res['friends'], true);

		$sql = "SELECT online FROM user WHERE id = :id";
		$sth = $this->dbh->prepare($sql);
		$sth->bindParam(':id', $friend_id);

		foreach($friends as $key => $friend) {
			$friend_id = $friend['id'];
			$sth->execute();
			$res = $sth->fetch(PDO::FETCH_ASSOC);
			$friends[$key]['online'] = $res['online'];
		}

		echo json_encode($friends);

		// $res = json_decode($res['friends'], true);
		// foreach($res as $item) {
		// 	echo '<li class="user_item" id="'.$item["id"].'">'.$item["username"].'</li>';
		// }
	}

	// === Метод для возвращения списка отправленных заявок
	public function getSentApp() {
		$sql = "SELECT sent_app FROM user_friends WHERE user_id = '$this->id'";
		$res = $this->dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
		$friends = json_decode($res['sent_app'], true);
		echo json_encode($friends);
	}

	// === Метод для возвращения списка новых заявок в друзья
	public function getIncomApp() {
		$sql = "SELECT incoming_app FROM user_friends WHERE user_id = '$this->id'";
		$res = $this->dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
		$friends = json_decode($res['incoming_app'], true);
		echo json_encode($friends);
	}

	// === Метод для отмены отправленной заявки в друзья
	public function cancelApp($friend_id, $sentOrIncom) {
		// Проверка на существование пользователя
		$sql = "SELECT * FROM user WHERE id = :id";
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id', $friend_id);
		$sth->execute();
		$res = $sth->fetchAll(PDO::FETCH_ASSOC);

		// Присваиваем ID в зависимости от входящего запроса
		if ($sentOrIncom) {
			$sentID = $this->id;
			$incomID = $friend_id;
		} else {
			$sentID = $friend_id;
			$incomID = $this->id;
		}

		if (count($res)) {

			// Запрос на определенную колонку юзеров
			$sentApp = $this->dbh->query("SELECT sent_app FROM user_friends WHERE user_id = '$sentID'");
			$incomApp = $this->dbh->query("SELECT incoming_app FROM user_friends WHERE user_id = '$incomID'");

			// Получиение чистого массива
			$sentApp = $sentApp->fetch(PDO::FETCH_ASSOC);
			$sentApp = json_decode($sentApp['sent_app'], true);
			$incomApp = $incomApp->fetch(PDO::FETCH_ASSOC);
			$incomApp = json_decode($incomApp['incoming_app'], true);

			$flag = true;

			// Удаляем из списка запросов запрос
			foreach ($sentApp as $key => $item) {
				if ($item['id'] == $incomID) {
					$flag = false;
					unset($sentApp[$key]);
					break;
				}
			}

			if ($flag) die('1');

			foreach ($incomApp as $key => $item) {
				if ($item['id'] == $sentID) {
					unset($incomApp[$key]);
					break;
				}
			}

			// Перезаписываем новый массив, обновляя ключи с помощью array_values, чтоб массив не преобразовался в объект
			$sentApp = json_encode(array_values($sentApp));
			$incomApp = json_encode(array_values($incomApp));

			// Обновляем БД заявок в друзья
			$sql = "UPDATE user_friends SET sent_app = ? WHERE user_id = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($sentApp, $sentID));

			$sql = "UPDATE user_friends SET incoming_app = ? WHERE user_id = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($incomApp, $incomID));

			// Проверка на отмену заявки от отправителя или получателя
			// Выводим новый список запросов юзеру
			if ($sentOrIncom) echo $sentApp;
			else echo $incomApp;

		} else die('2');
	}

	// === Метод для принятия заявки в друзья ===
	public function acceptApp($friend_id) {
		// Проверка на существование пользователя
		$sql = "SELECT * FROM user WHERE id = :id";
		$sth = $this->dbh->prepare($sql);
		$sth->bindValue(':id', $friend_id);
		$sth->execute();
		$res = $sth->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)) {

			// Запрос на определенную колонку юзеров
			$friendDB = $this->dbh->query("SELECT * FROM user_friends WHERE user_id = '$friend_id'");
			$myDB = $this->dbh->query("SELECT * FROM user_friends WHERE user_id = '$this->id'");

			// Получиение чистого массива
			$friendDB = $friendDB->fetch(PDO::FETCH_ASSOC);
			$sentApp = json_decode($friendDB['sent_app'], true);
			$myDB = $myDB->fetch(PDO::FETCH_ASSOC);
			$incomApp = json_decode($myDB['incoming_app'], true);

			$flag = true;

			// Удаляем из списка запросов запрос и добавляем в список друзей юзера
			foreach ($sentApp as $key => $item) {
				if ($item['id'] == $this->id) {
					$friends = json_decode($friendDB['friends'], true);
					array_push($friends, $item);
					$friends = json_encode($friends);

					$sql = "UPDATE user_friends SET friends = ? WHERE user_id = ?";
					$sth = $this->dbh->prepare($sql);
					$sth->execute(array($friends, $friend_id));

					unset($sentApp[$key]);
					$flag = false;
					break;
				}
			}

			if ($flag) die('1');

			foreach ($incomApp as $key => $item) {
				if ($item['id'] == $friend_id) {

					$friends = json_decode($myDB['friends'], true);
					array_push($friends, $item);
					$friends = json_encode($friends);

					$sql = "UPDATE user_friends SET friends = ? WHERE user_id = ?";
					$sth = $this->dbh->prepare($sql);
					$sth->execute(array($friends, $this->id));

					unset($incomApp[$key]);
					break;
				}
			}

			// Добавляем обновленный объект запросов юзерам обратно
			$sentApp = json_encode(array_values($sentApp));
			$incomApp = json_encode(array_values($incomApp));

			// Обновляем БД заявок в друзья
			$sql = "UPDATE user_friends SET sent_app = ? WHERE user_id = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($sentApp, $friend_id));

			$sql = "UPDATE user_friends SET incoming_app = ? WHERE user_id = ?";
			$sth = $this->dbh->prepare($sql);
			$sth->execute(array($incomApp, $this->id));

			// Выводим новый список запросов юзеру
			echo $incomApp;

			// === Создаем 2 чата для юзера и его друга (если их нету)
			$newChats = $this->dbh->query("SELECT * FROM friendly_chat WHERE user_id = '$this->id' AND friend_id = '$friend_id'");
			$newChats = $newChats->fetchAll(PDO::FETCH_ASSOC);

			if (!count($newChats)) {
				$json = json_encode(array());

				$sql = "INSERT INTO friendly_chat (user_id, friend_id, chat, newMsg) VALUES (?, ?, ?, ?)";
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($this->id, $friend_id, $json, $json));
				$sth = $this->dbh->prepare($sql);
				$sth->execute(array($friend_id, $this->id, $json, $json));
			}
		} else die('2');
	}

	// Удаление друга из списка друзей
	public function deleteFriend($friend_id) {

		// Информация о списках друзей
		$friendDB = $this->dbh->query("SELECT * FROM user_friends WHERE user_id = '$friend_id'");
		$myDB = $this->dbh->query("SELECT * FROM user_friends WHERE user_id = '$this->id'");

		// Получиение массива друзей
		$friendDB = $friendDB->fetch(PDO::FETCH_ASSOC);
		$friendDB = json_decode($friendDB['friends'], true);
		$myDB = $myDB->fetch(PDO::FETCH_ASSOC);
		$myDB = json_decode($myDB['friends'], true);

		// Удаляем юзеров из списка друзей
		$flag = true;
		foreach ($friendDB as $key => $item) {
			if ($item['id'] == $this->id) {
				unset($friendDB[$key]);
				$flag = false;
				break;
			}
		}
		if ($flag) die('1');

		foreach ($myDB as $key => $item) {
			if ($item['id'] == $friend_id) {
				unset($myDB[$key]);
				break;
			}
		}

		// Добавляем обновленный объект запросов юзерам обратно
		$myDB = json_encode(array_values($myDB));
		$friendDB = json_encode(array_values($friendDB));

		// Обновляем БД заявок в друзья
		$sql = "UPDATE user_friends SET friends = ? WHERE user_id = ?";
		$sth = $this->dbh->prepare($sql);
		$sth->execute(array($myDB, $this->id));
		$sth->execute(array($friendDB, $friend_id));

		// Выводим новый список друзей юзеру
		echo $myDB;
	}

	// *** ВОЗВРАЩАТЬ В ВИДЕ JSON ***
	// === Просмотр статистики друга в играх ===
	public function checkStat($friend_id, $friendName) {

		$games = ['snake', 'tetris', 'roulette'];
		foreach ($games as $row) {
			$sql = 'SELECT * FROM '.$row.' WHERE ID_'.$row.' = '.$friend_id;
			$sth = $this->dbh->query($sql);
			$friendDB = $sth->fetch();

			$sql = 'SELECT * FROM '.$row.' WHERE ID_'.$row.' = '.$this->id;
			$sth = $this->dbh->query($sql);
			$myDB = $sth->fetch();

			echo '<table border="1">';
			echo '<caption><b>'.$row.'</b></caption>';
			echo '<tr>
				<th>NickName</th>
				<th>Top score</th>
				<th>Games played</th>
			</tr>';
			echo '<tr>
				<td>'.$friendName.'</td>
				<td>'.$friendDB[1].'</td>
				<td>'.$friendDB[2].'</td>
			</tr> 
			<tr>
				<td>'.$this->username.'</td>
				<td>'.$myDB[1].'</td>
				<td>'.$myDB[2].'</td>
			</tr>';
			echo '</table>';
		}
	}
}

$id = $_SESSION['user_info']['id'];
$username = $_SESSION['user_info']['username'];
$obj = new InteractionFriends($dbh, $id, $username);

if (isset($_POST['add'])) {
	$obj->addFriend($_POST['friendName']);
}
else if (isset($_POST['friends'])) {
	$obj->getFriends();
}
else if (isset($_POST['sent'])) {
	$obj->getSentApp();
}
else if (isset($_POST['incoming'])) {
	$obj->getIncomApp();
}
else if (isset($_POST['cancel'])) {
	$obj->cancelApp($_POST['friend_id'], true);
}
else if (isset($_POST['cancel_incom'])) {
	$obj->cancelApp($_POST['friend_id'], false);
}
else if (isset($_POST['accept'])) {
	$obj->acceptApp($_POST['friend_id']);
}
else if (isset($_POST['delete_friend'])) {
	$obj->deleteFriend($_POST['friend_id']);
}
else if (isset($_POST['check_stat'])) {
	$obj->checkStat($_POST['friend_id'], $_POST['friendName']);
} 
else header('Location: friends.php');

$dbh = null;
?>