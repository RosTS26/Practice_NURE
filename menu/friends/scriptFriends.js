let ajax = new XMLHttpRequest();
let ajaxPush;
let user_id;
let username;
let online;

// Функция для сбрасываний до стандартных значений 
function defaultProfile() {
	clearInterval(ajaxPush);
	$('#username').html("UserName: ");
	$('#ID').html(`ID: `);
	$('.functions-btns').html('');
	$('.chat-container').html('');
	$('.field_for_message').html('');
	$('.field_for_message').css('display', 'none');
	$('.friend-profile').css('display', 'none');
}

// Демонстрация профиля друга
function showProfile() {
	$('.field_for_message').css('display', 'block');
	$('.friend-profile').css('display', 'flex');
	$('#username').html("UserName: <b>" + username + '</b>');
	$('#ID').html(`ID: <b>${user_id}</b>`);
	// if (online) {
	// 	$('#ID').append('<div id="online-check">Online</div>');
	// } else {
	// 	$('#ID').append('<div id="online-check">Offline</div>');
	// }
}

// Обновление списка друзей
function updateFriendList(data) {

	let arr = JSON.parse(data);

	$('.friends_list').empty();
	arr.forEach(item => {
		let friend = $('<li></li>');
		friend.addClass('user_item').attr('online', item['online']).attr('username', item['username']).attr('id', item['id']).html(item['username']);
		$('.friends_list').first().append(friend);
		if (Number(item['online']) == 1) {
			let online = $('<div>online</div>');
			online.addClass('online');
			$('#' + item["id"]).append(online);
		}
	});
	//alert(data);
}

// Вывод истории сообщений пользователя (основного чата)
function loadStartChat(data, friend_id) {

	// Обработка ошибок сервера
	// 1 - блокировка чата
	if (data == '1') {
    	$('#btnSendMsg').prop('disabled', true);

		$.post('friendlychat.php', {
			getBlockInfo: true,
		}, function(blockInfo) {
			$('.chat-container').html('<div class="chat-info blockChat">' + blockInfo + '</div>');
		});
		return 0;
	}

	// 2 - Ошибка заргузки чата
	else if (data == '2') {
		$('.chat-container').html('<div class="chat-info">Error: chat not loaded!</div>');
		return 0;
	}

	let dataChat = JSON.parse(data);
	$('.chat-container').empty();

	// Если чат пустой, выводим сообщение, с просьбой отправить первое сообщение 
	if (dataChat.length == 0) {
		let msgInfo = $('<div></div>');
		msgInfo.addClass('chat-info').html('Send a message to a friend to start a chat!');
		$('.chat-container').html(msgInfo);
	} 
	else {
		// Выводим на экран сообщения в зависимости от отправителя
		dataChat.forEach(item => {
			let msg = $('<div></div>');

			if (item['id'] == friend_id) msg.addClass('message received-message');
			else msg.addClass('message sent-message');

			msg.html('<p class="message-text">' + item['msg'] + '</p>');
			msg.append('<span class="message-time">' + item['time'] + '</span>');
			$('.chat-container').append(msg);
		});

		// Скролл вниз 
		$('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
	}
}

// Вывод новых сообщений пользователя (NewMsg)
function loadNewMsg(data, friend_id) {

	if (data == '1') {
		// Ответ от сервера "1" - остановка пулинга (получена блокировка чата)
		clearInterval(ajaxPush);
    	$('#btnSendMsg').prop('disabled', true);

		$.post('friendlychat.php', {
			getBlockInfo: true,
		}, function(blockInfo) {
			$('.chat-container').html('<div class="chat-info blockChat">' + blockInfo + '</div>');
		});

		return 0;
	}
	else if (data == '2') {
		clearInterval(ajaxPush);
		$('.chat-container').html('<div class="chat-info">Error: chat not loaded!</div>');
		return 0;
	}

	let newMsgs = JSON.parse(data);

	newMsgs.forEach(item => {
		let msg = $('<div></div>');

		if (item['id'] == friend_id) msg.addClass('message received-message');
		else msg.addClass('message sent-message');

		msg.html('<p class="message-text">' + item['msg'] + '</p>');
		msg.append('<span class="message-time">' + item['time'] + '</span>');
		$('.chat-container').append(msg);
	});

	$('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
}


// Проверка кол-ва входных заявок в друзья
function checkNewApp(data) {
	let len = JSON.parse(data).length;

	if (len != 0) {
		$('.check-newMsg').remove();
		let checkNewApp = $('<div></div>');
		checkNewApp.addClass('check-newMsg').html(len);
		$('#incoming-app').append(checkNewApp);
	} else {
		$('.check-newMsg').remove();
	}
}

$(function() {

	// Проверка на новые заявки в друзья
	$.post('friendsDB.php', {
		incoming: true,
	}, function(data) {
		checkNewApp(data);
	});

	// Панель для выбора друга
	$('.friends_list').on('click', '.user_item', function() {

		$(this).prependTo('.friends_list');
		$('.friends_info').scrollTop(0);
		$('.user_item').css('background', '#F0F0F0');
		$(this).css('background', '#89E39B');
		$('.chat-container').html('');

		user_id = $(this).attr('id');
		username = $(this).attr('username');
		(Number($(this).attr('online')) == 1) ? online = true : online = false;

		// === My friends ===
		if ($('#option1').is(':checked')) {
			showProfile();
			clearInterval(ajaxPush);

			// Формирование панели с свойствами и методами для выбраного друга 
			$('.functions-btns').html('<input type="radio" name="chat-or-stat" id="radio-chat" checked="true">' +
				'<label for="radio-chat" class="btn" id="chat">Chat</label>' +
				'<input type="radio" name="chat-or-stat" id="radio-stat">' +
				'<label for="radio-stat" class="btn" id="stat">Statistics</label>' +
				'<input type="button" class="btn" id="delete-friend" value="Delete a friend">');
			$('.field_for_message').html('<div class="chat-container"></div>' +
				'<div class="text-and-btn">' +
				'<textarea id="text-to-send"></textarea>' +
				'<input type="button" class="btn" value="Send" id="btnSendMsg"></div>');

			// Загрузка чата с другом
			$.post('friendlychat.php', {
				getChat: true,
				friend_id: user_id
			}, function(data) {
				loadStartChat(data, user_id);
				// $('.chat-container').html(data);
				// $('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
			});

			// Загрузка новых сообщений
			$.post('friendlychat.php', {
				getNewMsg: true,
				friend_id: user_id
			}, function(data) {
				// Если есть новые сообщения при загрузке, выводим их
				if (data) {
					$('.chat-container').append('<div class="newMsgInfo">New message</div>');
					loadNewMsg(data, user_id);
				}
			});

			// XHR-пулинг (каждую секунду делаем запрос на сервер для получения NewMsg)
			ajaxPush = setInterval(function() {
		    	$.post('friendlychat.php',{
		    		getNewMsg: true,
		    		friend_id: user_id
		    	}, function(data) {
		        	if (data) {
		        		if ($('.chat-info').val() === '') $('.chat-container').empty();
	    				loadNewMsg(data, user_id);
    				}
		    	});
			}, 1000);

			// === Отображение чата с дургом ===
			$('#chat').on('click', function() {
				$('#chat').css('background', 'linear-gradient(#49B681, #298533)');
				$('#stat').css('background', 'linear-gradient(#49708f, #293f50)');

				$('.field_for_message').html('<div class="chat-container"></div>' +
					'<div class="text-and-btn">' +
					'<textarea id="text-to-send"></textarea>' +
					'<input type="button" class="btn" value="Send" id="btnSendMsg"></div>');

				// Загрузка чата с другом
				$.post('friendlychat.php', {
					getChat: true,
    				friend_id: user_id
    			}, function(data) {
					loadStartChat(data, user_id);
    			});
			});


			// === Отправка сообщения ===
			$('.field_for_message').on('click', '#btnSendMsg', function() {
				if ($('#text-to-send').val().trim() !== '') {
					$.post('friendlychat.php', {
						sendMsg: $('#text-to-send').val(),
	    				friend_id: user_id
	    			}, function(data) {
	    				if ($('.chat-info').val() === '') $('.chat-container').empty();
	    				loadNewMsg(data, user_id);
	    			});
					$('#text-to-send').val('');
				}
			});

			// === Отображение статистики друга ===
			$('#stat').on('click', function() {
				$('#stat').css('background', 'linear-gradient(#49B681, #298533)');
				$('#chat').css('background', 'linear-gradient(#49708f, #293f50)');
				
				$.post('friendsDB.php', {
					check_stat: true,
					friendName: username,
    				friend_id: user_id
    			}, function(data) {
					$('.field_for_message').html(data);
    			});
			});

			// === Удалить друга из друзей ===
			$('#delete-friend').on('click', function() {
				if (confirm('Are you sure you want to delete this friend?')) {
					$.post('friendsDB.php', {
						delete_friend: true,
	    				friend_id: user_id
	    			}, function(data) {
	    				if (data == "1") {
	    					alert('Error: cancel application impossible!');
	    				} else {
		    				updateFriendList(data);
	    				}
	    			});
	    			defaultProfile();
				}
			});
		}

		// === Sent app ===
		else if ($('#option2').is(':checked')) {
			showProfile();
			$('.functions-btns').html('<input type="button" class="btn" id="cancel-app" value="Cancel the app">');

			$('#cancel-app').on('click', function() {
				if (confirm('Are you sure you want to cancel the application?')) {
					$.post('friendsDB.php', {
						cancel: true,
	    				friend_id: user_id
	    			}, function(data) {
	    				if (data == '1') alert('Error: cancel application impossible!');
	    				else if (data == '2') alert('Error: not ID!');
	    				else updateFriendList(data);
	    			});
	    			defaultProfile();
				}
			});
		}

		// === Incoming app ===
		else if ($('#option3').is(':checked')) {
			showProfile();
			$('.functions-btns').html('<input type="button" class="btn" id="accept-app" value="Accept app">');
			$('.functions-btns').append('<input type="button" class="btn" id="cancel-app" value="Cancel the app">');

			$('#accept-app').on('click', function() {
				if (confirm('Do you want to accept friend request?')) {
					$.post('friendsDB.php', {
						accept: true,
	    				friend_id: user_id
	    			}, function(data) {

	    				checkNewApp(data);

	    				if (data == '1') alert('Error: cancel application impossible!');
	    				else if (data == '2') alert('Error: not ID!');
	    				else updateFriendList(data);
	    			});
	    			defaultProfile();
				}
			});

			// Удаление входящих заявок
			$('#cancel-app').on('click', function() {
				if (confirm('Are you sure you want to cancel the application?')) {
					$.post('friendsDB.php', {
						cancel_incom: true,
	    				friend_id: user_id
	    			}, function(data) {
	    				
	    				checkNewApp(data);

	    				if (data == '1') alert('Error: cancel application impossible!');
	    				else if (data == '2') alert('Error: not ID!');
	    				else updateFriendList(data);	
	    			});
	    			defaultProfile();
				}
			});
		}
	}); 

	// Добавление друга
	$('#friend-add').on('click', function() {
		if ($('#friend-name').val() != '') {
			let friendName = $('#friend-name').val();

			$.post('friendsDB.php', {
				add: true,
    			friendName: friendName
    		}, function(data) {
    			alert(data);
    			//$('#test').html(data);
    		});

    		$('#friend-name').val('');
		}
	});

	// Мои друзья
	$('#my-friends').on('click', function() {
		defaultProfile();
		$('#my-friends').css('background', 'linear-gradient(#49B681, #298533)');
		$('#sent-app').css('background', 'linear-gradient(#49708f, #293f50)');
		$('#incoming-app').css('background', 'linear-gradient(#49708f, #293f50)');

		$.post('friendsDB.php', {
			friends: true,
    	}, function(data) {
    		updateFriendList(data);
    	});
	}); 

	// Отправленные заявки
	$('#sent-app').on('click', function() {
		defaultProfile();
		$('#sent-app').css('background', 'linear-gradient(#49B681, #298533)');
		$('#my-friends').css('background', 'linear-gradient(#49708f, #293f50)');
		$('#incoming-app').css('background', 'linear-gradient(#49708f, #293f50)');

		$.post('friendsDB.php', {
			sent: true,
    	}, function(data) {
    		updateFriendList(data);
    	});
	}); 

	// Полученные заявки
	$('#incoming-app').on('click', function() {
		defaultProfile();
		$('#incoming-app').css('background', 'linear-gradient(#49B681, #298533)');
		$('#my-friends').css('background', 'linear-gradient(#49708f, #293f50)');
		$('#sent-app').css('background', 'linear-gradient(#49708f, #293f50)');

		$.post('friendsDB.php', {
			incoming: true,
    	}, function(data) {
    		updateFriendList(data);
    	});
	});

	$('#my-friends').trigger('click');
});