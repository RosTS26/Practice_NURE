-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 27 2023 г., 02:16
-- Версия сервера: 8.0.30
-- Версия PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `dbforpractice`
--

-- --------------------------------------------------------

--
-- Структура таблицы `friendly_chat`
--

CREATE TABLE `friendly_chat` (
  `chat_id` int NOT NULL,
  `user_id` int NOT NULL,
  `friend_id` int NOT NULL,
  `chat` json NOT NULL,
  `newMsg` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `friendly_chat`
--

INSERT INTO `friendly_chat` (`chat_id`, `user_id`, `friend_id`, `chat`, `newMsg`) VALUES
(1, 14, 17, '[{\"id\": \"17\", \"msg\": \"123\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"123\\n\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"312\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qwe\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qwe\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qewq\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qweq\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"eqwe\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qew\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"Hohoh\", \"name\": \"Bob\", \"time\": \"01:02\"}, {\"id\": \"14\", \"msg\": \"cha\", \"name\": \"test\", \"time\": \"01:02\"}, {\"id\": \"17\", \"msg\": \"dqw\", \"name\": \"Bob\", \"time\": \"01:02\"}, {\"id\": \"14\", \"msg\": \"123\", \"name\": \"test\", \"time\": \"01:03\"}, {\"id\": \"14\", \"msg\": \"qwd\", \"name\": \"test\", \"time\": \"01:09\"}, {\"id\": \"14\", \"msg\": \"qwdqwd\", \"name\": \"test\", \"time\": \"01:09\"}, {\"id\": \"14\", \"msg\": \"qdwq\", \"name\": \"test\", \"time\": \"01:09\"}, {\"id\": \"14\", \"msg\": \"cdsv\", \"name\": \"test\", \"time\": \"01:11\"}, {\"id\": \"14\", \"msg\": \"vfvd\", \"name\": \"test\", \"time\": \"01:11\"}, {\"id\": \"14\", \"msg\": \"dcdc\", \"name\": \"test\", \"time\": \"01:13\"}]', '[]'),
(2, 17, 14, '[{\"id\": \"17\", \"msg\": \"123\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"123\\n\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"312\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qwe\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qwe\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qewq\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qweq\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"eqwe\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"qew\", \"name\": \"Bob\", \"time\": \"01:01\"}, {\"id\": \"17\", \"msg\": \"Hohoh\", \"name\": \"Bob\", \"time\": \"01:02\"}, {\"id\": \"14\", \"msg\": \"cha\", \"name\": \"test\", \"time\": \"01:02\"}, {\"id\": \"17\", \"msg\": \"dqw\", \"name\": \"Bob\", \"time\": \"01:02\"}, {\"id\": \"14\", \"msg\": \"123\", \"name\": \"test\", \"time\": \"01:03\"}, {\"id\": \"14\", \"msg\": \"qwd\", \"name\": \"test\", \"time\": \"01:09\"}, {\"id\": \"14\", \"msg\": \"qwdqwd\", \"name\": \"test\", \"time\": \"01:09\"}, {\"id\": \"14\", \"msg\": \"qdwq\", \"name\": \"test\", \"time\": \"01:09\"}, {\"id\": \"14\", \"msg\": \"cdsv\", \"name\": \"test\", \"time\": \"01:11\"}, {\"id\": \"14\", \"msg\": \"vfvd\", \"name\": \"test\", \"time\": \"01:11\"}, {\"id\": \"14\", \"msg\": \"dcdc\", \"name\": \"test\", \"time\": \"01:13\"}]', '[]'),
(3, 15, 17, '[]', '[]'),
(4, 17, 15, '[]', '[]'),
(5, 19, 15, '[]', '[]'),
(6, 15, 19, '[]', '[]'),
(7, 14, 19, '[{\"id\": \"19\", \"msg\": \"haaa\", \"name\": \"test3\", \"time\": \"02:16\"}]', '[]'),
(8, 19, 14, '[{\"id\": \"19\", \"msg\": \"haaa\", \"name\": \"test3\", \"time\": \"02:16\"}]', '[]');

-- --------------------------------------------------------

--
-- Структура таблицы `roulette`
--

CREATE TABLE `roulette` (
  `ID_roulette` int NOT NULL,
  `deposit` int NOT NULL DEFAULT '1000',
  `num_of_games` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `roulette`
--

INSERT INTO `roulette` (`ID_roulette`, `deposit`, `num_of_games`) VALUES
(1, 1000, 0),
(14, 19200, 13),
(15, 13424, 8),
(17, 4240, 2),
(18, 1000, 0),
(19, 1000, 0),
(22, 1000, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `snake`
--

CREATE TABLE `snake` (
  `ID_snake` int NOT NULL,
  `topScore` int NOT NULL DEFAULT '0',
  `num_of_games` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `snake`
--

INSERT INTO `snake` (`ID_snake`, `topScore`, `num_of_games`) VALUES
(1, 1, 1),
(14, 17, 68),
(15, 39, 2),
(17, 19, 4),
(18, 0, 0),
(19, 0, 0),
(22, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `tetris`
--

CREATE TABLE `tetris` (
  `ID_tetris` int NOT NULL,
  `topScore` int NOT NULL DEFAULT '0',
  `num_of_games` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tetris`
--

INSERT INTO `tetris` (`ID_tetris`, `topScore`, `num_of_games`) VALUES
(1, 0, 0),
(14, 100, 31),
(15, 0, 0),
(17, 50, 2),
(18, 0, 0),
(19, 0, 0),
(22, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `username`, `password`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e'),
(14, 'test', 'e10adc3949ba59abbe56e057f20f883e'),
(15, 'aboba', 'e10adc3949ba59abbe56e057f20f883e'),
(17, 'Bob', 'e10adc3949ba59abbe56e057f20f883e'),
(18, 'test2', 'e10adc3949ba59abbe56e057f20f883e'),
(19, 'test3', 'e10adc3949ba59abbe56e057f20f883e'),
(22, 'test4', 'e10adc3949ba59abbe56e057f20f883e');

-- --------------------------------------------------------

--
-- Структура таблицы `user_friends`
--

CREATE TABLE `user_friends` (
  `user_id` int NOT NULL,
  `friends` json NOT NULL,
  `sent_app` json NOT NULL,
  `incoming_app` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user_friends`
--

INSERT INTO `user_friends` (`user_id`, `friends`, `sent_app`, `incoming_app`) VALUES
(1, '[]', '[]', '[]'),
(14, '[{\"id\": \"17\", \"username\": \"bob\"}, {\"id\": \"19\", \"username\": \"test3\"}]', '[]', '[]'),
(15, '[{\"id\": \"17\", \"username\": \"Bob\"}, {\"id\": \"19\", \"username\": \"test3\"}]', '[]', '[]'),
(17, '[{\"id\": \"14\", \"username\": \"test\"}, {\"id\": \"15\", \"username\": \"aboba\"}]', '[]', '[]'),
(18, '[]', '[]', '[]'),
(19, '[{\"id\": \"15\", \"username\": \"aboba\"}, {\"id\": \"14\", \"username\": \"test\"}]', '[]', '[]'),
(22, '[]', '[]', '[]');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `friendly_chat`
--
ALTER TABLE `friendly_chat`
  ADD PRIMARY KEY (`chat_id`);

--
-- Индексы таблицы `roulette`
--
ALTER TABLE `roulette`
  ADD PRIMARY KEY (`ID_roulette`);

--
-- Индексы таблицы `snake`
--
ALTER TABLE `snake`
  ADD PRIMARY KEY (`ID_snake`);

--
-- Индексы таблицы `tetris`
--
ALTER TABLE `tetris`
  ADD PRIMARY KEY (`ID_tetris`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user_friends`
--
ALTER TABLE `user_friends`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `friendly_chat`
--
ALTER TABLE `friendly_chat`
  MODIFY `chat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
