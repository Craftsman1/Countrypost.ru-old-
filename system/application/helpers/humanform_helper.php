<?
	/**
	 *	Функция возвращает слово с правильным грамматическим окончанием,
	 *	в зависимости от переданного количества
	 *
	 * 	@param int $n
	 * 	@param string $oneending		-	форма слова в единственном числе
	 * 	@param string $twoending		-	форма слова для количества, кратного двум
	 * 	@param string $moreending		-	форма слова для множественного числа
	 * 	@param string $lang = "RU_ru"	-	Locale
	 * 
	 */
    function humanForm($n, $oneending, $twoending, $moreending = "", $lang = "RU_ru"){
        switch ($lang) {
        	case 'RU_ru':
        		$c = ($n % 10 == 1 && $n % 100 != 11 ? 0 : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 or $n % 100 >= 20) ? 1 : 2));
        	break;
        	
        	default:
        	break;
        }
        
        switch ($c){
            case 0: default:
                return $oneending;
            break;
            
            case 1:
                return $twoending;
            break;
            
            case 2:
                return $moreending;
            break;
        }
    }	
	
	function shortenText($text, $id = 0, $handler = 'showOdetail')
	{
		$trimmed_text = mb_strimwidth($text, 0 , 15, '...', 'UTF-8');
		
		if ($text != $trimmed_text)
		{
			$result = "<a href='#' onclick='return $handler($id);' title='Подробнее...'>$trimmed_text</a>";
	
			return $result;
		}
		
		return $text;
	}

	function shortenCountryName($text, $style = '')
	{
		$trimmed_text = mb_strimwidth($text, 0 , 10, '...', 'UTF-8');

		if ($text != $trimmed_text)
		{
			$result = "<b style='$style' title='$text'>$trimmed_text</b>";
		}
		else
		{
			$result = "<b style='$style'>$text</b>";
		}

		return $result;
	}

	/*
	// режем текст на слова по 15 символов (последние 3 символа будут '...'):
	// длинные слова обрезаем, оборачиваем ссылкой
	function shortenText($text, $id = 0)
	{
		//return $text;
		if (strlen($text) > 15)
		{
			$words = preg_split('/[\s,]+/', $text);
		
			foreach ($words as $word)
			{
				if (strlen($word) > 15)
				{
					$temp = split($word, $text);
					$result = '<a href="#" onclick="return showOdetail('.$id.');" title="Подробнее...">'.($temp[0] != '' ? $temp[0].' ' : '').substr($word, 0, 12).'...</a>';
					return $result;
				}
			}
		}
		
		return $text;
	}
*/
	// преобразуем дату к формату 16.08.2011 в 15:25
	function formatCommentDate($date)
	{
		if (!isset($date) || !$date || $date == '0000-00-00 00:00:00') return '';
		return substr($date,8,2).'.'.substr($date,5,2).'.'.substr($date,0,4).' в '.substr($date,11,5);
	}
?>