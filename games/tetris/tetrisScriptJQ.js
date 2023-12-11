let ajax = new XMLHttpRequest();

// Генерируем поле для игры с классом "Tetris"
let tetris = $('<div></div>');
tetris.addClass('tetris');

// 140 ячеек на поле (+40 за пределами поля для спана фигур)
for (let i = 0; i < 180; i++) {
	let excel = $('<div></div>');
	excel.addClass('excel');
	tetris.append(excel); // Добавляем дочерний элемент 
}

// "Рисуем" поле для игры путем добавления элемента "tetris"
let main = $('.main').first();
main.append(tetris);

// Присваеваем каждой ячейке координаты Х и У
let i = 0;
for (let y = 17; y >= 0; y--) {
	for (let x = 0; x < 10; x++) {
		$('.excel').eq(i).attr('posX', x);
		$('.excel').eq(i).attr('posY', y);
		i++;
	}
}

let interval; // Покадровая отрисовка
let x = 5, y = 14; // Начальные координаты фигур
// Массив с названиями всех фигур
let imgFigures = ["straight.png", "square.png", "figS.png",
				"figZ.png", "figL.png", "figJ.png",
				"figT.png", "iconSpawn.png", "loser.png"]; 
// Геометрия всех фигурок
let mainArr = [
	// Прямая
	[
		[0,1], [0,2], [0,3]
	],
	// Квадрат
	[
		[1,0], [0,1], [1,1]
	],
	// Фигура "S"
	[
		[1,0], [1,1], [2,1]
	],
	// Фигура "Z"
	[
		[1,0], [-1,1], [0,1]
	],
	// Фигура "L"
	[
		[1,0], [0,1], [0,2]
	],
	// Фигура "J"
	[
		[1,0], [1,1], [1,2]
	],
	// Фигура "T"
	[
		[1,0], [2,0], [1,1]
	],
];

let tegImg = $("#img");
tegImg.attr('src', `image/${imgFigures[7]}`);

// Выбор рандомной фигурки
function getRandomFig() {
	return Math.floor(Math.random() * mainArr.length);
	// return 0;
}

let indexRandFig = getRandomFig(); 	// Индекс рандомной фигуры 
let figureBody = 0;	// Тело фигуры
let score = 0; // Очки
$("#score").attr('value', `Score: ${score}`);

// Инициализация фигуры на поле
function Create() {

	// Функция проигрыша
	function GameOver() {
		figureBody.forEach(item => {
			if (item.hasClass('set')) {
				let scoreMsg = score;
				clearInterval(interval);
				// Выбираем все элементы с классом 'set' и удаляем этот класс
				$('.set').removeClass('set')

				ajax.onreadystatechange = Result;
				ajax.open("POST", "updatedb.php");
				ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				ajax.send(`score=${score}`);

				function Result() {
					if (ajax.readyState == 4) {
						if (ajax.status == 200) {
							$('#msgInfo').html(`Your score: ${scoreMsg}<br>${ajax.responseText}`);
							$('#msgInfo').css('display', 'block');
							tegImg.attr('src', `image/${imgFigures[8]}`);
						} else alert("Error AJAX");
					}
				}
				score = 0;
				$('#btnStart').prop('disabled', false);
			}
		});
	}

	// Присваиваем каждой ячейке начальные координаты Х У
	figureBody = [
		$(`[posX = "${x}"][posY = "${y}"]`),
		$(`[posX = "${x + mainArr[indexRandFig][0][0]}"][posY = "${y + mainArr[indexRandFig][0][1]}"]`),
		$(`[posX = "${x + mainArr[indexRandFig][1][0]}"][posY = "${y + mainArr[indexRandFig][1][1]}"]`),
		$(`[posX = "${x + mainArr[indexRandFig][2][0]}"][posY = "${y + mainArr[indexRandFig][2][1]}"]`),
	];

	// Проверяем на проигрыш
	GameOver();

	// Добавляемкаждой ячейке класс 'figure'
	figureBody.forEach(function(item) {
		item.addClass('figure');
	});

	// Выбираем следующую фигуру
	indexRandFig = getRandomFig();
	tegImg.attr('src', `image/${imgFigures[indexRandFig]}`);
}

// Падение фигурки
function Move() {
	let moveFlag = true; // Флаг для завершения падения
	let coords = [
		[figureBody[0].attr('posX'), figureBody[0].attr('posY')],
		[figureBody[1].attr('posX'), figureBody[1].attr('posY')],
		[figureBody[2].attr('posX'), figureBody[2].attr('posY')],
		[figureBody[3].attr('posX'), figureBody[3].attr('posY')]
	];

	// Если один из элементов фигуры с координатой У = 1 ИЛИ
	// У = нижняя координата У с классом "set", заканчиваем падение 
	coords.forEach(function(item) {
		if (item[1] == 0 || 
			$(`[posX = "${item[0]}"][posY = "${item[1] - 1}"]`).hasClass('set')) {
			moveFlag = false;
		}
	});

	if (moveFlag) {
		// Удаляем класс "figure"
		figureBody.forEach(function(item) {
			item.removeClass('figure');
		});
		// Меняем координту У кля каждой ячейки фигуры
		figureBody = [
			$(`[posX = "${coords[0][0]}"][posY = "${coords[0][1] - 1}"]`),
			$(`[posX = "${coords[1][0]}"][posY = "${coords[1][1] - 1}"]`),
			$(`[posX = "${coords[2][0]}"][posY = "${coords[2][1] - 1}"]`),
			$(`[posX = "${coords[3][0]}"][posY = "${coords[3][1] - 1}"]`),
		]
		// Добавляем обратно класс "figure"
		figureBody.forEach(function(item) {
			item.addClass('figure');
		});
	} else {
		// Меняем класс "figure" на класс "set"
		figureBody.forEach(function(item) {
			item.removeClass('figure');
			item.addClass('set');
		});

		// Очистка ряда в случае заполнения
		for (let i = 0; i < 14; i++) {
			let count = 0;
			for (let j = 0; j < 10; j++) {
				if ($(`[posX = "${j}"][posY = "${i}"]`).hasClass('set')) {
					count++;
					if (count == 10) {
						// Добавляем очки и выводим их на экран
						score += 10;
						$("#score").attr('value', `Score: ${score}`);

						for (let j2 = 0; j2 < 10; j2++) {
							$(`[posX = "${j2}"][posY = "${i}"]`).removeClass('set')
						}

						let newSet = []; // Массив для смещеных фигур вниз

						// Проходим по элементам с классом "set"
						$('.set').each(function() {
							let setCoords = [$(this).attr('posX'), $(this).attr('posY')];
							// Проверка, если ряды (элементы) находятся сверху
							if (+setCoords[1] > i) {
								$(this).removeClass('set');
								// Добавляем в новый массив координаты верхних фигур, при --У
								newSet.push($(`[posX = "${setCoords[0]}"][posY = "${setCoords[1]-1}"]`));
							}
						});

						newSet.forEach(item => {
							item.addClass('set');
						});

						i-- // Инкремент i, т.к. было удаление ряда фигур 
					}
				}
			}

		}
		Create();	// Создание новой фигуры
	}
}

// ======================================================================

// Нажатие на клавиши клавиатуры
$(document).on('keydown', function(e) {
	if ($("#btnStart").prop('disabled')) {
		let coords = [
			[figureBody[0].attr('posX'), figureBody[0].attr('posY')],
			[figureBody[1].attr('posX'), figureBody[1].attr('posY')],
			[figureBody[2].attr('posX'), figureBody[2].attr('posY')],
			[figureBody[3].attr('posX'), figureBody[3].attr('posY')]
		];

		function getNewState(a) {

			let flag = true;

			let figureNew = [
				$(`[posX = "${+coords[0][0] + a}"][posY = "${coords[0][1]}"]`),
				$(`[posX = "${+coords[1][0] + a}"][posY = "${coords[1][1]}"]`),
				$(`[posX = "${+coords[2][0] + a}"][posY = "${coords[2][1]}"]`),
				$(`[posX = "${+coords[3][0] + a}"][posY = "${coords[3][1]}"]`),
			];

			figureNew.forEach(item => {
				if (!item.attr('posX') || item.hasClass('set')) flag = false;
			});

			if (flag) {
				figureBody.forEach(function(item) {
					item.removeClass('figure');
				});

				figureBody = figureNew;

				figureBody.forEach(function(item) {
					item.addClass('figure');
				});
			}
		}

		// Вращение фигурки
		function RollFig() {

			let flag = true;

			// Центральный блок оси вращения
			let centerX = +coords[1][0];
			let centerY = +coords[1][1];

			coords.forEach(item => {
				let newX = item[0] - centerX;
				let newY = item[1] - centerY;
				item[0] = centerX + newY;
				item[1] = centerY - newX; 
			});

			figureNew = [
				$(`[posX = "${coords[0][0]}"][posY = "${coords[0][1]}"]`),
				$(`[posX = "${coords[1][0]}"][posY = "${coords[1][1]}"]`),
				$(`[posX = "${coords[2][0]}"][posY = "${coords[2][1]}"]`),
				$(`[posX = "${coords[3][0]}"][posY = "${coords[3][1]}"]`),
			];

			figureNew.forEach(item => {
				if (!item.attr('posX') || item.hasClass('set')) flag = false;
			});

			if (flag) {
				figureBody.forEach(function(item) {
					item.removeClass('figure');
				});

				figureBody = figureNew;

				figureBody.forEach(function(item) {
					item.addClass('figure');
				});
			}
		}

		if (e.keyCode == 37) getNewState(-1); // Нажатие на стрелку влево
		else if (e.keyCode == 39) getNewState(1); // Стрелка вправо
		else if (e.keyCode == 40) Move(); // Стрелка вниз (ускорение падения)
		else if (e.keyCode == 38) RollFig(); // Вращение фигурки
	}

});

let msgInfo = $('#msgInfo');
$('#btnStart').on("click", function() {
	$("#btnStart").prop('disabled', true);
	msgInfo.css('display', 'none');
	Create();
	// Вызов функции падения с интервалом 300 мс
	interval = setInterval(() => Move(), 300);
});