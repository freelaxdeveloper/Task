-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 07 2017 г., 23:51
-- Версия сервера: 5.7.19-0ubuntu0.16.04.1
-- Версия PHP: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `task`
--

-- --------------------------------------------------------

--
-- Структура таблицы `distribution`
--

CREATE TABLE `distribution` (
  `id` int(11) NOT NULL,
  `xpath` varchar(32) NOT NULL,
  `link` varchar(64) NOT NULL,
  `title` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `distribution_install`
--

CREATE TABLE `distribution_install` (
  `id` int(11) NOT NULL,
  `id_distribution` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `count` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `distribution_percent`
--

CREATE TABLE `distribution_percent` (
  `id` int(11) NOT NULL,
  `percent` int(11) NOT NULL,
  `id_distribution` int(11) NOT NULL,
  `hours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `distribution_settings`
--

CREATE TABLE `distribution_settings` (
  `id` int(11) NOT NULL,
  `id_distribution` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `time_create` int(11) NOT NULL,
  `color` varchar(16) NOT NULL,
  `id_user` int(11) NOT NULL,
  `set_management` enum('1','2') NOT NULL DEFAULT '2' COMMENT '1 - проект ведут все, 2 - только автор'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Проекты';

-- --------------------------------------------------------

--
-- Структура таблицы `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `message` varchar(1024) NOT NULL,
  `time_create` int(11) NOT NULL,
  `id_project` int(11) NOT NULL,
  `status` enum('1','2') NOT NULL DEFAULT '1' COMMENT 'не выполнено/выполнено',
  `deadlines` bigint(20) NOT NULL COMMENT 'сроки выполнения',
  `importance` int(11) NOT NULL DEFAULT '0' COMMENT 'важность выполнения',
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Список заданий';

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `time_create` int(11) NOT NULL,
  `url_token` varchar(64) DEFAULT NULL,
  `token_time_update` int(11) NOT NULL,
  `group` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT 'Пользователь/Модератор/Администратор',
  `token_ip` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Пользователи';

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `distribution`
--
ALTER TABLE `distribution`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `distribution_install`
--
ALTER TABLE `distribution_install`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_distribution` (`id_distribution`),
  ADD KEY `count` (`count`),
  ADD KEY `date` (`date`);

--
-- Индексы таблицы `distribution_percent`
--
ALTER TABLE `distribution_percent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `percent` (`percent`),
  ADD KEY `id_distribution` (`id_distribution`),
  ADD KEY `hours` (`hours`);

--
-- Индексы таблицы `distribution_settings`
--
ALTER TABLE `distribution_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_distribution` (`id_distribution`),
  ADD KEY `hours` (`hours`),
  ADD KEY `count` (`count`);

--
-- Индексы таблицы `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_project` (`id_project`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_project_2` (`id_project`),
  ADD KEY `status` (`status`),
  ADD KEY `deadlines` (`deadlines`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `distribution`
--
ALTER TABLE `distribution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `distribution_install`
--
ALTER TABLE `distribution_install`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `distribution_percent`
--
ALTER TABLE `distribution_percent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT для таблицы `distribution_settings`
--
ALTER TABLE `distribution_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT для таблицы `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT для таблицы `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
