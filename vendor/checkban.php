<?php

if (isset($_SESSION['user_info'])) {
	$sqlBan = 'SELECT * FROM users_ban WHERE user_id = '.$_SESSION['user_info']['id'];
	$sth = $admindbh->query($sqlBan);
	$checkBan = $sth->fetchAll(PDO::FETCH_ASSOC);

	if (count($checkBan) > 0) {
		$currentDateTime = new DateTime();

		foreach ($checkBan as $row) {
			// Преобразование даты и времени из базы данных в объект DateTime
			$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['end_date']);

			if ($currentDateTime < $end_date) {
				$cause = $row['cause'];
				$_SESSION['msgError'] = 'Your account is blocked until <br>'.$end_date->format('Y-m-d H:i:s').'<br>Cause: '.$cause;
				unset($_SESSION['user_info']);
			}
		}
	}
}

?>