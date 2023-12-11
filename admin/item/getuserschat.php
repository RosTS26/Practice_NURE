<?php

session_start();
require_once 'connectAdminDB.php';

if (isset($_POST['user_id'])) {
	
	$sql = 'SELECT * FROM users_chat WHERE user_id = :id';
	$sth = $admindbh->prepare($sql);
	$sth->bindValue(':id', $_POST['user_id']);
	$sth->execute();
	$res = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($res)) {
		$newMsg = json_decode($res[0]['newMsg'], true);
		if (count($newMsg)) {
			$flag = false;

			foreach ($newMsg as $key => $item) {
				if ($item['id'] != $_SESSION['user_info']['id']) {
					echo '<div class="message received-message">';
					echo '<p class="message-text">'.$item["msg"].'</p>';
					echo '<span class="message-time">'.$item["time"].'</span></div>';
					unset($newMsg[$key]);
					$flag = true;
				}
			}

			if ($flag) {
				$newMsg = json_encode($newMsg);

				$sql = 'UPDATE users_chat SET newMsg = :newMsg WHERE user_id = :id';
				$sth = $admindbh->prepare($sql);
				$sth->bindValue(':newMsg', $newMsg);
				$sth->bindValue(':id',  $_POST['user_id']);
				$sth->execute();
			}
		}
	}

} else header('Location: ../index.php');

$admindbh = null;

?>