-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 25 2023 г., 23:38
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
-- База данных: `dbadminpanel`
--

-- --------------------------------------------------------

--
-- Структура таблицы `users_ban`
--

CREATE TABLE `users_ban` (
  `user_id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cause` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users_ban`
--

INSERT INTO `users_ban` (`user_id`, `username`, `cause`, `start_date`, `end_date`) VALUES
(14, 'test', 'test unban', '2023-05-17 21:59:49', '2023-05-17 22:13:30'),
(14, 'test', 'ban', '2023-05-17 22:13:30', '2023-05-17 22:13:30'),
(15, 'aboba', 'test', '2023-05-17 22:18:17', '2023-05-17 22:18:17');

-- --------------------------------------------------------

--
-- Структура таблицы `users_chat`
--

CREATE TABLE `users_chat` (
  `user_id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `json_chat` json NOT NULL,
  `newMsg` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users_chat`
--

INSERT INTO `users_chat` (`user_id`, `username`, `json_chat`, `newMsg`) VALUES
(14, 'test', '[{\"id\": \"14\", \"msg\": \"test\", \"name\": \"test\", \"time\": \"00:36\"}, {\"id\": \"14\", \"msg\": \"test2\", \"name\": \"test\", \"time\": \"00:37\"}, {\"id\": \"14\", \"msg\": \"123\", \"name\": \"test\", \"time\": \"00:37\"}, {\"id\": \"14\", \"msg\": \"555\", \"name\": \"test\", \"time\": \"00:37\"}, {\"id\": \"14\", \"msg\": \"jojojo\", \"name\": \"test\", \"time\": \"00:41\"}, {\"id\": \"14\", \"msg\": \"test\", \"name\": \"test\", \"time\": \"00:41\"}, {\"id\": \"14\", \"msg\": \"9876\", \"name\": \"test\", \"time\": \"00:43\"}, {\"id\": \"14\", \"msg\": \"jopa\", \"name\": \"test\", \"time\": \"00:44\"}, {\"id\": \"1\", \"msg\": \"123455\", \"name\": \"admin\", \"time\": \"00:44\"}, {\"id\": \"1\", \"msg\": \"12345566\", \"name\": \"admin\", \"time\": \"00:44\"}, {\"id\": \"1\", \"msg\": \"123\", \"name\": \"admin\", \"time\": \"02:26\"}, {\"id\": \"1\", \"msg\": \"test2\", \"name\": \"admin\", \"time\": \"02:28\"}]', '[]'),
(17, 'Bob', '[{\"id\": \"17\", \"msg\": \"test\", \"name\": \"Bob\", \"time\": \"21:25\"}, {\"id\": \"17\", \"msg\": \"hohoho\", \"name\": \"Bob\", \"time\": \"21:31\"}]', '[{\"id\": \"17\", \"msg\": \"test\", \"name\": \"Bob\", \"time\": \"21:25\"}, {\"id\": \"17\", \"msg\": \"hohoho\", \"name\": \"Bob\", \"time\": \"21:31\"}]'),
(18, 'test2', '[{\"id\": \"18\", \"msg\": \"hello\\n\", \"name\": \"test2\", \"time\": \"00:45\"}, {\"id\": \"1\", \"msg\": \"ky bro\", \"name\": \"admin\", \"time\": \"00:45\"}, {\"id\": \"18\", \"msg\": \"aoaooa\", \"name\": \"test2\", \"time\": \"00:45\"}, {\"id\": \"1\", \"msg\": \"test\", \"name\": \"admin\", \"time\": \"01:27\"}, {\"id\": \"1\", \"msg\": \"test\", \"name\": \"admin\", \"time\": \"01:29\"}, {\"id\": \"1\", \"msg\": \"1234\", \"name\": \"admin\", \"time\": \"01:30\"}, {\"id\": \"1\", \"msg\": \"hello bro\", \"name\": \"admin\", \"time\": \"01:47\"}, {\"id\": \"1\", \"msg\": \"aooo\", \"name\": \"admin\", \"time\": \"01:47\"}, {\"id\": \"1\", \"msg\": \"123\", \"name\": \"admin\", \"time\": \"01:52\"}, {\"id\": \"1\", \"msg\": \"1234\", \"name\": \"admin\", \"time\": \"01:53\"}, {\"id\": \"1\", \"msg\": \"wqd\", \"name\": \"admin\", \"time\": \"01:54\"}, {\"id\": \"1\", \"msg\": \"aoaoa\", \"name\": \"admin\", \"time\": \"01:55\"}, {\"id\": \"1\", \"msg\": \"123\", \"name\": \"admin\", \"time\": \"01:56\"}, {\"id\": \"1\", \"msg\": \"halo halo\", \"name\": \"admin\", \"time\": \"01:57\"}, {\"id\": \"1\", \"msg\": \"oaoa\", \"name\": \"admin\", \"time\": \"02:04\"}, {\"id\": \"1\", \"msg\": \"test\", \"name\": \"admin\", \"time\": \"02:07\"}, {\"id\": \"1\", \"msg\": \"123\", \"name\": \"admin\", \"time\": \"02:09\"}, {\"id\": \"1\", \"msg\": \"123\", \"name\": \"admin\", \"time\": \"02:14\"}, {\"id\": \"1\", \"msg\": \"haohao\", \"name\": \"admin\", \"time\": \"02:14\"}, {\"id\": \"18\", \"msg\": \"Boobob\", \"name\": \"test2\", \"time\": \"02:16\"}, {\"id\": \"1\", \"msg\": \"cool\", \"name\": \"admin\", \"time\": \"02:16\"}, {\"id\": \"1\", \"msg\": \"Hey\", \"name\": \"admin\", \"time\": \"02:16\"}, {\"id\": \"1\", \"msg\": \"halo\", \"name\": \"admin\", \"time\": \"02:16\"}, {\"id\": \"1\", \"msg\": \"hello\", \"name\": \"admin\", \"time\": \"02:17\"}, {\"id\": \"1\", \"msg\": \"123\", \"name\": \"admin\", \"time\": \"02:20\"}, {\"id\": \"1\", \"msg\": \"test2\", \"name\": \"admin\", \"time\": \"02:26\"}]', '[]');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `users_ban`
--
ALTER TABLE `users_ban`
  ADD PRIMARY KEY (`start_date`);

--
-- Индексы таблицы `users_chat`
--
ALTER TABLE `users_chat`
  ADD PRIMARY KEY (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
