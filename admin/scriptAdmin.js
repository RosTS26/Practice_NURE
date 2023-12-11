let ajax = new XMLHttpRequest();
let ajaxPush;
let user_id;

function defaultBox() {
	$('#user_id').val('');
	$('#user_nickname').val('');
	$('#cause').val('');
	$('#days').val('');
	$('#user_id').prop('disabled', false);
	$('#user_nickname').prop('disabled', false);
}

$(function() {

	// User ban/unban
	//==========================================================

	$('#user_id').on('input', function() {
		if ($(this).val() !== '') {
			$('#user_nickname').prop('disabled', true);
		} else {
			$('#user_nickname').prop('disabled', false);
		}
	});

	$('#user_nickname').on('input', function() {
		if ($(this).val() !== '') {
			$('#user_id').prop('disabled', true);
		} else {
			$('#user_id').prop('disabled', false);
		}
	});

	// Выбор блокировки аккаунта или блокировка возможности отправки сообщений
	$("#ban-acc").change(function() {
		if ($(this).is(':checked')) {
			$("#styles-ban-acc").css('background', 'linear-gradient(#49B681, #298533)');
			$("#styles-block-chat").css('background', 'linear-gradient(#49708f, #293f50)');
			$(".ban-or-block").html('Ban/unban a user');

			$('#checkUnban').prop('checked', false).trigger('change');
		}
	});

	$("#block-chat").change(function() {
		if ($(this).is(':checked')) {
			$("#styles-block-chat").css('background', 'linear-gradient(#49B681, #298533)');
			$("#styles-ban-acc").css('background', 'linear-gradient(#49708f, #293f50)');
			$(".ban-or-block").html('Block/unblock user chat');

			$('#checkUnban').prop('checked', false).trigger('change');
		}
	});

	// Обработчки нажатия на чекбокс блокировка/разблокировка
	// Меняем кнопки в зависимости от выбраной функции (бан аккаунта/блокировка чата)
	$('#checkUnban').change(function() {

		if ($(this).is(':checked')) {
			$('#days').prop('disabled', true);
			$('#cause').prop('disabled', true);

			if ($('#ban-acc').is(':checked')) {
				$('.changeBtn').val('Unban');
				$('.changeBtn').attr('id', 'btnUnban');
			} else if ($('#block-chat').is(':checked')) {
				$('.changeBtn').val('Unblock');
				$('.changeBtn').attr('id', 'btnUnblock');
			}

		} else {
			$('#days').prop('disabled', false);
			$('#cause').prop('disabled', false);

			if ($('#ban-acc').is(':checked')) {
				$('.changeBtn').val('Ban');
				$('.changeBtn').attr('id', 'btnBan');
			} else if ($('#block-chat').is(':checked')) {
				$('.changeBtn').val('Block');
				$('.changeBtn').attr('id', 'btnBlock');
			}
		}
	});

	// User ban
	$(".user_ban").on("click", "#btnBan", function() {
		if ($("#user_id").val() === '' &&
			$("#user_nickname").val() === '') {
				$('#banMsg').html('Enter ID or nickname!');
				$('#banMsg').css('margin-top', '30px');
		}
		else if ($('#days').val().length == 0 || Number($('#days').val()) < '1') {
			$('#banMsg').html('Number of days input error!');
			$('#banMsg').css('margin-top', '30px');
		}
		else {
			$('#banMsg').html('').css('margin-top', '0');
			if (confirm('Ban a user for '+$('#days').val()+' day/days?')) {
				let user_id = $('#user_id').val();
				let user_nickname = $('#user_nickname').val();
				let cause = $('#cause').val();
				let days = $('#days').val();

				$.post('../admin/adminDB.php', {
					ban: true,
					user_id: user_id,
					username: user_nickname,
					cause: cause,
					days: days
				}, function(data) {
					$('#banMsg').html(data);
					$('#banMsg').css('margin-top', '30px');
					defaultBox();
				});
			}
		} 
	});

	// User unban
	$(".user_ban").on("click", "#btnUnban", function() {
		if ($("#user_id").val() === '' &&
			$("#user_nickname").val() === '') {
				$('#banMsg').html('Enter ID or nickname!');
				$('#banMsg').css('margin-top', '30px');
		} else {
			$('#banMsg').html('').css('margin-top', '0');
			if (confirm('Unban a user?')) {
				let user_id = $('#user_id').val();
				let user_nickname = $('#user_nickname').val();

				$.post('../admin/adminDB.php', {
					unban: true,
					user_id: user_id,
					username: user_nickname,
				}, function(data) {
					$('#banMsg').html(data);
					$('#banMsg').css('margin-top', '30px');
					defaultBox();
				});
			}
		} 
	});

	// User block chat
	$('.user_ban').on('click', '#btnBlock', function() {
		if ($("#user_id").val() === '' &&
			$("#user_nickname").val() === '') {
				$('#banMsg').html('Enter ID or nickname!');
				$('#banMsg').css('margin-top', '30px');
		}
		else if ($('#days').val().length == 0 || Number($('#days').val()) < '1') {
			$('#banMsg').html('Number of days input error!');
			$('#banMsg').css('margin-top', '30px');
		}
		else {
			$('#banMsg').html('').css('margin-top', '0');
			if (confirm('Block user chat for '+$('#days').val()+' day/days?')) {
				let user_id = $('#user_id').val();
				let user_nickname = $('#user_nickname').val();
				let cause = $('#cause').val();
				let days = $('#days').val();

				$.post('../admin/adminDB.php', {
					block: true,
					user_id: user_id,
					username: user_nickname,
					cause: cause,
					days: days
				}, function(data) {
					$('#banMsg').html(data);
					$('#banMsg').css('margin-top', '30px');
					defaultBox();
				});
			}
		}
	});

	// User unblock chat
	$('.user_ban').on('click', '#btnUnblock', function() {
		if ($("#user_id").val() === '' &&
			$("#user_nickname").val() === '') {
				$('#banMsg').html('Enter ID or nickname!');
				$('#banMsg').css('margin-top', '30px');
		} else {
			$('#banMsg').html('').css('margin-top', '0');
			if (confirm('Unblock user chat?')) {
				let user_id = $('#user_id').val();
				let user_nickname = $('#user_nickname').val();

				$.post('../admin/adminDB.php', {
					unblock: true,
					user_id: user_id,
					username: user_nickname,
				}, function(data) {
					$('#banMsg').html(data);
					$('#banMsg').css('margin-top', '30px');
					defaultBox();
				});
			}
		}
	});

	// Чат с игроками
	// ===========================================================

	$('#btnSendMsg').on('click', function() {
		if ($('#text-to-send').val() != '') {
			let sendText = $('#text-to-send').val();

			$.post('../admin/adminDB.php', {
				sentMsg: true,
				sendText: sendText,
				user_id: user_id
			}, function(data) {
				$('.chat-container').append(data);
				$('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
			});

			$('#text-to-send').val('');
		}
	});

	// Панель для выбора чата с игроком
	// =========================================================

	$('.user_item').on('click', function() {
		clearInterval(ajaxPush);

		$(this).prependTo('.users_list');
		$('.users_info').scrollTop(0);
		$('.user_item').css('background', '#F0F0F0');
		$(this).css('background', '#89E39B');
		$('.chat-container').html('');
		$('#btnSendMsg').prop('disabled', false);
		
		user_id = Number($(this).attr('id'));

		$.post('../admin/adminDB.php', {
			loadChat: true,
			user_id: user_id
		}, function(data) {
			$('.chat-container').append(data);
			$('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
		});

		ajaxPush = setInterval(function() {
		    $.post('../admin/adminDB.php', {
		    	getNewMsg: true,
		    	user_id: user_id
		    }, function(data) {
		    	if (data) {	    		
			        $('.chat-container').append(data);
			        $('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
		    	}
		    });
		}, 1000);
	});

	// Активация radio с функцией блокировки аккаунтов
	$('#ban-acc').prop('checked', true).trigger('change');
});
