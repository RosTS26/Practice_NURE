<?php

if (isset($_SESSION['user_info'])) {
	$sqlBan = 'SELECT * FROM users_block_chat WHERE user_id = '.$_SESSION['user_info']['id'];
	$sth = $admindbh->query($sqlBan);
	$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($checkBan)) {
		$currentDateTime = new DateTime();

		foreach ($checkBan as $row) {
			// Преобразование даты и времени из базы данных в объект DateTime
			$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

			if ($currentDateTime < $end_date) {
				$cause = $row['cause'];
				$_SESSION['user_info']['checkBlock'] = true;
				$_SESSION['user_info']['blockInfo'] = 'You are not allowed to use the chat until: <br>'.$end_date->format('Y-m-d H:i:s').'<br>Cause: '.$cause;
				break;
			}
			$_SESSION['user_info']['checkBlock'] = false;
		}
	}
}

?>