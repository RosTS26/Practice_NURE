let ajax = new XMLHttpRequest();

// Генерируем поле для игры с классом "Tetris"
let tetris = document.createElement('div');
tetris.classList.add('tetris');

// 140 ячеек на поле (+40 за пределами поля для спана фигур)
for (let i = 0; i < 180; i++) {
	let excel = document.createElement('div');
	excel.classList.add('excel');
	tetris.appendChild(excel); // Добавляем дочерний элемент 
}

// "Рисуем" поле для игры путем добавления элемента "tetris"
let main = document.getElementsByClassName('main')[0];
main.appendChild(tetris);

let excel = document.getElementsByClassName('excel');
let i = 0;

// Присваеваем каждой ячейке координаты Х и У
for (let y = 17; y >= 0; y--) {
	for (let x = 0; x < 10; x++) {
		excel[i].setAttribute('posX', x);
		excel[i].setAttribute('posY', y);
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

let tegImg = document.getElementById("img");
tegImg.src = `image/${imgFigures[7]}`;

// Выбор рандомной фигурки
function getRandomFig() {
	return Math.floor(Math.random() * mainArr.length);
	// return 0;
}

let indexRandFig = getRandomFig(); 	// Индекс рандомной фигуры 
let figureBody = 0;	// Тело фигуры
let score = 0; // Очки
document.getElementById("score").value = `Score: ${score}`;

// Инициализация фигуры на поле
function Create() {

	// Функция проигрыша
	function GameOver() {
		figureBody.forEach(item => {
			if (item.classList.contains('set')) {
				let scoreMsg = score;
				clearInterval(interval);
				// Выбираем все элементы с классом 'set' и удаляем этот класс
				removeSet = document.querySelectorAll('.set')
				removeSet.forEach(item => {
					item.classList.remove('set');
				});

				ajax.onreadystatechange = Result;
				ajax.open("POST", "updatedb.php");
				ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				ajax.send(`score=${score}`);

				function Result() {
					if (ajax.readyState == 4) {
						if (ajax.status == 200) {
							msgInfo.innerHTML = `Your score: ${scoreMsg}<br>${ajax.responseText}`;
							msgInfo.style.display = "block";
							img.src = `image/${imgFigures[8]}`;
						} else alert("Error AJAX");
					}
				}
				score = 0;
				document.getElementById("btnStart").disabled = false;
			}
		});
	}

	// Присваиваем каждой ячейке начальные координаты Х У
	figureBody = [
		document.querySelector(`[posX = "${x}"][posY = "${y}"]`),
		document.querySelector(`[posX = "${x + mainArr[indexRandFig][0][0]}"][posY = "${y + mainArr[indexRandFig][0][1]}"]`),
		document.querySelector(`[posX = "${x + mainArr[indexRandFig][1][0]}"][posY = "${y + mainArr[indexRandFig][1][1]}"]`),
		document.querySelector(`[posX = "${x + mainArr[indexRandFig][2][0]}"][posY = "${y + mainArr[indexRandFig][2][1]}"]`),
	];

	// Проверяем на проигрыш
	GameOver();

	// Добавляемкаждой ячейке класс 'figure'
	figureBody.forEach(function(item) {
		item.classList.add('figure');
	});

	// Выбираем следующую фигуру
	indexRandFig = getRandomFig();
	tegImg.src = `image/${imgFigures[indexRandFig]}`;
}

// Падение фигурки
function Move() {
	let moveFlag = true; // Флаг для завершения падения
	let coords = [
		[figureBody[0].getAttribute('posX'), figureBody[0].getAttribute('posY')],
		[figureBody[1].getAttribute('posX'), figureBody[1].getAttribute('posY')],
		[figureBody[2].getAttribute('posX'), figureBody[2].getAttribute('posY')],
		[figureBody[3].getAttribute('posX'), figureBody[3].getAttribute('posY')]
	];

	// Если один из элементов фигуры с координатой У = 1 ИЛИ
	// У = координа У с классом "set", заканчиваем падение 
	coords.forEach(function(item) {
		if (item[1] == 0 || 
			document.querySelector(`[posX = "${item[0]}"][posY = "${item[1] - 1}"]`).classList.contains('set')) {
			moveFlag = false;
		}
	});

	if (moveFlag) {
		// Удаляем класс "figure"
		figureBody.forEach(function(item) {
			item.classList.remove('figure');
		});
		// Меняем координту У кля каждой ячейки фигуры
		figureBody = [
			document.querySelector(`[posX = "${coords[0][0]}"][posY = "${coords[0][1] - 1}"]`),
			document.querySelector(`[posX = "${coords[1][0]}"][posY = "${coords[1][1] - 1}"]`),
			document.querySelector(`[posX = "${coords[2][0]}"][posY = "${coords[2][1] - 1}"]`),
			document.querySelector(`[posX = "${coords[3][0]}"][posY = "${coords[3][1] - 1}"]`),
		]
		// Добавляем обратно класс "figure"
		figureBody.forEach(function(item) {
			item.classList.add('figure');
		});
	} else {
		// Меняем класс "figure" на класс "set"
		figureBody.forEach(function(item) {
			item.classList.remove('figure');
			item.classList.add('set');
		});

		// Очиста ряда в случае заполнения
		for (let i = 0; i < 14; i++) {
			let count = 0;
			for (let j = 0; j < 10; j++) {
				if (document.querySelector(`[posX = "${j}"][posY = "${i}"]`).classList.contains('set')) {
					count++;
					if (count == 10) {
						// Добавляем очки и выводим их на экран
						score += 10;
						document.getElementById("score").value = `Score: ${score}`;

						for (let j2 = 0; j2 < 10; j2++) {
							document.querySelector(`[posX = "${j2}"][posY = "${i}"]`).classList.remove('set')
						}

						// Записываем все элементы с классом "set"
						let allSet = document.querySelectorAll('.set');
						let newSet = []; // Массив для смещеных фигур вниз
						
						allSet.forEach(item => {
							let setCoords = [item.getAttribute('posX'), item.getAttribute('posY')];
							// Проверка, если ряды (элементы) находятся сверху
							if (+setCoords[1] > i) {
								item.classList.remove('set');
								// Добавляем в новый массив координаты верхних фигур, при --У
								newSet.push(document.querySelector(`[posX = "${setCoords[0]}"][posY = "${setCoords[1]-1}"]`));
							}
						});

						newSet.forEach(item => {
							item.classList.add('set');
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
document.addEventListener('keydown', function(e) {
	if (document.getElementById("btnStart").disabled) {
		let coords = [
			[figureBody[0].getAttribute('posX'), figureBody[0].getAttribute('posY')],
			[figureBody[1].getAttribute('posX'), figureBody[1].getAttribute('posY')],
			[figureBody[2].getAttribute('posX'), figureBody[2].getAttribute('posY')],
			[figureBody[3].getAttribute('posX'), figureBody[3].getAttribute('posY')]
		];

		function getNewState(a) {

			let flag = true;

			let figureNew = [
				document.querySelector(`[posX = "${+coords[0][0] + a}"][posY = "${coords[0][1]}"]`),
				document.querySelector(`[posX = "${+coords[1][0] + a}"][posY = "${coords[1][1]}"]`),
				document.querySelector(`[posX = "${+coords[2][0] + a}"][posY = "${coords[2][1]}"]`),
				document.querySelector(`[posX = "${+coords[3][0] + a}"][posY = "${coords[3][1]}"]`),
			];

			figureNew.forEach(item => {
				if (!item || item.classList.contains('set')) flag = false;
			});

			if (flag) {
				figureBody.forEach(function(item) {
					item.classList.remove('figure');
				});

				figureBody = figureNew;

				figureBody.forEach(function(item) {
					item.classList.add('figure');
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
				document.querySelector(`[posX = "${coords[0][0]}"][posY = "${coords[0][1]}"]`),
				document.querySelector(`[posX = "${coords[1][0]}"][posY = "${coords[1][1]}"]`),
				document.querySelector(`[posX = "${coords[2][0]}"][posY = "${coords[2][1]}"]`),
				document.querySelector(`[posX = "${coords[3][0]}"][posY = "${coords[3][1]}"]`),
			];

			figureNew.forEach(item => {
				if (!item || item.classList.contains('set')) flag = false;
			});

			if (flag) {
				figureBody.forEach(function(item) {
					item.classList.remove('figure');
				});

				figureBody = figureNew;

				figureBody.forEach(function(item) {
					item.classList.add('figure');
				});
			}
		}

		if (e.keyCode == 37) getNewState(-1); // Нажатие на стрелку влево
		else if (e.keyCode == 39) getNewState(1); // Стрелка вправо
		else if (e.keyCode == 40) Move(); // Стрелка вниз (ускорение падения)
		else if (e.keyCode == 38) RollFig(); // Вращение фигурки
	}

});

let msgInfo = document.getElementById('msgInfo');
let btnStart = document.querySelector("#btnStart");
btnStart.addEventListener("click", function() {
	document.getElementById("btnStart").disabled = true;
	msgInfo.style.display = "none";
	Create();
	// Вызов функции падения с интервалом 300 мс
	interval = setInterval(() => Move(), 300);
});