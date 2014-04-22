DROP TABLE IF EXISTS `faq_section`;
CREATE TABLE IF NOT EXISTS `faq_section` (
  `faq_section_id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_section_name` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`faq_section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `faq_section`
--

INSERT INTO `faq_section` (`faq_section_id`, `faq_section_name`, `is_default`) VALUES
(1, 'Общие вопросы', 1);

ALTER TABLE  `faq` ADD  `faq_section_id` INT( 11 ) NOT NULL DEFAULT  '1';