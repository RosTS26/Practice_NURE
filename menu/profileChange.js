let ajax = new XMLHttpRequest();
let ajaxPush;
let user_id = $('#myID').html();

if (localStorage.getItem(`${user_id} newMsg`) == 1) {
	$('.check-newMsg').css('display', 'block');
}

$(function() {

	// === Изменение никнейма
	$('#nameChange').on('click', function() {
		$('#msg').html('New name: <input id="TextNewName" type="text" autocomplete="off"></input>' + 
			'<input id="newName" class="update" value="Change" type="button"></input>');
		
		$('#newName').on('click', function() {
			if ($('#TextNewName').val().length < 4) {
				alert('This name is too short!');
			}
			else if ($('#TextNewName').val().length > 30) {
				alert('This name is too long!')
			}
			else if (confirm('Do you really want to change your name?')) {

				$.post('profileDB.php', {
			    	newName: $('#TextNewName').val()
			    }, function(data) {
			    	$('#msg').html(data);
			    });

				// ajax.onreadystatechange = function() {
				// 	if (this.readyState == 4 && this.status == 200) {
				// 		$('#msg').html(this.responseText);	
				// 	}
				// };

				// ajax.open("POST", "profileChange/updatename.php");
				// ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				// ajax.send(`newName=${$('#TextNewName').val()}`);
			}
		});
	});

	// === Изменение пароля
	$('#passwordChange').on('click', function() {
		$('#msg').html('<div class="rowText">Old password: <input id="TextOldPassword" type="password"></input></div>' +
			'<div class="rowText">New password: <input id="TextNewPassword" type="password"></input></div>' + 
			'<div class="rowText">Repeat password: <input id="TextRepeatPassword" type="password"></input></div>' +
			'<input id="newPassword" class="update" value="Change" type="button"></input>');

		$('#newPassword').on('click', function() {
			if ($('#TextOldPassword').val().length == 0 ||
				$('#TextNewPassword').val().length == 0 ||
				$('#TextRepeatPassword').val().length == 0) {
				alert('The field is empty!');
			} else {

				let password = $('#TextOldPassword').val();
				let newPassword = $('#TextNewPassword').val();
				let repeatPassword = $('#TextRepeatPassword').val();

				$.post('profileDB.php', {
			    	password: password,
			    	newPassword: newPassword,
			    	repeatPassword: repeatPassword
			    }, function(data) {
			    	$('#msg').html(data);
			    });
			}
		});
	});

	// === Удаление аккаунта
	$('#delAcc').on('click', function() {
		$('#msg').html('<p style="color: #EB4C42;">When you delete your account, ' +
		'you will permanently lose access to it!</p>' + 
		'<div class="rowText">Enter password: <input id="EnterPassword" type="password"></input></div>' +
		'<input style="background:linear-gradient(#EA685F, #DA372C)" id="deleteAcc" class="update" value="DELETE ACCOUNT" type="button"></input>');
		
		$('#deleteAcc').on('click', function() {
			let password = $('#EnterPassword').val();

			$.post('profileDB.php', {
				deleteAccount: true,
				password: password
			}, function(data) {
				if (data) {
					$('#msg').html(data);
				} else {
					location.reload();
				}
			});
		});
	});

	// === Чат с администратором

	// Обработчки нажатия на чекбокс
	$('#admin_chat').change(function() {
		if ($(this).is(':checked')) {
			$('.field_for_message').css('display', 'block');
			
			let user_id = $('#myID').html();

			// Загружаем чат с администратором
			$.post('profileDB.php',{
		    	loadChat: true
		    }, function(data) {
		       	$('.chat-container').append(data);
		       	$('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
		    });
		} else {
			$('.field_for_message').css('display', 'none');
			//clearInterval(ajaxPush);
		}
	});

	// Отправка сообщения
	$('#btnSendMsg').on('click', function() {

		if ($('#text-to-send').val() != '') {
			let sendText = $('#text-to-send').val();

			$.post('profileDB.php',{
		    	sentMsg: true,
		    	sendText: sendText
		    }, function(data) {
		       	$('.chat-container').append(data);
		       	$('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
		    });

			$('#text-to-send').val('');
		}
	});

	ajaxPush = setInterval(function() {
    	$.post('profileDB.php',{
    		getNewMsg: true
    	}, function(data) {
        	if (data && !$('#admin_chat').is(':checked')) {
        		localStorage.setItem(`${user_id} newMsg`, 1);
			}
			else if ($('#admin_chat').is(':checked')) {
				localStorage.setItem(`${user_id} newMsg`, 0);
				if (data) {
					$('.chat-container').append(data);
	        		$('.chat-container').scrollTop($('.chat-container').prop('scrollHeight'));
				}
			}

			if (localStorage.getItem(`${user_id} newMsg`) == 1) {
				$('.check-newMsg').css('display', 'block');
			} else {
				$('.check-newMsg').css('display', 'none');
			}
			//console.log(getItem(`${user_id} newMsg`);
    	});
	}, 1000);
});