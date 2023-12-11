// "use strict"
let ajax = new XMLHttpRequest();

const canvas = document.getElementById("game");
const ctx = canvas.getContext("2d");

// Загружаем текстуру поля
const ground = new Image();
ground.onload = function() {
	ctx.drawImage(ground, 0, 0);
	ctx.fillStyle = "blue";
	ctx.font = "50px Arial";
	ctx.fillText("Snake!", box * 7, box * 10);
	ctx.fillText('Press "Start"', box * 5, box * 12);
}
ground.src = "image/ground.png";

// Загружаем текстуру еды (яблоко)
const foodImg = new Image();
foodImg.src = "image/food.png";

let topScore;
let rendering;
let box = 32; 			// Размер ячейки
let score = 0; 			// Кол-во очков
let speed = 150; 		// Скорость игры (обновление draw в мс)

// Спавн еды на поле
let food = {
	x: Math.floor((Math.random() * 17 + 1)) * box,
	y: Math.floor((Math.random() * 15 + 3)) * box,
};

// Начальный спавн змейки
let snake = [];
snake[0] = {
	x: 9 * box,
	y: 10 * box
};

document.addEventListener("keydown", direction); // работа с клавиатурой

let dir; // переменная для клавиш

// Считывания нажатия клавиш
function direction(event) {
	if(event.keyCode == 37 && dir != "right") {
		dir = "left";
	} else if(event.keyCode == 38 && dir != "down") {
		dir = "up";
	} else if(event.keyCode == 39 && dir != "left") {
		dir = "right";
	} else if(event.keyCode == 40 && dir != "up") {
		dir = "down";
	}
}

// Проигрыш в случае, если змейка съест свой хвост
function eatTail(head, arrSnake) {
	for(let i = 1; i < arrSnake.length; i++) {
		if(head.x == arrSnake[i].x && head.y == arrSnake[i].y) {
			gameOver();
			break;
		}
	}
}

// Код проигрыша (Удар об стену)
function hitWall (x, y) {
	if(x < box || 
	   x > box * 17 || 
	   y < box * 3 || 
	   y > box * 17) {
		gameOver();
	}
}

function gameOver() {
	clearInterval(rendering);
	document.getElementById("btnStart").disabled = false;
	document.getElementById("btnStart").value = "Restart";
	ctx.fillStyle = "blue";
	ctx.fillText("Game Over :(", box * 5, box * 10);
	ctx.fillText(`Score: ${score}`, box * 6.5, box * 12);

	ajax.onreadystatechange = Result;
	ajax.open("POST", "updatedb.php");
	ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajax.send(`score=${score}`);

	function Result() {
		if (ajax.readyState == 4) {
			if (ajax.status == 200) {
				ctx.fillText(ajax.responseText, box * 5, box * 14);
			} else alert("Error AJAX");
		}
	}
	
	score = 0;
	snake = [];
	snake[0] = {
		x: 9 * box,
		y: 10 * box
	}
}

// функция игры
function draw() {
	// Рисуем поле и еду
	ctx.drawImage(ground, 0, 0);
	ctx.drawImage(foodImg, food.x, food.y);

	// Цвет змейки и ее расположение
	for(let i = 0; i < snake.length; i++) {
		// Четный элемент - черный, нечетный - красный 
		ctx.fillStyle = i == 0 ? "#B61063" : i % 2 == 0 ? "#FE1B2D" : "#E11425";
		ctx.fillRect(snake[i].x, snake[i].y, box, box);	// Рисуем квадрат элемента змейки
	}

	let snakeX = snake[0].x;
	let snakeY = snake[0].y;

	// Если змейка кушает еду
	if(snakeX == food.x && snakeY == food.y) {
		score++;
		food = {
			x: Math.floor((Math.random() * 17 + 1)) * box,
			y: Math.floor((Math.random() * 15 + 3)) * box,
		}

		// Проверка спавна еды
		let iteration = 0;
		let flag = true

		while(flag) {
			flag = false;
			if (iteration > 10) {
				// Спавним еду в хвосте, если рандом дает задержку
				food = {
					x: snake.slice(-1)[0].x,
					y: snake.slice(-1)[0].y
				}
				flag = false;
			} else {
				for(i = 0; i < snake.length; i++) {
					// Переспавниваем еду рандомно, если она оказалась внутри змейки
					if (food.x == snake[i].x && food.y == snake[i].y) {
						food = {
							x: Math.floor((Math.random() * 17 + 1)) * box,
							y: Math.floor((Math.random() * 15 + 3)) * box
						};
						flag = true;
						iteration++;
						break;
					}
				}
			}
		}
	} else snake.pop();

	// Надпись очков
	ctx.fillStyle = "white";
	ctx.fillText(":", box * 1.9, box * 1.6);
	ctx.fillText(score, box * 2.5, box * 1.7);

	// Направление движения змейки
	if(dir == "left") snakeX -= box;
	if(dir == "right") snakeX += box;
	if(dir == "up") snakeY -= box;
	if(dir == "down") snakeY += box;

	let newHead = {
		x: snakeX,
		y: snakeY
	};
	
	snake.unshift(newHead);		// Добавляем новый элемент змейки (голову) в начало массива элементов
	hitWall (snake[0].x, snake[0].y);
	eatTail(snake[0], snake);
}

function game() {
	// Вызов функции игры с интервалом speed мс
	rendering = setInterval(() => draw(), speed);
}

// Кнопка запуска игры
let btnStart = document.querySelector("#btnStart");
btnStart.addEventListener("click", function() {
	document.getElementById("btnStart").disabled = true; 
	game(); 
});