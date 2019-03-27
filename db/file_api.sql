-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 27 2019 г., 11:07
-- Версия сервера: 5.6.38
-- Версия PHP: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `file_api`
--

-- --------------------------------------------------------

--
-- Структура таблицы `files_users`
--

CREATE TABLE `files_users` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `file_name` text NOT NULL,
  `for_short_url` text NOT NULL,
  `type` char(3) NOT NULL,
  `count_down` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `token_users`
--

CREATE TABLE `token_users` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `secret` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `token_users`
--

INSERT INTO `token_users` (`id`, `token`, `secret`) VALUES
(5, 'TVRVMU16WTNNak0xTkMweE1qY3VNQzR3TGpFPS1mNTI4NzY0ZDYyNGRiMTI5YjMyYzIxZmJjYTBjYjhkNg==', 'f528764d624db129b32c21fbca0cb8d6');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `files_users`
--
ALTER TABLE `files_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `token_users`
--
ALTER TABLE `token_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `files_users`
--
ALTER TABLE `files_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `token_users`
--
ALTER TABLE `token_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
