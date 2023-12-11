let ajax = new XMLHttpRequest();
let deposit;	// deposit игрока
ajax.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		deposit = this.responseText;
	}
}
ajax.open("POST", "getDeposit.php");
ajax.send();

// Класс фигур
class Figures {
	constructor(name, constX, constY) {
		this.name = name;
		this.constX = constX;
		this.constY = constY;
		this.coordY = 0;
		this.image = new Image();
		this.image.src = "image/figures/" + name + ".png";
		switch (this.name) {
			case "circle":
				this.price = 1;
				break;
			case "square":
				this.price = 5;
				break;
			case "triangle":
				this.price = 10;
				break;
			case "rhombus":
				this.price = 20;
				break;
			case "pentagon":
				this.price = 50;
				break;
			case "heart":
				this.price = 100;
				break;
			case "star4":
				this.price = 150;
				break;
			case "lightning":
				this.price = 200;
				break;
			case "star5":
				this.price = 300;
				break;
		}
	}

	/*renderingFigures() {
		ctx.fillStyle = "white";
		ctx.drawImage(ground, 0, 0, 700, 630);
		ctx.fillText("Выигрыш:", box, box * 2);
		//ctx.fillText(sum + "$", box * 4.5, box * 2);
		fallY += 5;

		if ((fallY > box * this.constY) &&
			(this.coordY + 5 < borderDownBox - box * this.constY)) {
				this.coordY += 5;
			}
		ctx.drawImage(this.image, (2 + box * (this.constX + 1)), (151 + this.coordY), 60, 55);
		
		ctx.fillStyle = "#578A34";
		ctx.fillRect(0, 151, 700, 58);
		if (fallY >= borderDownBox) {
			fallY = 0;
			clearInterval(rendering);
		}
	}*/
}

const canvas = document.getElementById("game");
const ctx = canvas.getContext("2d");
ctx.fillStyle = "white";
ctx.font = "50px Arial";

// Объявляем переменные и константы
let randFig = [];					// Двумерный массив с рандомными фигурами
let sum = 0;						// Сумма выигрыша
let minQuantityFig = 7;				// Минимальное кол-во фигур для подсчета
let fallY = 0;						// Падение фигурок (Y-координата)
const box = 70;						// Размер ячейки
const borderDownBox = box * 5 - 5;	// Нижняя граница поля
let rendering;						// Переменная для покадровой отрисовки
// Перечень досутпых фигур 
const thisFigures = ["circle", "square", "triangle", 
					"rhombus", "pentagon", "heart", 
					"star4", "lightning", "star5"];

function getText() {
	ctx.fillStyle = "white";
	ctx.font = "48px Arial";
	ctx.drawImage(ground, 0, 0, 870, 630);
	ctx.fillText("Deposit:", box, box);
	ctx.fillText(deposit + "$", box * 4, box);
	ctx.fillText("Win:", box, box * 2);
	ctx.fillText(sum + "$", box * 2.8, box * 2);
	ctx.fillStyle = "#4A752C";
	ctx.fillRect(520, 200, 320, 358);

	let figura;
	let img = new Image();
	ctx.fillStyle = "white";
	ctx.font = "36px Arial";
	for (let i = 0; i < thisFigures.length; i++) {
		img.src = "image/figures/" + thisFigures[i] + ".png"
		if (i > 4) {
			figura = new Figures(thisFigures[i], 0, 0);
			ctx.drawImage(img, 680, -135 + 70 * i, 60, 55);
			ctx.fillText(`${figura.price}$`, 750, -95 + 70 * i);	
		} else {
			figura = new Figures(thisFigures[i], 0, 0);
			ctx.drawImage(img, 530, 215 + 70 * i, 60, 55);
			ctx.fillText(`${figura.price}$`, 600, 255 + 70 * i);	
		}
	}

}

function randomMixFiguresOOP() {
	let randomFigures = new Array(6);
	for (let x = 0; x < 6; x ++) {
		randomFigures[x] = new Array(5);
		for (let y = 0; y < 5; y++) {
			let index = Math.floor(Math.random() * 9);
			randomFigures[x][y] = new Figures(thisFigures[index], x, y);
		}
	}
	return randomFigures;
}

// ==========================================================================================
// ==========================================================================================

const ground = new Image();		// Изображение поля
// Рисуем поле после загрузки изображения
setTimeout(function() {
	ground.onload = function() {
		getText();
		ctx.fillStyle = "#578A34";
		ctx.fillRect(0, 151, 870, 58);
	}
	ground.src = "image/ground2.png";	// Загружаем текстуру поля

	$(".msgInfo").css('display', 'none');
	$(".indexBox").css('display', 'block');
}, 1000);

// ==========================================================================================

// Функция покадровой отрисовки поля с фигурками 
function renderingFigures(figures) {
	getText();

	fallY += 5;

	figures.forEach((figuresY) => {
		figuresY.forEach((figura) => {
			if ((fallY > box * figura.constY || fallY < figura.coordY) &&
				(figura.coordY < borderDownBox - box * figura.constY)) {
				figura.coordY += 5;
			}
			ctx.drawImage(figura.image,
			(2 + box + box * figura.constX), 151 + figura.coordY, 60, 55);
		});
	});

	ctx.fillStyle = "#578A34";
	ctx.fillRect(0, 151, 870, 58);
	if (fallY >= borderDownBox) {
		fallY = 0;
		clearInterval(rendering);
	}
}

// Очистка поля от сыграного прокрута 
function removeOldFig(figures) {
	getText();

	fallY += 5;

	figures.forEach((figuresY) => {
		figuresY.forEach((figura) => {
			if (figura.coordY < 405) {
				figura.coordY += 5;
			}
			ctx.drawImage(figura.image,
			(2 + box + box * figura.constX), 151 + figura.coordY, 60, 55);
		});
	});

	ctx.fillStyle = "#578A34";
	ctx.fillRect(0, 556, 500, 68);
	ctx.fillRect(0, 151, 870, 58);
	if (fallY >= borderDownBox) {
		fallY = 0;
		clearInterval(rendering);
	}
}

// =========================================================================================

// Главная функция игры
async function game() {
	deposit -= 100;
	let thisSum = 0;

	if (randFig.length != 0) {
	 	rendering = setInterval(() => removeOldFig(randFig), 15);
	 	await new Promise(r => setTimeout(r, 1200));
	}

	randFig = randomMixFiguresOOP(); 	// Заполняем массив рандомными фигурами
	let spin = false;					// Условие для повторного прокрута

	rendering = setInterval(() => renderingFigures(randFig), 15);	// Вызов функции с интервалом 15мс
	await new Promise(r => setTimeout(r, 1200));					// Пауза на 1200мс

	// Главный цикл для прокрутки рулетки
	do {
		spin = false;
		// Массив для подсчета кол-ва фигур
		let NumFigures = new Map([
			["circle", 0], ["square", 0], ["triangle", 0],
			["rhombus", 0], ["pentagon", 0], ["heart", 0],
			["star4", 0], ["lightning", 0], ["star5", 0]
		]);

		// Подсчитываем кол-во отдельных фигур
		randFig.forEach((figuresY) => {
			figuresY.forEach((item) => {
				let num = NumFigures.get(item.name);
				NumFigures.set(item.name, ++num);
			});
		});

		// Флаг true для повторного прокрута если фигур больше 7
		for (let figura of NumFigures.keys()) {
			if (NumFigures.get(figura) >= minQuantityFig) {
				spin = true; // Флаг true для повторного прокрута
			}
		}

		if (spin) {
			// Подсвечиваем и удаляем фигуры которые "сыграли"
			for (let i = 0; i < 6; i++) {
				let y = 0;
				for (let j = 0; j < 5; j++) {
					// Если объект отсутвует, завершаем цикл
					if (randFig[i][j] === undefined) break;
					if (NumFigures.get(randFig[i][j].name) >= minQuantityFig) {
						// Рисуем прозрачные квадраты на сыграных фугарках
						ctx.fillStyle = "rgba(255,10,38,0.5)";
						ctx.fillRect((2 + box + box * i), 146 + (box * 5 - box * y), 60, 55);
						thisSum += randFig[i][j].price; // Добавляем цену фигурки к общей сумме
						sum += randFig[i][j].price;;
						randFig[i].splice(j, 1); // Удаляем фигуру
						j--;
					}
					y++;
				}
				// Присваиваем оставшимся фигурам новую constY
				for (newY = 0; newY < randFig[i].length; newY++) {
					randFig[i][newY].constY = newY;
				}
				// Добавляем новые фигуры в массив
				for (let newFig = randFig[i].length; newFig < 5; newFig++) {
					let index = Math.floor(Math.random() * 9);
					randFig[i].push(new Figures(thisFigures[index], i, newFig));
				}
			}
			// Пауза для подсветки
			await new Promise(r => setTimeout(r, 1200));
			// Новый прокрут
			rendering = setInterval(() => renderingFigures(randFig), 15);
			await new Promise(r => setTimeout(r, 1500));
		}
	} while(spin);

	ajax.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			deposit = this.responseText;
		}
	}
	ajax.open('POST', 'updatedb.php');
	ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajax.send(`sum=${thisSum - 100}`);

	setTimeout (() => {
		renderingFigures(randFig);
		sum = 0;
		$('#btnSpin').prop('disabled', false);
	}, 1000);
}

// ===============================================================================

// Кнопка запуска рулетки (вызов главной функции игры)
$("#btnSpin").on("click", function() {
	$('#btnSpin').prop('disabled', true); // Отключаем кнопку
	if (Number(deposit) < 100) {
		alert("Недостаточно средств на балансе!");
	} else {
		game();
	}
});