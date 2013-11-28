-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 28 2013 г., 09:20
-- Версия сервера: 5.5.34-0ubuntu0.13.04.1
-- Версия PHP: 5.4.9-4ubuntu2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `countrypost`
--

-- --------------------------------------------------------

--
-- Структура таблицы `money_transfers`
--

CREATE TABLE IF NOT EXISTS `money_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `percent` double NOT NULL,
  `type` tinyint(1) NOT NULL,
  `currency` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `money_transfers`
--

INSERT INTO `money_transfers` (`id`, `name`, `percent`, `type`, `currency`) VALUES
(1, 'Перевод из России в Китай (обычный)\n', 1.5, 0, 'RUB'),
(2, 'Перевод из России в Китай (срочный) ', 2, 1, 'RUB'),
(3, 'Перевод из России в Китай (срочный) и пополнение Alipay', 2, 2, 'RUB'),
(4, 'Перевод из Украины в Китай (обычный) ', 1.5, 0, 'UAH'),
(5, 'Перевод из Украины в Китай (срочный)', 2, 1, 'UAH'),
(6, 'Перевод из Украины в Китай (срочный) и пополнение Alipay', 2, 2, 'UAH'),
(7, 'Перевод из Китая в Россию (обычный)', 0.5, 0, 'CNY'),
(8, 'Перевод из Китая в Россию (срочный)', 1.5, 1, 'CNY'),
(9, 'Перевод из Китая в Украину(обычный)', 0.5, 0, 'CNY'),
(10, 'Перевод из Китая в Украину(срочный)', 1.5, 1, 'CNY');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
